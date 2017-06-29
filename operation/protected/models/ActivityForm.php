<?php

class ActivityForm extends CFormModel
{
	public $id;
	public $activity_code;
	public $activity_title;
	public $start_time;
	public $end_time;
	public $order_class;
	public $num;
    public $luu;
    public $lcu;
    public $lud;
    public $lcd;

	public function attributeLabels()
	{
		return array(
            'activity_code'=>Yii::t('procurement','Activity Code'),
            'activity_title'=>Yii::t('procurement','Activity Title'),
            'start_time'=>Yii::t('procurement','Start Time'),
            'end_time'=>Yii::t('procurement','End Time'),
            'num'=>Yii::t('procurement','Number Restrictions'),
            'order_class'=>Yii::t('procurement','Order Class')
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, activity_code, activity_title, start_time, end_time, order_class, num','safe'),
            array('activity_code','required'),
            array('activity_title','required'),
            array('start_time','required'),
            array('end_time','required'),
            array('order_class','required'),
            array('num','required'),
			array('activity_code','validateCode'),
			array('activity_title','validateTitle'),
            array('num','numerical','allowEmpty'=>false,'integerOnly'=>true,'min'=>1),
		);
	}

	public function validateTitle($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_order_activity")
            ->where('activity_title=:activity_title and id!=:id', array(':activity_title'=>$this->activity_title,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Activity Title of already exists');
            $this->addError($attribute,$message);
        }
	}
	public function validateCode($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_order_activity")
            ->where('activity_code=:activity_code and id!=:id', array(':activity_code'=>$this->activity_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Activity Code of already exists');
            $this->addError($attribute,$message);
        }
	}

	//刪除快速訂單
    public function getOrderClassNotFast(){
	    $arr = OrderGoods::getArrGoodsClass();
	    unset($arr["Fast"]);
	    return $arr;
    }

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_activity")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->activity_code = $row['activity_code'];
                $this->activity_title = $row['activity_title'];
                $this->start_time = $row['start_time'];
                $this->end_time = $row['end_time'];
                $this->order_class = $row['order_class'];
                $this->num = $row['num'];
                break;
			}
		}
		return true;
	}
	
	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveActivity($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	//刪除活動下面的所有訂單及訂單內的物品
    public function deleteAllOrderToActivityId($activity_id){
        $rs = Yii::app()->db->createCommand()->select("id")->from("opr_order")->where("activity_id=:activity_id and judge=1",array(":activity_id"=>$activity_id))->queryAll();
        if($rs){
            foreach ($rs as $row){
                Yii::app()->db->createCommand()->delete('opr_order', 'id=:id', array(':id'=>$row['id']));
                Yii::app()->db->createCommand()->delete('opr_order_status', 'order_id=:order_id', array(':order_id'=>$row['id']));
                Yii::app()->db->createCommand()->delete('opr_order_goods', 'order_id=:order_id', array(':order_id'=>$row['id']));
            }
        }
    }

	protected function saveActivity(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from opr_order_activity where id = :id";
                $this->deleteAllOrderToActivityId($this->id);
                break;
            case 'new':
                $sql = "insert into opr_order_activity(
							activity_code, activity_title, start_time, end_time, order_class, num, lcu, lcd
						) values (
							:activity_code, :activity_title, :start_time, :end_time, :order_class, :num, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_order_activity set
							activity_code = :activity_code, 
							activity_title = :activity_title, 
							start_time = :start_time,
							end_time = :end_time,
							order_class = :order_class,
							num = :num,
							luu = :luu,
							lud = :lud
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);

        if (strpos($sql,':activity_code')!==false)
            $command->bindParam(':activity_code',$this->activity_code,PDO::PARAM_STR);
        if (strpos($sql,':activity_title')!==false)
            $command->bindParam(':activity_title',$this->activity_title,PDO::PARAM_STR);
        if (strpos($sql,':start_time')!==false)
            $command->bindParam(':start_time',$this->start_time,PDO::PARAM_STR);
        if (strpos($sql,':end_time')!==false)
            $command->bindParam(':end_time',$this->end_time,PDO::PARAM_STR);
        if (strpos($sql,':order_class')!==false)
            $command->bindParam(':order_class',$this->order_class,PDO::PARAM_STR);
        if (strpos($sql,':num')!==false)
            $command->bindParam(':num',$this->num,PDO::PARAM_INT);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}
