<?php

class DeliveryForm extends CFormModel
{
	public $id;
	public $order_user;
	//public $technician;
    public $status;
    public $remark;
	public $luu;
	public $lcu;
	public $lcd;
	public $statusList;
	public $order_code;
	public $goods_list;

    public function attributeLabels()
	{
		return array(
            'order_code'=>Yii::t('procurement','Order Code'),
            'goods_list'=>Yii::t('procurement','Goods List'),
            'order_user'=>Yii::t('procurement','Order User'),
            //'technician'=>Yii::t('procurement','Technician'),
            'status'=>Yii::t('procurement','Order Status'),
            'remark'=>Yii::t('procurement','Remark'),
            'lcu'=>Yii::t('procurement','Apply for user'),
            'lcd'=>Yii::t('procurement','Apply for time'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, order_code, order_user, order_class, activity_id, technician, status, remark, luu, lcu, lud, lcd','safe'),
            array('goods_list','required','on'=>array('audit','edit','reject')),
            array('goods_list','validateGoods','on'=>array('audit','edit')),
            array('remark','required','on'=>'reject'),
            //array('order_num','numerical','allowEmpty'=>true,'integerOnly'=>true),
            //array('order_num','in','range'=>range(0,600)),
		);
	}

	//驗證訂單內的物品
    public function validateGoods($attribute, $params){
	    $goods_list = $this->goods_list;
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
            return false;
        }
        foreach ($goods_list as $key =>$goods){
            if(empty($goods["goods_id"]) && empty($goods["confirm_num"])){
                unset($this->goods_list[$key]);
            }else if (empty($goods["confirm_num"]) && $goods["confirm_num"] != 0){
                $message = Yii::t('procurement','Actual Number cannot be empty');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["confirm_num"]) || floor($goods["confirm_num"])!=$goods["confirm_num"]){
                $message = Yii::t('procurement','Actual Number can only be numbered');
                $this->addError($attribute,$message);
                return false;
            }else{
                $list = WarehouseForm::getGoodsToGoodsId($goods["goods_id"]);
                if (empty($list)){
                    $message = Yii::t('procurement','Not Font Goods').$goods["goods_id"]."a";
                    $this->addError($attribute,$message);
                    return false;
                }elseif (intval($list["inventory"])<intval($goods["confirm_num"])){
                    $message = $list["name"]."：".Yii::t('procurement','Cannot exceed the quantity of Inventory')."（".$list["inventory"]."）";
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
        }
    }

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("id, order_code, lcd, order_user, technician, status, remark, lcu")
            ->from("opr_order")->where("id=:id AND judge=0 AND city=:city",array(":id"=>$index,":city"=>$city))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->order_code = $row['order_code'];
                $this->goods_list = WarehouseForm::getGoodsListToId($row['id']);
                $this->order_user = $row['order_user'];
                //$this->technician = $row['technician'];
                $this->status = $row['status'];
                $this->remark = "";
                $this->lcu = $row['lcu'];
                $this->lcd = date("Y-m-d",strtotime($row['lcd']));
                $this->statusList = OrderForm::getStatusListToId($row['id']);
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
        $oldOrderStatus = Yii::app()->db->createCommand()->select()->from("opr_order")
            ->where("id=:id",array(":id"=>$this->id))->queryAll();
		$sql = '';
        switch ($this->scenario) {
            case 'edit':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id AND judge=0
						";
                break;
            case 'audit':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id AND judge=0
						";
                break;
            case 'reject':
                $sql = "update opr_order set
							remark = :remark,
							luu = :luu,
							lud = :lud,
							status = :status
						where id = :id AND judge=0
						";
                $this->goods_list = array();
                break;
        }
		if (empty($sql)) return false;

        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;
        $order_username = Yii::app()->user->name;
        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':status')!==false){
            if ($this->scenario == "audit"){
                $this->status = "approve";
            }elseif ($this->scenario == "edit"){
                $this->status = "read";
            }elseif ($this->scenario == "reject"){
                $this->status = "reject";
            }
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        }

        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        $command->execute();

        Yii::app()->db->createCommand()->insert('opr_order_status', array(
            'order_id'=>$this->id,
            'status'=>$this->status,
            'r_remark'=>$this->remark,
            'lcu'=>$order_username,
            'time'=>date('Y-m-d H:i:s'),
        ));

        //物品的添加、修改
        foreach ($this->goods_list as $goods){
            if(!empty($goods["id"])){
                //修改
                Yii::app()->db->createCommand()->update('opr_order_goods', array(
                    'confirm_num'=>$goods["confirm_num"],
                    'remark'=>$goods["remark"],
                    'luu'=>$uid,
                    'lud'=>date('Y-m-d H:i:s'),
                ), 'id=:id', array(':id'=>$goods["id"]));
                if ($this->scenario == "audit"){
                    //減少庫存 inventory
                    $this->reduceInventory($goods["goods_id"],$goods["confirm_num"]);
                }
            }
        }

        //發送郵件
        OrderGoods::sendEmailTwo($oldOrderStatus,$this->status,$this->order_code);
		return true;
	}

    //減少庫存
	public function reduceInventory($goodsId,$num){
        if(empty($goodsId)||!is_numeric($goodsId) || floor($goodsId)!=$goodsId){
            return false;
        }
        if(empty($num)||!is_numeric($num) || floor($num)!=$num){
            return false;
        }
        Yii::app()->db->createCommand("update opr_warehouse set inventory=inventory-$num where id=$goodsId")->execute();
    }

    //退回
    function backward(){
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_order")->where('status = "approve" and id = :id',array(':id'=>$this->id))->queryAll();
        if(count($rows) > 0){
            $uid = Yii::app()->user->id;
            $this->status = "sent";
            Yii::app()->db->createCommand()->update('opr_order', array(
                'status'=>$this->status,
                'remark'=>$this->remark,
                'luu'=>$uid,
                'lud'=>date('Y-m-d H:i:s'),
            ), 'id=:id', array(':id'=>$this->id));

            Yii::app()->db->createCommand()->insert('opr_order_status', array(
                'order_id'=>$this->id,
                'status'=>"backward",
                'r_remark'=>$this->remark,
                'lcu'=>$uid,
                'time'=>date('Y-m-d H:i:s'),
            ));
            return true;
        }
        return false;
    }
}
