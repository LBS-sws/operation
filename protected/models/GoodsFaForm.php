<?php

class GoodsFaForm extends CFormModel
{
	public $id;
	public $goods_code;
	public $name;
	public $classify_id;
	public $type;
	public $unit;
	public $price;
    public $big_num = 99999;
    public $small_num = 1;
    public $multiple = 1;
    public $rules_id = 0;
	public $origin;
	public $img_url;
    public $orderClass = "Fast";

	public function attributeLabels()
	{
		return array(
            'goods_code'=>Yii::t('procurement','Goods Code'),
            'name'=>Yii::t('procurement','Name'),
            'multiple'=>Yii::t('procurement','Multiple'),
            'rules_id'=>Yii::t('procurement','Hybrid Rules'),
            'classify_id'=>Yii::t('procurement','Classify'),
            'type'=>Yii::t('procurement','Type'),
            'unit'=>Yii::t('procurement','Unit'),
            'price'=>Yii::t('procurement','Price（US$）'),
            'big_num'=>Yii::t('procurement','Max Number'),
            'small_num'=>Yii::t('procurement','Min Number'),
            'origin'=>Yii::t('procurement','Origin'),
            'img_url'=>Yii::t("procurement","good image"),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_code, name, img_url, classify_id, type, unit, price, rules_id, multiple, big_num, small_num, origin','safe'),
            array('goods_code','required'),
            array('name','required'),
            array('type','required'),
            array('unit','required'),
            array('origin','required'),
            array('price','required'),
            array('classify_id','required'),
            array('price','numerical','allowEmpty'=>false,'integerOnly'=>false),
            array('classify_id','numerical','allowEmpty'=>true,'integerOnly'=>true),
            array('big_num','numerical','allowEmpty'=>false,'integerOnly'=>true,'min'=>1),
            array('small_num','numerical','allowEmpty'=>false,'integerOnly'=>true,'min'=>1),
            array('multiple','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>1),
            array('rules_id','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>0),
			array('name','validateName'),
			array('goods_code','validateCode'),
		);
	}

	public function validateName($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods_fa")->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}
	public function validateCode($attribute, $params){
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods_fa")->where('goods_code=:goods_code and id!=:id', array(':goods_code'=>$this->goods_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Goods Code of already exists');
            $this->addError($attribute,$message);
        }
	}

    public function downExcel(){
        $list["head"] = array("存货编码","存货名称","所属分类码","来源地","规格型号","主计量单位"
        ,'参考售价',"数量倍率","最大数量","最小数量","混合規則");
        $rs = Yii::app()->db->createCommand()->select("a.*,b.name as classify_name,c.name as rules_name")
            ->from("opr_goods_fa a")
            ->leftJoin("opr_classify b","a.classify_id=b.id")
            ->leftJoin("opr_goods_rules c","a.rules_id=c.id")->queryAll();
        $list["body"] = array();
        if($rs){
            foreach ($rs as $row){
                $list["body"][]=array(
                    "goods_code"=>$row["goods_code"],
                    "name"=>$row["name"],
                    "classify_name"=>$row["classify_name"],
                    "origin"=>$row["origin"],
                    "type"=>$row["type"],
                    "unit"=>$row["unit"],
                    "price"=>$row["price"],
                    "multiple"=>$row["multiple"],
                    "big_num"=>$row["big_num"],
                    "small_num"=>$row["small_num"],
                    "rules_name"=>$row["rules_name"]
                );
            }
        }
        return $list;
    }

	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select()
            ->from("opr_goods_fa")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->type = $row['type'];
                $this->unit = $row['unit'];
                $this->price = sprintf("%.2f", $row['price']);
                $this->goods_code = $row['goods_code'];
                $this->classify_id = $row['classify_id'];
                $this->origin = $row['origin'];
                $this->big_num = $row['big_num'];
                $this->img_url = empty($row['img_url'])?"":Yii::app()->request->baseUrl."/".$row['img_url'];
                $this->small_num = $row['small_num'];
                $this->rules_id = $row['rules_id'];
                $this->multiple = $row['multiple'];
                break;
			}
		}
		return true;
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
                $sql = "delete from opr_goods_fa where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_goods_fa(
							name, type, unit, price, goods_code, classify_id, origin, rules_id, multiple, big_num, small_num,lcu,lcd
						) values (
							:name, :type, :unit, :price, :goods_code, :classify_id, :origin, :rules_id, :multiple, :big_num, :small_num,:lcu,:lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_goods_fa set
							name = :name, 
							type = :type, 
							multiple = :multiple, 
							rules_id = :rules_id, 
							unit = :unit,
							classify_id = :classify_id,
							origin = :origin,
							goods_code = :goods_code,
							big_num = :big_num,
							small_num = :small_num,
							luu = :luu,
							lud = :lud,
							price = :price
						where id = :id
						";
                break;
        }
		if (empty($sql)) return false;

        //$city = Yii::app()->user->city();
        $uid = Yii::app()->user->id;

        $command=$connection->createCommand($sql);
        if(empty($this->big_num)){
            $this->big_num = 0;
        }
        if(empty($this->small_num)){
            $this->small_num = 0;
        }
        if (strpos($sql,':id')!==false)
            $command->bindParam(':id',$this->id,PDO::PARAM_INT);
        if (strpos($sql,':classify_id')!==false)
            $command->bindParam(':classify_id',$this->classify_id,PDO::PARAM_INT);
        if (strpos($sql,':big_num')!==false)
            $command->bindParam(':big_num',$this->big_num,PDO::PARAM_INT);
        if (strpos($sql,':small_num')!==false)
            $command->bindParam(':small_num',$this->small_num,PDO::PARAM_INT);
        if (strpos($sql,':multiple')!==false)
            $command->bindParam(':multiple',$this->multiple,PDO::PARAM_INT);
        if (strpos($sql,':rules_id')!==false)
            $command->bindParam(':rules_id',$this->rules_id,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':goods_code')!==false)
            $command->bindParam(':goods_code',$this->goods_code,PDO::PARAM_STR);
        if (strpos($sql,':origin')!==false)
            $command->bindParam(':origin',$this->origin,PDO::PARAM_STR);
        if (strpos($sql,':type')!==false)
            $command->bindParam(':type',$this->type,PDO::PARAM_STR);
        if (strpos($sql,':unit')!==false)
            $command->bindParam(':unit',$this->unit,PDO::PARAM_STR);
        if (strpos($sql,':price')!==false)
            $command->bindParam(':price',$this->price,PDO::PARAM_STR);
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
        GoodsImForm::uploadImg(get_class($this),$this->id,"opr_goods_fa");
		return true;
	}
}
