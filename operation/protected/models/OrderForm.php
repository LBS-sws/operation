<?php

class OrderForm extends CFormModel
{
	public $id;
	public $goods_id;
	public $goods_name;
	public $order_num;
	public $confirm_num;
	public $order_user;
	public $technician;
    public $status;
    public $remark;
	public $luu;
	public $lcu;
	public $statusList;

	public function attributeLabels()
	{
		return array(
            'goods_id'=>Yii::t('procurement','Goods Name'),
            'order_num'=>Yii::t('procurement','Order Number'),
            'order_user'=>Yii::t('procurement','Order User'),
            'technician'=>Yii::t('procurement','Technician'),
            'status'=>Yii::t('procurement','Order Status'),
            'remark'=>Yii::t('procurement','Remark'),
            'confirm_num'=>Yii::t('procurement','Confirm Number'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_id, order_num, order_user, technician, status, remark, confirm_num, luu, lcu','safe'),
            array('goods_id','required'),
            array('order_num','required'),
            array('technician','validateTe'),
            array('order_num','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('order_num','in','range'=>range(0,600)),
		);
	}

	//驗證技術員是否填寫
    public function validateTe($attribute, $params){
        if($this->technician == "" && $this->status == "sent"){
            $message = Yii::t('procurement','The order send must specify the technician');
            $this->addError($attribute,$message);
        }
    }

    //根據訂單id查訂單所有狀態
    public function getStatusListToId($order_id){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_order_status")->where('order_id=:order_id',array(':order_id'=>$order_id))->queryAll();
        return $rs;
    }

    //根據當前狀態提供選擇
    public function getNowStatusList(){
        $arr =array();
        $rs = Yii::app()->db->createCommand()->select("status")->from("opr_order")->where('id=:id',array(':id'=>$this->id))->queryAll();
        $status=$rs[0]["status"];
        switch ($status){
            case "pending"://等待
                $arr = array(
                    "pending"=>Yii::t("procurement","pending"),
                    "sent"=>Yii::t("procurement","sent"),
                    "cancelled"=>Yii::t("procurement","cancelled")
                );
                break;
            case "sent"://發送
                $arr = array(
                    "sent"=>Yii::t("procurement","sent"),
                    "approve"=>Yii::t("procurement","approve"),
                    "reject"=>Yii::t("procurement","reject")
                );
                break;
            case "approve"://接受
                $arr = array(
                    "approve"=>Yii::t("procurement","approve")
                );
                break;
            case "reject"://拒絕
                $arr = array(
                    "reject"=>Yii::t("procurement","reject")
                );
                break;
            case "cancelled"://取消
                $arr = array(
                    "cancelled"=>Yii::t("procurement","cancelled")
                );
                break;
        }
        return $arr;
    }

    //根據物品id查物品所有信息
    public function getGoodNameToId($id){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_goods")->where('id=:id',array(':id'=>$id))->queryAll();
        if(count($rs) != 1){
            return array();
        }
        return $rs[0];
    }

    //獲取物品列表
    public function getGoodsList(){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_goods")->queryAll();
        return $rs;
    }
    //獲取物品列表
    public function getGoodsListArr(){
        $arr=array(""=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("opr_goods")->queryAll();
        foreach ($rs as $row){
            $arr[$row["id"]] = $row["name"];
        }
        return $arr;
    }

    //獲取所有用戶列表
    public function getUserListArr(){
        $arr=array(""=>"");
        $suffix = Yii::app()->params['envSuffix'];
        $table = "security".$suffix.".sec_user";
        $rs = Yii::app()->db->createCommand()->select("username")->from($table)->queryAll();
        foreach ($rs as $row){
            $arr[$row["username"]] = $row["username"];
        }
        return $arr;
    }

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("id, goods_id, order_num, order_user, technician, status, remark, confirm_num")
            ->from("opr_order")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
			    $goodsList =$this->getGoodNameToId($row['goods_id']);
			    $num = 0;
                $this->id = $row['id'];
                $this->goods_id = $row['goods_id'];
                $this->order_num = $row['order_num'];
                $this->order_user = $row['order_user'];
                $this->confirm_num = $row['confirm_num'];
                $this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->remark = $row['remark'];
                $this->statusList = $this->getStatusListToId($row['id']);
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
			$this->saveGoods($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from opr_order where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_order(
							goods_id, order_num, order_user, status, luu, lcu
						) values (
							:goods_id, :order_num, :order_user, :status, :luu, :lcu
						)";
                break;
            case 'edit':
                $sql = "update opr_order set
							goods_id = :goods_id, 
							order_num = :order_num, 
							technician = :technician,
							confirm_num = :confirm_num,
							remark = :remark,
							status = :status
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $order_username = Yii::app()->user->name;
        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':goods_id')!==false)
            $command->bindParam(':goods_id',$this->goods_id,PDO::PARAM_INT);
        if (strpos($sql,':order_user')!==false)
            $command->bindParam(':order_user',$order_username,PDO::PARAM_STR);
        if (strpos($sql,':order_num')!==false)
            $command->bindParam(':order_num',$this->order_num,PDO::PARAM_INT);
        if (strpos($sql,':status')!==false){
            if($this->scenario == "new"){
                $this->status = "pending";
            }
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        }

        if (strpos($sql,':technician')!==false)
            $command->bindParam(':technician',$this->technician,PDO::PARAM_STR);
        if (strpos($sql,':confirm_num')!==false)
            $command->bindParam(':confirm_num',$this->confirm_num,PDO::PARAM_STR);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);

        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
        if ($this->scenario=='delete'){
            $this->id = Yii::app()->db->createCommand()->delete('opr_order_status', 'order_id=:order_id', array(':order_id'=>$this->id));
        }else{
            Yii::app()->db->createCommand()->insert('opr_order_status', array(
                'order_id'=>$this->id,
                'status'=>$this->status,
                'lcu'=>$order_username,
                'time'=>date('Y-m-d H:i:s'),
            ));
        }
		return true;
	}
}
