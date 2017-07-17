<?php

class ClassifyForm extends CFormModel
{
	public $id;
	public $name;
	public $level;

	public function attributeLabels()
	{
		return array(
            'name'=>Yii::t('procurement','Name'),
            'level'=>Yii::t('procurement','Level'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, name,level','safe'),
            array('name','required'),
			array('name','validateName'),
            array('level','numerical','allowEmpty'=>true,'integerOnly'=>true,'min'=>0),
		);
	}

	public function validateName($attribute, $params){
        $city = Yii::app()->user->city();
        $id = -1;
        if(!empty($this->id)){
            $id = $this->id;
        }
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_classify")
            ->where('name=:name and id!=:id', array(':name'=>$this->name,':id'=>$id))->queryAll();
        if(count($rows)>0){
            $message = Yii::t('procurement','the name of already exists');
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
		$rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_classify")->where("id=:id",array(":id"=>$index))->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->level = $row['level'];
                break;
			}
		}
		return true;
	}

    //獲取分類列表
    public function getClassifyList(){
	    $arr = array(""=>"");
        $rs = Yii::app()->db->createCommand()->select()->from("opr_classify")->order('level desc')->queryAll();
        if($rs){
            foreach ($rs as $row){
                $arr[$row["id"]] = $row["name"];
            }
        }
        return $arr;
    }

    //根據訂單id查分類名字
    public function getClassifyToId($classify_id){
        $rs = Yii::app()->db->createCommand()->select("name")
            ->from("opr_classify")->where('id=:id',array(':id'=>$classify_id))->queryAll();
        if($rs){
            return $rs[0]["name"];
        }
        return "";
    }

    //刪除驗證
    public function deleteValidate(){
        $rs0 = Yii::app()->db->createCommand()->select()->from("opr_goods_do")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs1 = Yii::app()->db->createCommand()->select()->from("opr_goods_fa")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs2 = Yii::app()->db->createCommand()->select()->from("opr_goods_im")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        $rs3 = Yii::app()->db->createCommand()->select()->from("opr_warehouse")->where('classify_id=:classify_id',array(':classify_id'=>$this->id))->queryAll();
        if($rs0 || $rs1 || $rs2 || $rs3){
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
                $sql = "delete from opr_classify where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_classify(
							name,level, lcu, lcd
						) values (
							:name,:level, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_classify set
							name = :name, 
							level = :level, 
							luu = :luu,
							lud = :lud
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
        if (strpos($sql,':name')!==false)
            $command->bindParam(':name',$this->name,PDO::PARAM_STR);
        if (strpos($sql,':level')!==false)
            $command->bindParam(':level',$this->level,PDO::PARAM_INT);

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
