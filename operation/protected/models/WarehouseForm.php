<?php

class WarehouseForm extends CFormModel
{
	public $id;
	public $goods_code;
	public $name;
	public $unit;
	public $orderClass = "Warehouse";
	public $inventory;
	public $classify_id;
	public $price;
	public $costing;
	public $decimal_num;
	public $luu;
	public $lcu;

	public function attributeLabels()
	{
		return array(
            'goods_code'=>Yii::t('procurement','Goods Code'),
            'classify_id'=>Yii::t('procurement','Classify'),
            'name'=>Yii::t('procurement','Name'),
            'unit'=>Yii::t('procurement','Unit'),
            'inventory'=>Yii::t('procurement','Inventory'),
            'price'=>Yii::t('procurement','Price（RMB）'),
            'costing'=>Yii::t('procurement','Costing（RMB）'),
            'decimal_num'=>Yii::t('procurement','Decimal'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_code, name, unit, inventory, classify_id, price, costing, decimal_num, lcu, luu','safe'),
            array('name','required'),
            array('classify_id','required'),
            array('unit','required'),
            array('inventory','required'),
            array('inventory','numerical','allowEmpty'=>false,'integerOnly'=>false),
			array('name','validateName'),
			array('goods_code','validateCode'),
			array('price','required'),
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

    //刪除驗證
    public function deleteValidate(){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_order_goods")->where('goods_id=:goods_id',array(':goods_id'=>$this->id))->queryAll();
        if($rs){
            return false;
        }else{
            return true;
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
                $this->unit = $row['unit'];
                $this->classify_id = $row['classify_id'];
                $this->goods_code = $row['goods_code'];
                $this->inventory = $row['inventory'];
                $this->costing = sprintf("%.2f",$row['costing']);
                $this->decimal_num = empty($row['decimal_num'])?"否":$row['decimal_num'];
                $this->price = $row['price'];
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
        $rs = Yii::app()->db->createCommand()->select("b.name,b.inventory,b.goods_code,b.classify_id,b.unit,a.goods_num,a.confirm_num,a.id,a.goods_id,a.remark,a.note")
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
							name, unit, inventory, classify_id, lcu, goods_code,city,price,costing,decimal_num
						) values (
							:name, :unit, :inventory, :classify_id, :lcu, :goods_code,:city,:price,:costing,:decimal_num
						)";
                break;
            case 'edit':
                $sql = "update opr_warehouse set
							name = :name, 
							classify_id = :classify_id, 
							unit = :unit,
							price = :price,
							costing = :costing,
							decimal_num = :decimal_num,
							luu = :luu,
							inventory = :inventory
						where id = :id AND city=:city
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $this->goods_code = 1;//後續因為自動生成物品編號，數據庫原因固定為1
        $command=$connection->createCommand($sql);
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':goods_code')!==false)
            $command->bindParam(':goods_code',$this->goods_code,PDO::PARAM_STR);
        if (strpos($sql,':unit')!==false)
            $command->bindParam(':unit',$this->unit,PDO::PARAM_STR);
        if (strpos($sql,':price')!==false)
            $command->bindParam(':price',$this->price,PDO::PARAM_STR);
        if (strpos($sql,':costing')!==false)
            $command->bindParam(':costing',$this->costing,PDO::PARAM_STR);
        if (strpos($sql,':decimal_num')!==false)
            $command->bindParam(':decimal_num',$this->decimal_num,PDO::PARAM_STR);
        if (strpos($sql,':inventory')!==false)
            $command->bindParam(':inventory',$this->inventory,PDO::PARAM_INT);
        if (strpos($sql,':classify_id')!==false)
            $command->bindParam(':classify_id',$this->classify_id,PDO::PARAM_INT);

        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':luu')!==false)
            $command->bindParam(':luu',$uid,PDO::PARAM_STR);
        if (strpos($sql,':lcu')!==false)
            $command->bindParam(':lcu',$uid,PDO::PARAM_STR);

        $command->execute();

        if ($this->scenario=='new'){
            $this->id = Yii::app()->db->getLastInsertID();
            $this->scenario = "edit";
            $this->setGoodsCode();
        }
		return true;
	}

    private function setGoodsCode(){
        $code = strval($this->id);
        $this->goods_code = "W";
        for($i = 0;$i < 5-strlen($code);$i++){
            $this->goods_code.="0";
        }
        $this->goods_code .= $code;
        Yii::app()->db->createCommand()->update('opr_warehouse', array(
            'goods_code'=>$this->goods_code
        ), 'id=:id', array(':id'=>$this->id));
    }

    public function downExcel(){
        $city = Yii::app()->user->city();
        $list["head"] = array("存货编码","存货名称","主计量单位","所属分类码","参考售价","是否允许小数","安全库存");
        $rs = Yii::app()->db->createCommand()->select("a.*,b.name as classify_name")->from("opr_warehouse a")
            ->leftJoin("opr_classify b","a.classify_id=b.id")
            ->where('a.city=:city',array(':city'=>$city))->queryAll();
        $list["body"] = array();
        if($rs){
            foreach ($rs as $row){
                $list["body"][]=array(
                    "goods_code"=>$row["goods_code"],
                    "name"=>$row["name"],
                    "unit"=>$row["unit"],
                    "classify_name"=>$row["classify_name"],
                    "price"=>$row["price"],
                    "decimal_num"=>$row["decimal_num"],
                    "inventory"=>$row["inventory"],
                );
            }
        }
        return $list;
    }
}
