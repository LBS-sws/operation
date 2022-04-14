<?php

class ServiceDeductForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $employee_id;
	public $city;
	public $service_code;
	public $deduct_date;
	public $deduct_type;
	public $service_month;
	public $service_year;
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
            'service_code'=>Yii::t('rank','deduct code'),
            'service_month'=>Yii::t('rank','month'),
            'service_year'=>Yii::t('rank','year'),
            'deduct_type'=>Yii::t('rank','deduct type'),
            'deduct_date'=>Yii::t('rank','deduct date'),
            'remark'=>Yii::t('rank','remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,employee_id,service_code,remark,service_month,service_year,deduct_date,deduct_type','safe'),
			array('employee_id,deduct_date,deduct_type','required'),
            array('id','validateID','on'=>array("delete")),
            array('deduct_date','validateDate'),
		);
	}

    public function validateID($attribute, $params) {
        $date = date("Ym",strtotime(" - 1 months"));
        $row = Yii::app()->db->createCommand()->select("deduct_date")->from("opr_service_deduct")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
        if($row){
            if($date>=date("Ym",strtotime($row["deduct_date"]))){
                $this->addError($attribute, "只允许删除本月服务，请与管理员联系");
                return false;
            }
        }else{
            $this->addError($attribute, "数据异常，请刷新重试");
            return false;
        }
    }

    public function validateDate($attribute, $params) {
        $deduct_date = $this->deduct_date;
        $old_date = date("Ym");
        $row = Yii::app()->db->createCommand()->select("deduct_date")->from("opr_service_deduct")
            ->where("id=:id",array(":id"=>$this->id))->queryRow();
	    if($row){
	        $old_date = date("Ym",strtotime($row["deduct_date"]));
        }
        switch ($this->deduct_type){
            case 1:
                $this->score_num = -1000;
                break;
            case 2:
                $this->score_num = -500;
                break;
            case 3:
                $this->score_num = -300;
                break;
            default:
                $this->score_num = 0;
        }
	    $this->service_year = date("Y",strtotime($deduct_date));
	    $this->service_month = date("n",strtotime($deduct_date));
        $deduct_date = date("Ym",strtotime($deduct_date));
        $date = date("Ym",strtotime(" - 2 months"));
        if($date>=$deduct_date||$date>=$old_date){
            $this->addError($attribute, "不允许修改两个月以前的数据");
        }
    }

	public function retrieveData($index)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $row = Yii::app()->db->createCommand()->select("a.*")->from("opr_service_deduct a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id=b.id")
            ->where("a.id=:id and b.city in ($city_allow)",array(":id"=>$index))->queryRow();
		if ($row) {
			$this->id = $row['id'];
			$this->employee_id = $row['employee_id'];
			$this->service_code = $row['service_code'];
			$this->service_year = $row['service_year'];
			$this->service_month = $row['service_month'];
			$this->deduct_date = CGeneral::toDate($row['deduct_date']);
			$this->deduct_type = $row['deduct_type'];
			$this->remark = $row['remark'];
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
				$sql = "delete from opr_service_deduct where id = :id";
				break;
			case 'new':
				$sql = "insert into opr_service_deduct(
						employee_id, service_year, service_month, deduct_date, deduct_type, remark, score_num, lcu, lcd) values (
						:employee_id, :service_year, :service_month, :deduct_date, :deduct_type, :remark, :score_num, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update opr_service_deduct set 
					deduct_type = :deduct_type,
					deduct_date = :deduct_date,
					service_year = :service_year,
					service_month = :service_month,
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
		if (strpos($sql,':deduct_type')!==false)
			$command->bindParam(':deduct_type',$this->deduct_type,PDO::PARAM_INT);
		if (strpos($sql,':score_num')!==false)
			$command->bindParam(':score_num',$this->score_num,PDO::PARAM_INT);
		if (strpos($sql,':deduct_date')!==false)
			$command->bindParam(':deduct_date',$this->deduct_date,PDO::PARAM_STR);
		if (strpos($sql,':remark')!==false)
			$command->bindParam(':remark',$this->remark,PDO::PARAM_STR);

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
            Yii::app()->db->createCommand()->update("opr_service_deduct",array('service_code'=>$this->service_code),"id={$this->id}");
        }

		return true;
	}

	protected function resetServiceCode(){
        $str="D";
        $this->service_code = $str.(100000+$this->id);
    }
}