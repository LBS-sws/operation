<?php

class WarehouseForm extends CFormModel
{
	public $id;
	public $goods_code;
	public $name;
	public $type;
	public $unit;
	public $inventory;
	public $luu;
	public $lcu;

	public function attributeLabels()
	{
		return array(
            'goods_code'=>Yii::t('procurement','Goods Code'),
            'name'=>Yii::t('procurement','Name'),
            'type'=>Yii::t('procurement','Type'),
            'unit'=>Yii::t('procurement','Unit'),
            'inventory'=>Yii::t('procurement','Inventory'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_code, name, type, unit, inventory, lcu, luu','safe'),
            array('goods_code','required'),
            array('goods_code','numerical','allowEmpty'=>false,'integerOnly'=>true),
            array('name','required'),
            array('type','required'),
            array('unit','required'),
            array('inventory','required'),
            array('inventory','numerical','allowEmpty'=>false,'integerOnly'=>true),
			array('name','validateName'),
			array('goods_code','validateCode'),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_warehouse")
            ->where('name=:name and id!=:id and city = :city', array(':name'=>$this->name,':id'=>$id,':city'=>$city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}
	public function validateCode($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_warehouse")
            ->where('goods_code=:goods_code and id!=:id and city = :city', array(':goods_code'=>$this->goods_code,':id'=>$id,':city'=>$city))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Goods Code of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_warehouse")->where("id=:id and city=:city",array(":id"=>$index,':city'=>$city))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->type = $row['type'];
                $this->unit = $row['unit'];
                $this->goods_code = $row['goods_code'];
                $this->inventory = $row['inventory'];
                break;
			}
		}
		return true;
	}

    //獲取物品列表
    public function getGoodsList(){
        $city = Yii::app()->user->city();
        $rs = Yii::app()->db->createCommand()->select()->from("opr_warehouse")->where("city=:city",array(":city"=>$city))->queryAll();
        return $rs;
    }

    //根據物品id獲取物品信息
    public function getGoodsToGoodsId($goods_id){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_warehouse")
            ->where('id = :id and city=:city',array(':id'=>$goods_id,':city'=>$city))
            ->queryAll();
        if($rows){
            return $rows[0];
        }else{
            return array();
        }
    }

    //根據訂單id查訂單所有物品
    public function getGoodsListToId($order_id){
        $rs = Yii::app()->db->createCommand()->select("b.name,b.inventory,b.goods_code,b.unit,b.type,a.goods_num,a.confirm_num,a.id,a.goods_id")
            ->from("opr_order_goods a,opr_warehouse b")->where('a.order_id=:order_id and a.goods_id = b.id',array(':order_id'=>$order_id))->queryAll();
        return $rs;
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
                $sql = "delete from opr_warehouse where id = :id AND city=:city";
                break;
            case 'new':
                $sql = "insert into opr_warehouse(
							name, type, unit, inventory, lcu, lcd, goods_code,city
						) values (
							:name, :type, :unit, :inventory, :lcu, :lcd, :goods_code,:city
						)";
                break;
            case 'edit':
                $sql = "update opr_warehouse set
							name = :name, 
							type = :type, 
							unit = :unit,
							goods_code = :goods_code,
							luu = :luu,
							lud = :lud,
							inventory = :inventory
						where id = :id AND city=:city
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':goods_code')!==false)
            $command->bindParam(':goods_code',$this->goods_code,PDO::PARAM_STR);
        if (strpos($sql,':type')!==false)
            $command->bindParam(':type',$this->type,PDO::PARAM_STR);
        if (strpos($sql,':unit')!==false)
            $command->bindParam(':unit',$this->unit,PDO::PARAM_STR);
        if (strpos($sql,':inventory')!==false)
            $command->bindParam(':inventory',$this->inventory,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lud')!==false)
            $command->bindParam(':lud',date("Y-m-d H:s:i"),PDO::PARAM_STR);
        if (strpos($sql,':lcd')!==false)
            $command->bindParam(':lcd',date("Y-m-d H:s:i"),PDO::PARAM_STR);
        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
        }
		return true;
	}
}