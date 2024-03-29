<?php

class GoodsImForm extends CFormModel
{
	public $id;
	public $goods_code;
	public $name;
	public $classify_id;
	public $type;
	public $unit;
	public $price;
	public $price_two;
    public $big_num = 99999;
    public $small_num = 1;
    public $multiple = 1;
    public $rules_id = 0;
	public $origin;
	public $len;
	public $width;
	public $height;
	public $net_weight;
	public $gross_weight;
	public $inspection;
	public $customs_code;
	public $customs_name;
	public $img_url;
    public $orderClass = "Import";

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
            'price'=>Yii::t('procurement','price one').'（US$）',
            'price_two'=>Yii::t('procurement','price two').'（US$）',
            'big_num'=>Yii::t('procurement','Max Number'),
            'small_num'=>Yii::t('procurement','Min Number'),
            'origin'=>Yii::t('procurement','Origin'),
            'inspection'=>Yii::t('procurement','inspection'),
            'customs_code'=>Yii::t('procurement','customs code'),
            'customs_name'=>Yii::t('procurement','customs name'),
            'net_weight'=>Yii::t('procurement','Net Weight（kg）'),
            'gross_weight'=>Yii::t('procurement','Gross Weight（kg）'),
            'volume'=>Yii::t('procurement','Length×Width×Height（cm）'),
            'img_url'=>Yii::t("procurement","good image"),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, goods_code, img_url, name, customs_name, customs_code, inspection, classify_id, type, unit, price, price_two, rules_id, multiple, big_num, small_num, net_weight, gross_weight, origin, len, width, height','safe'),
            array('goods_code','required'),
            array('name','required'),
            array('type','required'),
            array('unit','required'),
            array('origin','required'),
            array('price','required'),
            array('price_two','required'),
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
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods_im")->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
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
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_goods_im")->where('goods_code=:goods_code and id!=:id', array(':goods_code'=>$this->goods_code,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the Goods Code of already exists');
            $this->addError($attribute,$message);
        }
	}

//id, goods_code, name, classify_id, type, unit, price, big_num, small_num, net_weight, gross_weight, origin, len, width, height
	public function retrieveData($index) {
		$city = Yii::app()->user->city();
		$rows = Yii::app()->db->createCommand()->select()
            ->from("opr_goods_im")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->type = $row['type'];
                $this->img_url = empty($row['img_url'])?"":Yii::app()->request->baseUrl."/".$row['img_url'];
                $this->unit = $row['unit'];
                $this->price = sprintf("%.2f", $row['price']);
                $this->price_two = sprintf("%.2f", $row['price_two']);
                $this->goods_code = $row['goods_code'];
                $this->classify_id = $row['classify_id'];
                $this->net_weight = $row['net_weight'];
                $this->gross_weight = $row['gross_weight'];
                $this->origin = $row['origin'];
                $this->len = $row['len'];
                $this->width = $row['width'];
                $this->height = $row['height'];
                $this->big_num = $row['big_num'];
                $this->small_num = $row['small_num'];
                $this->multiple = $row['multiple'];
                $this->rules_id = $row['rules_id'];
                $this->customs_name = $row['customs_name'];
                $this->customs_code = $row['customs_code'];
                $this->inspection = $row['inspection'];
                break;
			}
		}
		return true;
	}


    public function downExcel(){
        $list["head"] = array("物品编号","物品名称","物品分类","来源地","包装规格","单位"
        ,'价格1','价格2',"海关编号","海关名字","商检","净重","毛重","长","宽","高","数量倍率","最大数量","最小数量","混合規則");
        $rs = Yii::app()->db->createCommand()->select("a.*,b.name as classify_name,c.name as rules_name")->from("opr_goods_im a")
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
                    "price_two"=>$row["price_two"],
                    "customs_code"=>$row["customs_code"],
                    "customs_name"=>$row["customs_name"],
                    "inspection"=>$row["inspection"],
                    "net_weight"=>$row["net_weight"],
                    "gross_weight"=>$row["gross_weight"],
                    "len"=>$row["len"],
                    "width"=>$row["width"],
                    "height"=>$row["height"],
                    "multiple"=>$row["multiple"],
                    "big_num"=>$row["big_num"],
                    "small_num"=>$row["small_num"],
                    "rules_name"=>$row["rules_name"]
                );
            }
        }
        return $list;
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

    public static function uploadImg($className,$id,$sqlName){
        $upload = CUploadedFile::getInstanceByName("img_url");
        if($upload){
            $fileType = $upload->extensionName;
            if(in_array($fileType,array("png","jpg","jpeg"))){
                $upload_url ="upload/images/{$className}/";
                $upload_url.= "id_{$id}". '.' . $fileType;
                $upload->saveAs($upload_url);
                $fileUrl = dirname(Yii::app()->basePath)."/".$upload_url;
                self::resizeImage($fileUrl,900);
                Yii::app()->db->createCommand()->update($sqlName, array(
                    'img_url'=>$upload_url,
                ), 'id=:id', array(':id'=>$id));
            }
        }
    }

