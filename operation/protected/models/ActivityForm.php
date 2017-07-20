<?php

class ActivityForm extends CFormModel
{
	public $id;
	public $activity_code;
	public $activity_title;
	public $start_time;
	public $end_time;
	public $order_class;
	public $num=1;
    public $luu;
    public $lcu;
    public $lud;
    public $lcd;

    public function init(){
        $this->start_time = date("Y/m/d");
    }

	public function attributeLabels()
	{
		return array(
            'activity_code'=>Yii::t('procurement','Activity Code'),
            'activity_title'=>Yii::t('procurement','Activity Title'),
            'start_time'=>Yii::t('procurement','Start Time'),
            'end_time'=>Yii::t('procurement','End Time'),
            'num'=>Yii::t('procurement','Max Number Restrictions'),
            'order_class'=>Yii::t('procurement','Order Class')
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, start_time, end_time, order_class, num','safe'),
            array('start_time','required'),
            array('end_time','required'),
            array('end_time','validateDate'),
            array('order_class','required',"on"=>"new"),
            array('num','required'),
            array('num','numerical','allowEmpty'=>false,'integerOnly'=>true,'min'=>1),
		);
	}

    public function validateDate($attribute, $params){
        if(strtotime($this->start_time)>strtotime($this->end_time)){
            $message = Yii::t('procurement','The end time cannot be less than the start time');
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

    //刪除驗證
    public function deleteValidate(){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_order")->where('activity_id=:activity_id',array(':activity_id'=>$this->id))->queryAll();
        if($rs){
            return false;
        }else{
            return true;
        }
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

    //自動生成標題和編號
    public function selfTitleAndCode(){
        $day = date("Ymd");
        $codeStr = $this->order_class == "Import"?"PHJK":"PHGN";
        $titleStr = $this->order_class == "Import"?"进口货":"国内货";
        $count = Yii::app()->db->createCommand()->select("count(id)")->from("opr_order_activity")->where(array('like', 'activity_code', "%$codeStr%"))->queryScalar();
        $count = empty($count)?"":$count+1;
        $this->activity_code = $codeStr.$day.$count;
        $this->activity_title = $day.$titleStr."采购订单".$count;
    }

	protected function saveActivity(&$connection) {
        $this->selfTitleAndCode();
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
							start_time = :start_time,
							end_time = :end_time,
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
