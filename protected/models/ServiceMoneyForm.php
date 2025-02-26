<?php

class ServiceMoneyForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $city;
	public $service_code;
	public $service_month;
	public $service_date;
	public $service_year;
	public $service_money;//服务金额
	public $score_num;//服务金额得分

	public $night_money=0;//夜单金额
	public $night_score=0;//夜单得分

	public $create_money;//创新服务金额
	public $create_score;//创新服务得分

	public $update_u=1;//u系统自动同步 1：同步 0：不同步
    public $companyRank=false;//是否刷新排行榜的值
	public $remark;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'code'=>Yii::t('rank','employee code'),
            'name'=>Yii::t('rank','employee name'),
            'employee_id'=>Yii::t('rank','employee name'),
            'city_name'=>Yii::t('rank','city'),
            'service_code'=>Yii::t('rank','syn code'),
            'service_month'=>Yii::t('rank','month'),
            'service_year'=>Yii::t('rank','year'),
            'service_money'=>Yii::t('rank','service money'),
            'score_num'=>Yii::t('rank','service score'),
            'night_money'=>Yii::t('rank','night money'),
            'night_score'=>Yii::t('rank','night score'),
            'create_money'=>Yii::t('rank','create money'),
            'create_score'=>Yii::t('rank','create score'),
            'remark'=>Yii::t('rank','history'),
            'update_u'=>Yii::t('rank','Update For U'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,employee_id,update_u,service_code,remark,service_month,service_year,service_money,score_num,night_money,night_score,create_money,create_score','safe'),
			array('employee_id,service_money,night_money,create_money,update_u','required'),
            array('service_year,service_month','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('id','validateID','on'=>array("delete")),
            array('service_money','validateMoney'),
            array('service_year','validateYear'),
            array('id','validateRemark','on'=>array("edit")),
		);
	}

	public static function updateLongDate($year,$month){
	    $arr = array("status"=>false,"message"=>"");
	    $oneDate = date("Y/m/01");
        $day = date("j");
        $date = date("Ym",strtotime("{$oneDate} - 1 months"));
        $longDate = date("Ym",strtotime("{$oneDate} - 2 months"));
        $serviceDate = date("Ym",strtotime($year."/".$month."/01"));
        $day-= $month==9||$month==4?5:0;//5月份或10月份延遲五天
        if($day<5){
            if($longDate>=$serviceDate){
                $arr = array("status"=>true,"message"=>"不允许修改兩个月以前的数据");
            }
        }else{
            if($date>=$serviceDate){
                $arr = array("status"=>true,"message"=>"不允许修改上个月以前的数据");
            }
        }
        return $arr;
    }

    public function validateYear($attribute, $params) {
	    $arr = ServiceMoneyForm::updateLongDate($this->service_year,$this->service_month);
        if($arr["status"]===true){
            $this->addError($attribute, $arr["message"]);
        }
    }

    public function validateID($attribute, $params) {
        $date = date("Ym",strtotime(" - 1 months"));
        $row = Yii::app()->db->createCommand()->select("service_year,service_month")->from("opr_service_money")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            if($date>=date("Ym",strtotime($row["service_year"]."/".$row["service_month"]."/01"))){
                $this->addError($attribute, "只允许删除本月服务，请与管理员联系");
                return false;
            }
        }else{
            $this->addError($attribute, "数据异常，请刷新重试");
            return false;
        }
    }

    public function validateMoney($attribute, $params) {
	    if($this->service_money<0){
            $this->addError($attribute, "服务金额不能为负数");
            return false;
        }
	    if($this->night_money<0){
            $this->addError($attribute, "夜单金额不能为负数");
            return false;
        }
	    if($this->create_money<0){
            $this->addError($attribute, "创新服务金额不能为负数");
            return false;
        }
        $id = $this->id;
        $row = Yii::app()->db->createCommand()->select("service_code,id")->from("opr_service_money")
            ->where("employee_id=:id and service_year='{$this->service_year}' and service_month='{$this->service_month}' and id!='{$id}'",array(":id"=>$this->employee_id))->queryRow();
	    if($row){
            $this->addError($attribute, "该员工在{$this->service_year}年{$this->service_month}月已存在服务金额，不允许重复添加(同步编号:{$row["service_code"]})");
            return false;
        }else{
            $this->service_money = round($this->service_money,2);
            $this->night_money = round($this->night_money,2);
            $this->create_money = round($this->create_money,2);
            $this->score_num = self::computeScore($this->service_money,"score_num");
            $this->night_score = self::computeScore($this->night_money,"night_score");
            $this->create_score = self::computeScore($this->create_money,"create_score");
        }
    }

    public function validateRemark($attribute, $params) {
	    $errorMsg = $this->getError("service_money");
        $uid = Yii::app()->user->id;
        $id = $this->id;
        $row = Yii::app()->db->createCommand()->select("*")->from("opr_service_money")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            if(empty($errorMsg)){
                $this->remark = $row["remark"];
                if($this->service_money!=$row["service_money"]){
                    $this->update_u = 0;//手动修改后默认不同步U系统数据
                    $this->companyRank = true;//金额有变动，需要刷新排行榜
                    $row["service_money"] = floatval($row["service_money"]);
                    $this->remark.=empty($this->remark)?"":"\r\n";
                    $this->remark.="用户（{$uid}）修改了服务金额：{$row["service_money"]} -> {$this->service_money} | 时间:".date("Y/m/d H:i:s");
                }
                if($this->night_money!=$row["night_money"]){
                    $this->update_u = 0;//手动修改后默认不同步U系统数据
                    $this->companyRank = true;//金额有变动，需要刷新排行榜
                    $row["night_money"] = floatval($row["night_money"]);
                    $this->remark.=empty($this->remark)?"":"\r\n";
                    $this->remark.="用户（{$uid}）修改了夜单金额：{$row["night_money"]} -> {$this->night_money} | 时间:".date("Y/m/d H:i:s");
                }
                if($this->create_money!=$row["create_money"]){
                    $this->update_u = 0;//手动修改后默认不同步U系统数据
                    $this->companyRank = true;//金额有变动，需要刷新排行榜
                    $row["create_money"] = floatval($row["create_money"]);
                    $this->remark.=empty($this->remark)?"":"\r\n";
                    $this->remark.="用户（{$uid}）修改了创新服务金额：{$row["create_money"]} -> {$this->create_money} | 时间:".date("Y/m/d H:i:s");
                }
            }
        }else{
            $this->addError($attribute, "数据异常，请刷新重试");
            return false;
        }
    }

    //根据类型计算得分
    public static function computeScore($money,$str="score_num"){
	    switch ($str){
            case "score_num"://服务金额得分
                return self::computeScoreMoney($money);
            case "night_score"://夜单金额得分
                return self::computeNightMoney($money);
            case "create_score"://创新服务金额得分
                return self::computeCreateMoney($money);
        }
        return $money;
    }
    //根据夜单金额计算得分
    public static function computeNightMoney($money){
        if($money<2000){
            $score = $money/200;
        }else{
            $score = 2000/200 + ($money-2000)/500;
            $score = round($score,2);
        }
        return $score;
    }
    //根据创新服务金额计算得分
    public static function computeCreateMoney($money){
        if($money<1000){
            $score = $money/100;
        }else{
            $score = 1000/100 + ($money-1000)/400;
            $score = round($score,2);
        }
        return $score;
    }
    //根据服务金额计算得分
    public static function computeScoreMoney($money){
        $arr = array(
            array('money'=>20000,'rate'=>0.1),
            array('money'=>25000,'rate'=>0.08),
            array('money'=>30000,'rate'=>0.07),
            array('money'=>40000,'rate'=>0.05),
            array('money'=>50000,'rate'=>0.03),
            array('money'=>70000,'rate'=>0.02),
        );
        $endRate = 0.01;
        $scoreNum = 0;//分数
        foreach ($arr as $key=>$item){
            $lastMoney = $key==0?0:$arr[$key-1]["money"];
            $otherMoney = $money-$item["money"];
            if($otherMoney>0){
                $scoreNum+=($item["money"]-$lastMoney)*$item["rate"];
                if($key==count($arr)-1){//最后一次循环
                    $scoreNum+=$otherMoney*$endRate;
                }
            }else{
                $scoreNum+=($money-$lastMoney)*$item["rate"];
                break;//跳出循环
            }
        }
        $scoreNum = round($scoreNum,2);
        return $scoreNum;
    }

    public function curlJobFee($year,$month,$bool=true){
        $year = is_numeric($year)?$year:date("Y");
        $month = is_numeric($month)?$month:date("n");
        $jobFeeList =self::getUServiceMoney($year,$month);
/*  2023/04/21（棄用）
        $jobFeeList =JobFee::getData($year,$month);
        $jobFeeList ='{
            "code":1,
            "data":[
                {
                "code":"400002",
                "name":"修改名字",
                "sum_money":4444,
                "night_money":333,
                "type_money_data":[
                    {"type_id":1,"type_name":"甲醛","type_money":123},
                    {"type_id":2,"type_name":"隔油池清洁服务","type_money":456},
                    {"type_id":5,"type_name":"服务","type_money":666}
                ]},
                {
                "code":"400003",
                "name":"测试3",
                "sum_money":6666,
                "night_money":555,
                "type_money_data":[
                    {"type_id":1,"type_name":"甲醛","type_money":152},
                    {"type_id":2,"type_name":"隔油池清洁服务","type_money":485},
                    {"type_id":3,"type_name":"清洁服务","type_money":666}
                ]}
            ],"msg":"消息"}';
        $jobFeeList = json_decode($jobFeeList,true);
*/
        if(!empty($jobFeeList)&&$bool){
            $staffList = self::getEmployeeCodeList();
            foreach ($jobFeeList as $row){
                if(key_exists($row["code"],$staffList)){
                    $this->saveCurlData($year,$month,$staffList[$row["code"]],$row);
                }
            }
        }
        return $jobFeeList;
    }

    public static function getMMRANKCity(){
        $cityList = array();
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("code")
            ->from("security{$suffix}.sec_city_info")
            ->where("field_id='MMRANK' and field_value=1")
            ->queryAll();
        if($rows){
            foreach ($rows as $row){
                $cityList[] = $row["code"];
            }
        }
        $cityStr = "'".implode("','",$cityList)."'";
        return $cityStr;
    }

    public static function getUServiceMoney($year,$month,$city=''){
        //$cityList = empty($city)?self::getMMRANKCity():$city; //由于派单系统是and查询，所以查询所有城市
        //由于2024年1月29日使用了新的U系统，所以使用新代码
        $list = SystemU::getTechnicianSNC($year,$month);
        return isset($list["data"])?$list["data"]:array();
        //由于2024年1月29日使用了新的U系统，所以不使用以下代码
        $whereDate = date("Y/m/01",strtotime("{$year}/{$month}/01"));
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.OT,a.JobTime,a.AddFirst,a.Fee,a.TermCount,a.Staff01,a.Staff02,a.Staff03,f.ServiceName")
            ->from("service{$suffix}.joborder a")
            ->leftJoin("service{$suffix}.service f","a.ServiceType = f.ServiceType")
            ->where("a.Status=3 and date_format(a.JobDate,'%Y/%m/01') = '{$whereDate}'")
            ->queryAll();
        $list = array();
        $createList = array("甲醛","隔油池清洗服务","雾化消毒","厨房油烟清洁服务",
            "RA空气净化-延长维保","RA空气净化-随意派","RA空气净化-轻松派");
        $staffStrList = array("Staff01","Staff02","Staff03");
        if($rows){
            foreach ($rows as $row){
                $row["AddFirst"] = is_numeric($row["AddFirst"])?floatval($row["AddFirst"]):0;
                $row["Fee"] = is_numeric($row["Fee"])?floatval($row["Fee"]):0;
                $row["TermCount"] = is_numeric($row["TermCount"])?floatval($row["TermCount"]):0;
                $money = empty($row["TermCount"])?0:($row["Fee"]+$row["AddFirst"])/$row["TermCount"];
                $staffCount = 1;
                $staffCount+= empty($row["Staff02"])?0:1;
                $staffCount+= empty($row["Staff03"])?0:1;
                $money = $money/$staffCount;//如果多人，需要平分金額
                $money = round($money,2);
                foreach ($staffStrList as $staffStr){
                    $staff = $row[$staffStr];
                    if(!empty($staff)){
                        if(!key_exists($staff,$list)){
                            $list[$staff]=array(
                                "code"=>$staff,//員工編號
                                "sum_money"=>0,//服務金額
                                "night_money"=>0,//夜單金額
                                "create_money"=>0,//創新金額
                            );
                        }
                        $list[$staff]["sum_money"]+=$money;
                        if(!empty($row["OT"])&&!empty($row["JobTime"])&&($row["JobTime"]>="20:00:00"||$row["JobTime"]<="03:00:00")){
                            $list[$staff]["night_money"]+=$money;
                        }
                        if(in_array($row["ServiceName"],$createList)){
                            $list[$staff]["create_money"]+=$money;
                        }
                    }
                }
            }
        }
        return $list;
    }

    private function getDateCreateMoney($row){
        $sum = 0;
        $list = array("甲醛","隔油池清洗服务","雾化消毒","厨房油烟清洁服务",
            "RA空气净化-延长维保","RA空气净化-随意派","RA空气净化-轻松派");
        if(key_exists("type_money_data",$row)&&!empty($row["type_money_data"])){
            foreach ($row["type_money_data"] as $item){
                if(in_array($item["type_name"],$list)){
                    $sum+=floatval($item["type_money"]);
                }
            }
        }
        return $sum;
    }


    private function saveCurlData($year,$month,$staff,$data){
        $data["night_money"] = key_exists("night_money",$data)?$data["night_money"]:0;
        //夜单暂时不同步(2023年4月1号开始生效)
        if(strtotime("{$year}/{$month}/01")<strtotime("2023/04/01")){//由於夜單金額異常，暫時設置成0
            $data["night_money"] = 0;
        }
        $data["create_money"] = key_exists("create_money",$data)?$data["create_money"]:0;
        $row = Yii::app()->db->createCommand()->select("id,update_u")->from("opr_service_money")
            ->where("employee_id=:id and service_year={$year} and service_month={$month}",array(":id"=>$staff["id"]))->queryRow();
        if($row){//存在就覆蓋
            if($row["update_u"]==1){ //设置为允许自动同步
                Yii::app()->db->createCommand()->update("opr_service_money",array(
                    'night_money'=>round($data["night_money"],2),
                    'service_money'=>round($data["sum_money"],2),
                    'create_money'=>round($data["create_money"],2),
                    "score_num"=>self::computeScore($data["sum_money"],"score_num"),
                    "night_score"=>self::computeScore($data["night_money"],"night_score"),
                    "create_score"=>self::computeScore($data["create_money"],"create_score"),
                    "remark"=>"系統自動刷新：".date("Y-m-d H:i:s"),
                    "luu"=>"系統"
                ),"id={$row['id']}");
            }
        }else{//不存在則新增
            Yii::app()->db->createCommand()->insert("opr_service_money",array(
                "employee_id"=>$staff["id"],
                "service_date"=>date("Y-m-d",strtotime("{$year}-{$month}-1")),
                "service_year"=>$year,
                "service_month"=>$month,
                "service_money"=>round($data["sum_money"],2),
                "night_money"=>round($data["night_money"],2),
                "create_money"=>round($data["create_money"],2),
                "score_num"=>self::computeScore($data["sum_money"],"score_num"),
                "night_score"=>self::computeScore($data["night_money"],"night_score"),
                "create_score"=>self::computeScore($data["create_money"],"create_score"),
                "remark"=>"系統自動刷新：".date("Y-m-d H:i:s"),
                "lcu"=>"系統"
            ));
            $this->id = Yii::app()->db->getLastInsertID();
            $this->resetServiceCode();
            Yii::app()->db->createCommand()->update("opr_service_money",array('service_code'=>$this->service_code),"id={$this->id}");
        }
    }

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.*")->from("opr_service_money a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id=b.id")
            ->where("a.id=:id and b.city in ($city_allow)",array(":id"=>$index))->queryRow();
		if ($row) {
			$this->id = $row['id'];
			$this->employee_id = $row['employee_id'];
			$this->service_code = $row['service_code'];
			$this->service_year = $row['service_year'];
			$this->service_month = $row['service_month'];
			$this->service_money = floatval($row['service_money']);
			$this->score_num = floatval($row['score_num']);
			$this->night_money = floatval($row['night_money']);
			$this->night_score = floatval($row['night_score']);
			$this->create_money = floatval($row['create_money']);
			$this->create_score = floatval($row['create_score']);
			$this->remark = $row['remark'];
			$this->update_u = $row['update_u'];
            return true;
		}else{
		    return false;
        }
	}

    public static function getEmployeeList($id=0){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $id = empty($id)?0:$id;
        $list = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name")->from("hr$suffix.hr_employee a")
            ->leftJoin("hr$suffix.hr_dept b","a.position=b.id")
            ->where("(b.review_status=1 and b.review_type=2 and a.city in ($city_allow)) or a.id=:id",array(":id"=>$id))->order("a.name asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["name"]." ({$row["code"]})";
            }
        }
        return $list;
    }

    public static function getEmployeeListNow($id=0){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $id = empty($id)?0:$id;
        $list = array(""=>"");
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name")->from("hr$suffix.hr_employee a")
            ->leftJoin("hr$suffix.hr_dept b","a.position=b.id")
            ->where("(a.staff_status=0 and b.review_status=1 and b.review_type=2 and a.city in ($city_allow)) or a.id=:id",array(":id"=>$id))->order("a.name asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["name"]." ({$row["code"]})";
            }
        }
        return $list;
    }

    public static function getEmployeeCodeList(){
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = self::getMMRANKCity();
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name")->from("hr$suffix.hr_employee a")
            ->leftJoin("hr$suffix.hr_dept b","a.position=b.id")
            ->where("b.review_status=1 and b.review_type=2 and a.city in ({$city_allow})")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["code"]] = array("code"=>$row["code"],"name"=>$row["name"],"id"=>$row["id"]);
            }
        }
        return $list;
    }
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveDataForSql($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
		    var_dump($e);
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update.');
		}
	}

	protected function saveDataForSql(&$connection)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = '';
		switch ($this->scenario) {
			case 'delete':
				$sql = "delete from opr_service_money where id = :id";
				break;
			case 'new':
				$sql = "insert into opr_service_money(
						employee_id, update_u, service_date, service_year, service_month, service_money, night_money, night_score, create_money, create_score, score_num, lcu, lcd) values (
						:employee_id, :update_u, :service_date, :service_year, :service_month, :service_money, :night_money, :night_score, :create_money, :create_score, :score_num, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update opr_service_money set 
					service_money = :service_money,
					score_num = :score_num,
					night_money = :night_money,
					night_score = :night_score,
					create_money = :create_money,
					create_score = :create_score,
					update_u = :update_u,
					remark = :remark,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		$thisTime = strtotime($this->service_year."/".$this->service_month."/1");
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':update_u')!==false)
			$command->bindParam(':update_u',$this->update_u,PDO::PARAM_INT);
		if (strpos($sql,':service_year')!==false)
			$command->bindParam(':service_year',$this->service_year,PDO::PARAM_INT);
		if (strpos($sql,':service_month')!==false)
			$command->bindParam(':service_month',$this->service_month,PDO::PARAM_INT);
		if (strpos($sql,':service_money')!==false)
			$command->bindParam(':service_money',$this->service_money,PDO::PARAM_INT);
        if (strpos($sql,':night_money')!==false){
            $this->night_money = $thisTime>=strtotime("2023/04/01")?$this->night_money:0;
            $command->bindParam(':night_money',$this->night_money,PDO::PARAM_INT);
        }
        if (strpos($sql,':night_score')!==false){
            $this->night_score = $thisTime>=strtotime("2023/04/01")?$this->night_score:0;
            $command->bindParam(':night_score',$this->night_score,PDO::PARAM_INT);
        }
		if (strpos($sql,':create_money')!==false)
			$command->bindParam(':create_money',$this->create_money,PDO::PARAM_INT);
		if (strpos($sql,':create_score')!==false)
			$command->bindParam(':create_score',$this->create_score,PDO::PARAM_INT);
		if (strpos($sql,':score_num')!==false)
			$command->bindParam(':score_num',$this->score_num,PDO::PARAM_INT);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':service_date')!==false){
            $this->service_date = date("Y-m-d",strtotime($this->service_year."-{$this->service_month}-01"));
            $command->bindParam(':service_date',$this->service_date,PDO::PARAM_STR);
        }

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false){
            $date = date("Y-m-d H:i:s");
            $command->bindParam(':lcd',$date,PDO::PARAM_STR);
        }
		$command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->resetServiceCode();
            Yii::app()->db->createCommand()->update("opr_service_money",array('service_code'=>$this->service_code),"id={$this->id}");
        }
        $this->companyRankValue();

		return true;
	}

	protected function resetServiceCode(){
        $str="S";
        $this->service_code = $str.(100000+$this->id);
    }

    protected function companyRankValue(){
        if($this->companyRank&&$this->getScenario()!="delete"){
            $model = new RankingMonthForm();
            $model->resetOneRank($this->service_year,$this->service_month,$this->employee_id);
        }
    }

    public function resetThisNight(){
        $this->companyRank = "start";
        $this->setScenario("edit");
        Yii::app()->db->createCommand()->update("opr_service_money",
            array(
                'night_money'=>0,
                'night_score'=>0,
            ),"id={$this->id}");
        $this->companyRankValue();
    }
}