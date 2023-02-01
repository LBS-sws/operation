<?php

class ServiceNewForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $city;
	public $service_code;
	public $service_month;
	public $service_date;
	public $service_year;
	public $service_num;
	public $score_num;
	public $remark;
	public $user_remark;

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
            'service_code'=>Yii::t('rank','number code'),
            'service_month'=>Yii::t('rank','month'),
            'service_year'=>Yii::t('rank','year'),
            'service_num'=>Yii::t('rank','service number'),
            'remark'=>Yii::t('rank','history'),
            'user_remark'=>Yii::t('rank','remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,employee_id,service_code,user_remark,remark,service_month,service_year,service_num','safe'),
			array('employee_id,service_num','required'),
            array('service_year,service_month','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('id','validateID','on'=>array("delete")),
            array('service_num','validateMoney'),
            array('id','validateRemark','on'=>array("edit")),
		);
	}

    public function validateID($attribute, $params) {
        $date = date("Ym",strtotime(" - 1 months"));
        $row = Yii::app()->db->createCommand()->select("service_year,service_month")->from("opr_service_new")
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
	    if($this->service_num<0){
            $this->addError($attribute, "服务单数不能为负数");
            return false;
        }
        $id = $this->id;
        $row = Yii::app()->db->createCommand()->select("service_code,id")->from("opr_service_new")
            ->where("employee_id=:id and service_year='{$this->service_year}' and service_month='{$this->service_month}' and id!='{$id}'",array(":id"=>$this->employee_id))->queryRow();
	    if($row){
            $this->addError($attribute, "该员工在{$this->service_year}年{$this->service_month}月已存在服务单数，不允许重复添加(单数编号:{$row["service_code"]})");
            return false;
        }else{
            $date = date("Ym",strtotime(" - 2 months"));
            if($date>=date("Ym",strtotime($this->service_year."/".$this->service_month."/01"))){
                $this->addError($attribute, "不允许修改两个月以前的数据");
            }
        }
        $this->score_num = $this->service_num*500;
    }

    public function validateRemark($attribute, $params) {
        $errorMsg = $this->getError("service_num");
        $uid = Yii::app()->user->id;
        $id = $this->id;
        $row = Yii::app()->db->createCommand()->select("*")->from("opr_service_new")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        if($row){
            if(empty($errorMsg)){
                $this->remark = $row["remark"];
                if($this->service_num!=$row["service_num"]){
                    $this->remark.=empty($this->remark)?"":"\r\n";
                    $this->remark.="用户（{$uid}）修改了单数：{$row["service_num"]} -> {$this->service_num} | 时间:".date("Y/m/d H:i:s");
                }
            }
        }else{
            $this->addError($attribute, "数据异常，请刷新重试");
            return false;
        }
    }

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.*")->from("opr_service_new a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id=b.id")
            ->where("a.id=:id and b.city in ($city_allow)",array(":id"=>$index))->queryRow();
		if ($row) {
			$this->id = $row['id'];
			$this->employee_id = $row['employee_id'];
			$this->service_code = $row['service_code'];
			$this->service_year = $row['service_year'];
			$this->service_month = $row['service_month'];
			$this->service_num = $row['service_num'];
			$this->remark = $row['remark'];
			$this->user_remark = $row['user_remark'];
			$this->score_num = $row['score_num'];
            return true;
		}else{
		    return false;
        }
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
				$sql = "delete from opr_service_new where id = :id";
				break;
			case 'new':
				$sql = "insert into opr_service_new(
						employee_id, service_date, service_year, service_month, service_num, score_num, user_remark, lcu, lcd) values (
						:employee_id, :service_date, :service_year, :service_month, :service_num, :score_num, :user_remark, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update opr_service_new set 
					service_num = :service_num,
					score_num = :score_num,
					remark = :remark,
					user_remark = :user_remark,
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
		if (strpos($sql,':service_num')!==false)
			$command->bindParam(':service_num',$this->service_num,PDO::PARAM_INT);
		if (strpos($sql,':score_num')!==false)
			$command->bindParam(':score_num',$this->score_num,PDO::PARAM_INT);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
		if (strpos($sql,':user_remark')!==false)
			$command->bindParam(':user_remark',$this->user_remark,PDO::PARAM_STR);
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
            Yii::app()->db->createCommand()->update("opr_service_new",array('service_code'=>$this->service_code),"id={$this->id}");
        }

		return true;
	}

	protected function resetServiceCode(){
        $str="N";
        $this->service_code = $str.(100000+$this->id);
    }
}