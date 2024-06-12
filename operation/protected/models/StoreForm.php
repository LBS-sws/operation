<?php

class StoreForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $name;
	public $city;
	public $jd_store_no;
	public $store_type=1;
	public $z_display=1;

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
        return array(
            'name'=>Yii::t('procurement','Store Name'),
            'jd_store_no'=>Yii::t('procurement','JD warehouse no'),
            'store_type'=>Yii::t('procurement','store type'),
            'z_display'=>Yii::t('procurement','display'),
            'city'=>Yii::t('procurement','city name'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,name,jd_store_no,store_type,city','safe'),
			array('name,jd_store_no,store_type,city','required'),
            array('store_type,z_display','numerical','allowEmpty'=>false,'integerOnly'=>true),
            array('city','validateCity'),
            array('id','validateID','on'=>array("delete")),
		);
	}

    public function validateCity($attribute, $params){
        $city = Yii::app()->user->city();
        $city_allow = Yii::app()->user->city_allow();
        if(empty($this->city)){
            $this->city=$city;
        }else{
            if (strpos("'{$city_allow}'","'{$this->city}'")===false){
                $this->city=$city;
                $message = "城市异常，请刷新重试";
                $this->addError($attribute, $message);
                return false;
            }
        }
    }

    public function validateID($attribute, $params) {
        $id = $this->$attribute;
        $row = Yii::app()->db->createCommand()->select("id")->from("opr_order_goods_store")
            ->where("store_id=:id",array(":id"=>$id))->queryRow();
        if($row){
            $this->addError($attribute, "这条记录已被使用无法删除");
            return false;
        }
    }

	public function retrieveData($index)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select * from opr_store where id='".$index."'";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$this->id = $row['id'];
			$this->name = $row['name'];
			$this->store_type = $row['store_type'];
			$this->z_display = $row['z_display'];
			$this->jd_store_no = $row['jd_store_no'];
			$this->city = $row['city'];
            return true;
		}else{
		    return false;
        }
	}

    public static function getStoreListForStoreID($store_id){
        $row = Yii::app()->db->createCommand()->select("jd_store_no")->from("opr_store")
            ->where("id=:id",array(":id"=>$store_id))->queryRow();
        if($row){
            return $row["jd_store_no"];
        }
        return "";
    }

    public static function getStoreListForCity($city){
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("*")->from("opr_store")
            ->where("z_display=1 and city=:city",array(":city"=>$city))->order("store_type asc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list[$row["id"]] = $row["name"];
            }
        }
        return $list;
    }

    public static function getStoreDefaultForCity($city){
        $row = Yii::app()->db->createCommand()->select("id,jd_store_no")->from("opr_store")
            ->where("store_type=1 and z_display=1 and city=:city",array(":city"=>$city))->order("id asc")->queryRow();
        if($row){
            return $row;
        }
        return false;
    }

    public static function getStoreListForOrder($orderGoodList){
        $list = array("store_id"=>array(),"store_num"=>array(),"id"=>array());
        $rows = Yii::app()->db->createCommand()->select("*")->from("opr_order_goods_store")
            ->where("order_goods_id=:id",array(":id"=>$orderGoodList["id"]))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $list["id"][] = $row["id"];
                $list["store_id"][] = $row["store_id"];
                $list["store_num"][] = $row["store_num"];
            }
        }else{
            $list["id"][] = 0;
            $list["store_id"][] = "";
            $list["store_num"][] = empty($orderGoodList["confirm_num"])?$orderGoodList["goods_num"]:$orderGoodList["confirm_num"];
        }
        return $list;
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
				$sql = "delete from opr_store where id = :id";
				break;
			case 'new':
				$sql = "insert into opr_store(
						name, jd_store_no, store_type, z_display, city, lcu, lcd) values (
						:name, :jd_store_no, :store_type, :z_display, :city, :lcu, :lcd)";
				break;
			case 'edit':
				$sql = "update opr_store set 
					name = :name, 
					jd_store_no = :jd_store_no,
					store_type = :store_type,
					z_display = :z_display,
					city = :city,
					luu = :luu
					where id = :id";
				break;
		}

		$uid = Yii::app()->user->id;

		$command=$connection->createCommand($sql);
		if (strpos($sql,':id')!==false)
			$command->bindParam(':id',$this->id,PDO::PARAM_INT);
		if (strpos($sql,':jd_store_no')!==false)
			$command->bindParam(':jd_store_no',$this->jd_store_no,PDO::PARAM_STR);
		if (strpos($sql,':store_type')!==false)
			$command->bindParam(':store_type',$this->store_type,PDO::PARAM_INT);
		if (strpos($sql,':z_display')!==false)
			$command->bindParam(':z_display',$this->z_display,PDO::PARAM_INT);
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_STR);

		if (strpos($sql,':lcu')!==false)
			$command->bindParam(':lcu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':luu')!==false)
			$command->bindParam(':luu',$uid,PDO::PARAM_STR);
		if (strpos($sql,':lcd')!==false){
            $date = date("Y-m-d H:i:s");
            $command->bindParam(':lcd',$date,PDO::PARAM_STR);
        }
		$command->execute();

        if ($this->scenario=='new')
            $this->id = Yii::app()->db->getLastInsertID();

		return true;
	}
}