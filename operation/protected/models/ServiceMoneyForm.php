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
	public $service_money;
	public $score_num;
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
            'service_code'=>Yii::t('rank','money code'),
            'service_month'=>Yii::t('rank','month'),
            'service_year'=>Yii::t('rank','year'),
            'service_money'=>Yii::t('rank','service money'),
            'score_num'=>Yii::t('rank','service score'),
            'remark'=>Yii::t('rank','history'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,employee_id,service_code,remark,service_month,service_year,service_money,score_num','safe'),
			array('employee_id,service_money','required'),
            array('service_year,service_month','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('id','validateID','on'=>array("delete")),
            array('service_money','validateMoney'),
            array('id','validateRemark','on'=>array("edit")),
		);
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
        $id = $this->id;
        $row = Yii::app()->db->createCommand()->select("service_code,id")->from("opr_service_money")
            ->where("employee_id=:id and service_year='{$this->service_year}' and service_month='{$this->service_month}' and id!='{$id}'",array(":id"=>$this->employee_id))->queryRow();
	    if($row){
            $this->addError($attribute, "该员工在{$this->service_year}年{$this->service_month}月已存在服务金额，不允许重复添加(金额编号:{$row["service_code"]})");
            return false;
        }else{
            $date = date("Ym",strtotime(" - 2 months"));
            if($date>=date("Ym",strtotime($this->service_year."/".$this->service_month."/01"))){
                $this->addError($attribute, "不允许修改两个月以前的数据");
            }else{
                $this->service_money = round($this->service_money,2);
                $this->score_num = self::computeScore($this->service_money);
            }
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
                    $row["service_money"] = floatval($row["service_money"]);
                    $this->remark.=empty($this->remark)?"":"\r\n";
                    $this->remark.="用户（{$uid}）修改了金额：{$row["service_money"]} -> {$this->service_money} | 时间:".date("Y/m/d H:i:s");
                }
                $this->service_money = round($this->service_money,2);
                $this->score_num = self::computeScore($this->service_money);
            }
        }else{
            $this->addError($attribute, "数据异常，请刷新重试");
            return false;
        }
    }

    //根据服务金额计算得分
    public static function computeScore($money){
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
			$this->remark = $row['remark'];
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
						employee_id, service_date, service_year, service_month, service_money, score_num, lcu, lcd) values (
						:employee_id, :service_date, :service_year, :service_month, :service_money, :score_num, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update opr_service_money set 
					service_money = :service_money,
					score_num = :score_num,
					remark = :remark,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':employee_id')!==false)
			$command->bindParam(':employee_id',$this->employee_id,PDO::PARAM_INT);
		if (strpos($sql,':service_year')!==false)
			$command->bindParam(':service_year',$this->service_year,PDO::PARAM_INT);
		if (strpos($sql,':service_month')!==false)
			$command->bindParam(':service_month',$this->service_month,PDO::PARAM_INT);
		if (strpos($sql,':service_money')!==false)
			$command->bindParam(':service_money',$this->service_money,PDO::PARAM_INT);
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

		return true;
	}

	protected function resetServiceCode(){
        $str="S";
        $this->service_code = $str.(100000+$this->id);
    }
}