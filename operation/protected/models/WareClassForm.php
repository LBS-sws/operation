<?php
//2024年9月28日09:28:46

class WareClassForm extends CFormModel
{
	public $id;
	public $warehouse_id;
	public $class_str;
	public $class_report;
    public $warehouseList;

	public function attributeLabels()
	{
		return array(
            'warehouse_id'=>"物料id",
            'goods_code'=>"物料编号",
            'name'=>"物料名称",
            'class_str'=>"分类名称",
            'class_report'=>"清洁/灭虫/其它",
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, warehouse_id, class_report,class_str','safe'),
            array('warehouse_id','required'),
            array('class_report,class_str','required'),
			array('warehouse_id','validateWare'),
		);
	}

	public function validateWare($attribute, $params){
        $row = Yii::app()->db->createCommand()->select("*")->from("opr_warehouse")
            ->where('id=:id',array(':id'=>$this->warehouse_id))->queryRow();
        if($row){
            $this->warehouseList=$row;
        }else{
            $message = "物料id异常，请刷新重试";
            $this->addError($attribute,$message);
        }
	}

	public function retrieveData($index) {
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("opr_warehouse_class")->where("id=:id",array(":id"=>$index))->queryRow();
        if ($row) {
            $this->id = $row['id'];
            $this->warehouse_id = $row['warehouse_id'];
            $this->class_report = $row['class_report'];
            $this->class_str = $row['class_str'];
            return true;
        }else{
            return false;
        }
	}

	public function retrieveDataByWareID($index) {
        $this->warehouse_id = $index;
		$row = Yii::app()->db->createCommand()->select("*")
            ->from("opr_warehouse_class")->where("warehouse_id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->class_report = $row['class_report'];
            $this->class_str = $row['class_str'];
        }
        return true;
	}

	public function getClassStr(){
	    return array(
            "清洁"=>"清洁",
            "灭虫"=>"灭虫",
            "纸品"=>"纸品",
            "除油"=>"除油",
            "辅助"=>"辅助",
            "飘盈香"=>"飘盈香",
        );
    }

	public function getClassReport(){
	    return array(
            "清洁"=>"清洁",
            "灭虫"=>"灭虫",
            "其它"=>"其它",
        );
    }

    public static function downExcel(){
        $list["head"] = array("物料id","物料编码","物料名称","物料分类","清洁/灭虫/其它");
        $rs = Yii::app()->db->createCommand()->select("a.*")->from("opr_warehouse a")
            ->leftJoin("opr_warehouse_class b","a.id=b.warehouse_id")
            ->where('a.local_bool=0 and b.id is null')->queryAll();
        $list["body"] = array();
        if($rs){
            foreach ($rs as $row){
                $arr = array(
                    "id"=>$row["id"],
                    "goods_code"=>$row["goods_code"],
                    "name"=>$row["name"],
                    "class_str"=>"",
                    "class_report"=>"",
                );
                $list["body"][]=$arr;
            }
        }
        return $list;
    }

	protected function resetScenario(){
        $row = Yii::app()->db->createCommand()->select("*")
            ->from("opr_warehouse_class")->where("warehouse_id=:id",array(":id"=>$this->warehouse_id))->queryRow();
        if($row){
            $this->id = $row["id"];
        }else{
            $this->id=null;
        }
        if($this->getScenario()!="delete"){
            if($row){
                $this->setScenario('edit');
            }else{
                $this->setScenario('new');
            }
        }
    }

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
		    $this->resetScenario();
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
                $sql = "delete from opr_warehouse_class where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_warehouse_class(
							warehouse_id,class_str,class_report, lcu, lcd
						) values (
							:warehouse_id,:class_str,:class_report, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_warehouse_class set
							warehouse_id = :warehouse_id, 
							class_str = :class_str, 
							class_report = :class_report, 
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
        if (strpos($sql,':warehouse_id')!==false)
            $command->bindParam(':warehouse_id',$this->warehouse_id,PDO::PARAM_STR);
        if (strpos($sql,':class_str')!==false)
            $command->bindParam(':class_str',$this->class_str,PDO::PARAM_STR);
        if (strpos($sql,':class_report')!==false)
            $command->bindParam(':class_report',$this->class_report,PDO::PARAM_STR);

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
        }
		return true;
	}
}
