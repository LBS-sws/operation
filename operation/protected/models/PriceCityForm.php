<?php

class PriceCityForm extends CFormModel
{
	public $id;
	public $city;
	public $city_name;
	public $price_type;

	public function attributeLabels()
	{
		return array(
            'city_name'=>Yii::t('user','City'),
            'price_type'=>Yii::t('procurement','price type'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, city,city_name,price_type','safe'),
            array('city','required'),
            array('city_name','required'),
            array('price_type', 'required'),
			array('city','validateCity'),
            //, 'message'=>'必须为电子邮箱', 'pattern'=>'/[a-z]/i'
		);
	}

	public function validateCity($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("id")->from("opr_city_price")
            ->where('city=:city', array(':city'=>$this->city))->queryRow();
        if($row){
            $this->id = $row["id"];
            $this->setScenario("edit");
        }else{
            $this->setScenario("new");
        }
	}

	public function retrieveData($index) {
        $suffix = Yii::app()->params['envSuffix'];
		$rows = Yii::app()->db->createCommand()->select("a.code,a.name as city_name,b.price_type")
            ->from("security$suffix.sec_city a")
            ->leftJoin("opr_city_price b","a.code = b.city")
            ->where("a.code=:code",array(":code"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->city = $row['code'];
                $this->city_name = $row['city_name'];
                $this->price_type = $row['price_type'] != 2?1:2;
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
            case 'new':
                $sql = "insert into opr_city_price(
							city,price_type
						) values (
							:city,:price_type
						)";
                break;
            case 'edit':
                $sql = "update opr_city_price set
							city = :city, 
							price_type = :price_type
						where id = :id
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
        if (strpos($sql,':price_type')!==false)
            $command->bindParam(':price_type',$this->price_type,PDO::PARAM_STR);
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$this->city,PDO::PARAM_INT);

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