//縮放圖片
    public static function resizeImage($fileUrl,$width=2000){
        $fileInfo = getimagesize($fileUrl);
        $fileType = $fileInfo[2];
        $newWidth = $fileInfo[0];
        $newHeight= $fileInfo[1];
        // 根据文件类型获取后缀名
        $extension = image_type_to_extension($fileType);
        $extension = str_replace(".","",$extension);
        if($fileInfo[0]>$width){ //寬度最大700
            $newWidth = $width;
            $newHeight = ($fileInfo[1]/$fileInfo[0])*$width;
        }
        if($fileInfo[1]>1000){ //高度最大1000
            $newHeight = 1000;
            $newWidth = ($fileInfo[0]/$fileInfo[1])*$newHeight;
        }
        $newImg = imagecreatetruecolor($newWidth,$newHeight);
        $fun = "imagecreatefrom".$extension;
        $source = $fun($fileUrl);
        imagecopyresampled($newImg, $source, 0, 0, 0, 0, $newWidth,$newHeight, $fileInfo[0], $fileInfo[1]);
        imagedestroy($source);
        //圖片保存為jpg
        imagejpeg($newImg,$fileUrl,100);
        // 释放内存
        imagedestroy($newImg);
    }

	protected function saveGoods(&$connection) {
		$sql = '';
        switch ($this->scenario) {
            case 'delete':
                $sql = "delete from opr_goods_im where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_goods_im(
							name, type, unit, inspection, customs_code, customs_name, price, price_two, goods_code, classify_id, net_weight, gross_weight, origin, len, width, height, rules_id, multiple, big_num, small_num,lcu,lcd
						) values (
							:name, :type, :unit, :inspection, :customs_code, :customs_name, :price, :two_price, :goods_code, :classify_id, :net_weight, :gross_weight, :origin, :len, :width, :height, :rules_id, :multiple, :big_num, :small_num,:lcu,:lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_goods_im set
							name = :name, 
							type = :type, 
							unit = :unit,
							classify_id = :classify_id,
							net_weight = :net_weight,
							gross_weight = :gross_weight,
							origin = :origin,
							len = :len,
							width = :width,
							height = :height,
							goods_code = :goods_code,
							multiple = :multiple,
							rules_id = :rules_id,
							big_num = :big_num,
							small_num = :small_num,
							luu = :luu,
							lud = :lud,
							price = :price,
							inspection = :inspection,
							customs_code = :customs_code,
							customs_name = :customs_name,
							price_two = :two_price
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
        if (strpos($sql,':gross_weight')!==false)
            $command->bindParam(':gross_weight',$this->gross_weight,PDO::PARAM_STR);
        if (strpos($sql,':net_weight')!==false)
            $command->bindParam(':net_weight',$this->net_weight,PDO::PARAM_STR);
        if (strpos($sql,':big_num')!==false)
            $command->bindParam(':big_num',$this->big_num,PDO::PARAM_INT);
        if (strpos($sql,':small_num')!==false)
            $command->bindParam(':small_num',$this->small_num,PDO::PARAM_INT);
        if (strpos($sql,':multiple')!==false)
            $command->bindParam(':multiple',$this->multiple,PDO::PARAM_INT);
        if (strpos($sql,':rules_id')!==false)
            $command->bindParam(':rules_id',$this->rules_id,PDO::PARAM_INT);
        if (strpos($sql,':len')!==false)
            $command->bindParam(':len',$this->len,PDO::PARAM_INT);
        if (strpos($sql,':width')!==false)
            $command->bindParam(':width',$this->width,PDO::PARAM_INT);
        if (strpos($sql,':height')!==false)
            $command->bindParam(':height',$this->height,PDO::PARAM_INT);
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
        if (strpos($sql,':two_price')!==false)
            $command->bindParam(':two_price',$this->price_two,PDO::PARAM_STR);
        if (strpos($sql,':inspection')!==false)
            $command->bindParam(':inspection',$this->inspection,PDO::PARAM_STR);
        if (strpos($sql,':customs_code')!==false)
            $command->bindParam(':customs_code',$this->customs_code,PDO::PARAM_STR);
        if (strpos($sql,':customs_name')!==false)
            $command->bindParam(':customs_name',$this->customs_name,PDO::PARAM_STR);

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
        self::uploadImg(get_class($this),$this->id,"opr_goods_im");
		return true;
	}
}
