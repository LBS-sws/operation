<?php

class StorageForm extends CFormModel
{
	public $id;
	public $code;
	public $apply_time;
	public $goods_list=array();
	public $storage_list=array();
	public $status_type;
	public $remark;
	public $storage_code;
	public $storage_name;

	public function attributeLabels()
	{
        return array(
            'code'=>Yii::t('procurement','storage code'),
            'apply_time'=>Yii::t('procurement','storage time'),
            'goods_list'=>Yii::t('procurement','storage goods'),
            'status_type'=>Yii::t('procurement','storage type'),
            'remark'=>Yii::t('procurement','Remark'),
        );
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, apply_time,code,goods_list,status_type,remark,storage_code,storage_name','safe'),
            array('apply_time','required'),
            array('goods_list','required'),
            array('id','validateId'),
            array('apply_time','validateTime'),
            array('name','validateGoodsList'),
            //, 'message'=>'必须为电子邮箱', 'pattern'=>'/[a-z]/i'
		);
	}

	public function validateId($attribute, $params){
        if(!empty($this->id)){ //驗證入庫單是否能修改
            $rows = Yii::app()->db->createCommand()->select("id")->from("opr_storage")
                ->where('status_type=1 and id=:id', array(':id'=>$this->id))->queryRow();
            if($rows){
                $message = Yii::t('procurement','The receipt is in storage and cannot be modified');
                $this->addError($attribute,$message);
                return false;
            }
        }
	}

	public function validateTime($attribute, $params){
        if(!empty($this->apply_time)){ //驗證入庫單時間
            $startTime = date("Y-m-d",strtotime("-1 week"));
            $endTime = date("Y-m-d");
            $date = date("Y-m-d",strtotime($this->apply_time));
            if($date<$startTime || $date>$endTime){
                $message = Yii::t('procurement','storage time').Yii::t('procurement',' is scope of').":$startTime ~ $endTime";
                $this->addError($attribute,$message);
                return false;
            }
        }
	}

	public function validateGoodsList($attribute, $params){
	    if(!empty($this->goods_list)){
            $city = Yii::app()->user->city();
            $this->storage_list = array();
            $this->storage_code = "";
            $this->storage_name = "";
	        foreach ($this->goods_list as $row){
                $goods = Yii::app()->db->createCommand()->select("*")->from("opr_warehouse")
                    ->where('city=:city and id=:id', array(':id'=>$row["id"],":city"=>$city))->queryRow();
                if($goods){
                    $this->storage_code.=($this->storage_code==""?$this->storage_code:"~").$goods["id"];
                    $this->storage_name.=($this->storage_name==""?$this->storage_name:",").$goods["name"];
                    $this->storage_name.=" * ".$row["add_num"];
                    if(!is_numeric($row["add_num"])||$row["add_num"]<=0){
                        $message = Yii::t("procurement","Goods Number can only be numbered").":".$row["name"];
                        $this->addError($attribute,$message);
                        return false;
                    }else{
                        $this->storage_list[]=array(
                            "warehouse_id"=>$goods["id"],
                            "inventory"=>$goods["inventory"],
                            "min_num"=>$goods["min_num"],
                            "add_num"=>$row["add_num"],
                        );
                    }
                }else{
                    $message = "仓库物品不存在:".$row["name"];
                    $this->addError($attribute,$message);
                    return false;
                }
            }
        }
	}

	public function retrieveData($index) {
        $city = Yii::app()->user->city();
		$row = Yii::app()->db->createCommand()->select("*")
            ->from("opr_storage")->where("id=:id and city='$city'",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->apply_time = $row['apply_time'];
            $this->remark = $row['remark'];
            $this->status_type = $row['status_type'];
            $this->storage_code = $row['storage_code'];
            $this->storage_name = $row['storage_name'];
            $rows = Yii::app()->db->createCommand()->select("a.add_num,b.inventory,b.goods_code,b.name,b.unit,b.id")
                ->from("opr_storage_info a")
                ->leftJoin("opr_warehouse b","a.warehouse_id = b.id")
                ->where("a.storage_id=:id",array(":id"=>$index))->queryAll();
            $this->goods_list = $rows?$rows:array();
		}
		return true;
	}

    //刪除驗證
    public function deleteValidate($num=1){
        $city = Yii::app()->user->city();
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_storage")
            ->where('status_type=:num and id=:id and city=:city', array(':num'=>$num,':city'=>$city,':id'=>$this->id))->queryRow();
        if($rows){
            return true;
        }else{
            return false;
        }
    }

    //退回入庫單
    public function backward(){
        Yii::app()->db->createCommand()->update('opr_storage', array(
            'status_type'=>0
        ), 'id=:id', array(':id'=>$this->id));

        $rows = Yii::app()->db->createCommand()->select("*")->from("opr_storage_info")
            ->where("storage_id=:id",array(":id"=>$this->id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                $goods = Yii::app()->db->createCommand()->select("*")->from("opr_warehouse")
                    ->where('id=:id', array(':id'=>$row["warehouse_id"]))->queryRow();
                if($goods){
                    $inventory = floatval($goods["inventory"])-floatval($row["add_num"]);
                    $z_index = $inventory<=floatval($goods["min_num"])?2:1;
                    Yii::app()->db->createCommand()->update('opr_warehouse', array(
                        'inventory'=>$inventory,
                        'z_index'=>$z_index
                    ), 'id=:id', array(':id'=>$row["warehouse_id"]));
                }
            }
        }
    }

    public function getReadonly(){
        return $this->getScenario()=='view'||$this->status_type==1;
    }

    public function printTableStorage(){
        $html = '';
        if(empty($this->goods_list)){
            $html.="<tr data-num=':id' id='table_template' style='display: none;'>";
            $html.="<td>";
            $html.=TbHtml::hiddenField(":model[:id][id]",":id");
            $html.=TbHtml::textField(":model[:id][goods_code]",":goods_code",array("readonly"=>true));
            $html.="</td>";
            $html.="<td>".TbHtml::textField(":model[:id][name]",":name",array("readonly"=>true))."</td>";
            $html.="<td>".TbHtml::textField(":model[:id][unit]",":unit",array("readonly"=>true))."</td>";
            $html.="<td>".TbHtml::textField(":model[:id][inventory]",":inventory",array("readonly"=>true))."</td>";
            $html.="<td>".TbHtml::numberField(":model[:id][add_num]","",array("readonly"=>$this->getReadonly(),'min'=>0))."</td>";
            if(!$this->getReadonly()) {
                $html .= "<td class='text-center'>" . TbHtml::button(Yii::t("dialog", "Remove"), array("class" => "storageDelete")) . "</td>";
            }
            $html.="</tr>";
            $html .= "<tr><td colspan='6'>请选择物品</td></tr>";
        }else{
            foreach ($this->goods_list as $row){
                $id = $row['id'];
                $html.="<tr data-num='$id'>";
                $html.="<td>";
                $html.=TbHtml::hiddenField("StorageForm[goods_list][$id][id]",$row['id']);
                $html.=TbHtml::textField("StorageForm[goods_list][$id][goods_code]",$row['goods_code'],array("readonly"=>true));
                $html.="</td>";
                $html.="<td>".TbHtml::textField("StorageForm[goods_list][$id][name]",$row['name'],array("readonly"=>true))."</td>";
                $html.="<td>".TbHtml::textField("StorageForm[goods_list][$id][unit]",$row['unit'],array("readonly"=>true))."</td>";
                $html.="<td>".TbHtml::textField("StorageForm[goods_list][$id][inventory]",$row['inventory'],array("readonly"=>true))."</td>";
                $html.="<td>".TbHtml::numberField("StorageForm[goods_list][$id][add_num]",floatval($row['add_num']),array("readonly"=>$this->getReadonly(),'min'=>0))."</td>";
                if(!$this->getReadonly()){
                    $html.="<td class='text-center'>".TbHtml::button(Yii::t("dialog","Remove"),array("class"=>"storageDelete"))."</td>";
                }
                $html.="</tr>";
            }
        }
        return $html;
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
                $sql = "delete from opr_storage where id = :id";
                break;
            case 'new':
                $sql = "insert into opr_storage(
							apply_time,remark,city,status_type,storage_code,storage_name, lcu, lcd
						) values (
							:apply_time,:remark,:city,:status_type,:storage_code,:storage_name, :lcu, :lcd
						)";
                break;
            case 'edit':
                $sql = "update opr_storage set
							apply_time = :apply_time, 
							remark = :remark, 
							status_type = :status_type, 
							storage_code = :storage_code, 
							storage_name = :storage_name, 
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
        if (strpos($sql,':city')!==false)
            $command->bindParam(':city',$city,PDO::PARAM_STR);
        if (strpos($sql,':apply_time')!==false)
            $command->bindParam(':apply_time',$this->apply_time,PDO::PARAM_STR);
        if (strpos($sql,':remark')!==false)
            $command->bindParam(':remark',$this->remark,PDO::PARAM_STR);
        if (strpos($sql,':storage_name')!==false)
            $command->bindParam(':storage_name',$this->storage_name,PDO::PARAM_STR);
        if (strpos($sql,':storage_code')!==false)
            $command->bindParam(':storage_code',$this->storage_code,PDO::PARAM_STR);
        if (strpos($sql,':status_type')!==false)
            $command->bindParam(':status_type',$this->status_type,PDO::PARAM_INT);

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
            $this->setStorageCode();
            $this->scenario = "edit";
        }

        Yii::app()->db->createCommand()->delete("opr_storage_info","storage_id=:storage_id",array(":storage_id"=>$this->id));
        foreach ($this->storage_list as $row){ //添加入庫物品
            Yii::app()->db->createCommand()->insert("opr_storage_info",array(
                "add_num"=>$row["add_num"],
                "storage_id"=>$this->id,
                "warehouse_id"=>$row["warehouse_id"],
            ));
            if($this->status_type == 1){ //物品入庫（庫存添加）
                $inventory = floatval($row["inventory"])+floatval($row["add_num"]);
                $z_index = $inventory<=floatval($row["min_num"])?2:1;
                Yii::app()->db->createCommand()->update('opr_warehouse', array(
                    'inventory'=>$inventory,
                    'z_index'=>$z_index
                ), 'id=:id', array(':id'=>$row["warehouse_id"]));
            }
        }
		return true;
	}

    private function setStorageCode(){
        $code = strval($this->id);
        $storageCode = "ST";
        for($i = 0;$i < 5-strlen($code);$i++){
            $storageCode.="0";
        }
        $storageCode .= $code;
        Yii::app()->db->createCommand()->update('opr_storage', array(
            'code'=>$storageCode
        ), 'id=:id', array(':id'=>$this->id));
    }
}
