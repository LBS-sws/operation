<?php

class OrderForm extends CFormModel
{
	public $id;
	public $order_user;
	public $technician;
    public $status;
    public $remark;
	public $luu;
	public $lcu;
	public $statusList;
	public $order_code;
	public $goods_list;

	public function attributeLabels()
	{
		return array(
            'order_code'=>Yii::t('procurement','Order Code'),
            'goods_list'=>Yii::t('procurement','Goods List'),
            'order_user'=>Yii::t('procurement','Order User'),
            'technician'=>Yii::t('procurement','Technician'),
            'status'=>Yii::t('procurement','Order Status'),
            'remark'=>Yii::t('procurement','Remark'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, order_user, technician, status, remark, luu, lcu, lud, lcd','safe'),
            array('goods_list','required'),
            array('goods_list','validateGoods'),
            array('technician','validateTe'),
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
            if(empty($goods["goods_id"]) && empty($goods["goods_num"])){
                unset($this->goods_list[$key]);
            }else if (empty($goods["goods_id"]) || empty($goods["goods_num"])){
                $message = Yii::t('procurement','The goods or quantity cannot be empty');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["goods_id"])){
                $message = Yii::t('procurement','goods does not exist');
                $this->addError($attribute,$message);
                return false;
            }else if(!is_numeric($goods["goods_num"])){
                $message = Yii::t('procurement','Goods Number can only be numbered');
                $this->addError($attribute,$message);
                return false;
            }
        }
        if(count($this->goods_list)<1){
            $message = Yii::t('procurement','Fill in at least one goods');
            $this->addError($attribute,$message);
        }
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

    //根據訂單id查訂單所有物品
    public function getGoodsListToId($order_id){
        $rs = Yii::app()->db->createCommand()->select("b.name,b.price,b.unit,b.type,a.goods_num,a.confirm_num,a.id,a.goods_id")
            ->from("opr_order_goods a,opr_goods b")->where('a.order_id=:order_id and a.goods_id = b.id',array(':order_id'=>$order_id))->queryAll();
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


    //獲取物品列表
    public function getGoodsList(){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_goods")->queryAll();
        return $rs;
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
		$rows = Yii::app()->db->createCommand()->select("id, order_code, order_user, technician, status, remark")
            ->from("opr_order")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->order_code = $row['order_code'];
                $this->goods_list = $this->getGoodsListToId($row['id']);
                $this->order_user = $row['order_user'];
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
							order_user, technician, remark, status, lcu, lcd
						) values (
							:order_user,:technician,:remark, :status, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_order set
							technician = :technician,
							remark = :remark,
							luu = :luu,
							lud = :lud,
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
        if (strpos($sql,':order_user')!==false)
            $command->bindParam(':order_user',$order_username,PDO::PARAM_STR);
        if (strpos($sql,':status')!==false){
            if($this->scenario == "new"){
                $this->status = "pending";
            }
            $command->bindParam(':status',$this->status,PDO::PARAM_STR);
        }

        if (strpos($sql,':technician')!==false)
            $command->bindParam(':technician',$this->technician,PDO::PARAM_STR);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date('Y-m-d H:i:s'),PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
            $code = strval($this->id);
            $this->order_code = "";
            for($i = 0;$i < 5-strlen($code);$i++){
                $this->order_code.="0";
            }
            $this->order_code .= $code;
            Yii::app()->db->createCommand()->update('opr_order', array(
                'order_code'=>$this->order_code,
            ), 'id=:id', array(':id'=>$this->id));
        }
        if ($this->scenario=='delete'){
            Yii::app()->db->createCommand()->delete('opr_order_status', 'order_id=:order_id', array(':order_id'=>$this->id));
            Yii::app()->db->createCommand()->delete('opr_order_goods', 'order_id=:order_id', array(':order_id'=>$this->id));
        }else{
            Yii::app()->db->createCommand()->insert('opr_order_status', array(
                'order_id'=>$this->id,
                'status'=>$this->status,
                'r_remark'=>$this->remark,
                'lcu'=>$order_username,
                'time'=>date('Y-m-d H:i:s'),
            ));
        }

        //物品的添加、修改
        foreach ($this->goods_list as $goods){
            if(empty($goods["id"])){
                //添加
                Yii::app()->db->createCommand()->insert('opr_order_goods', array(
                    'goods_id'=>$goods["goods_id"],
                    'order_id'=>$this->id,
                    'goods_num'=>$goods["goods_num"],
                    'lcu'=>$uid,
                    'lcd'=>date('Y-m-d H:i:s'),
                ));
            }else{
                //修改
                Yii::app()->db->createCommand()->update('opr_order_goods', array(
                    'goods_id'=>$goods["goods_id"],
                    'goods_num'=>$goods["goods_num"],
                    'luu'=>$uid,
                    'lud'=>date('Y-m-d H:i:s'),
                ), 'id=:id', array(':id'=>$goods["id"]));
            }
        }
		return true;
	}
}
