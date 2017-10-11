<?php

/**
 * UserForm class.
 * UserForm is the data structure for keeping
 * user form data. It is used by the 'user' action of 'SiteController'.
 */
class UploadExcelForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $orderClass;
	public $file;
	public $dbName;
	public $add_num;
	public $update_id = 0;
	public $bool = true;
	public $error_list=array();
	public $start_title="";

	/**
     *
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,file,orderClass','safe'),
            array('file', 'file', 'types'=>'xlsx,xls', 'allowEmpty'=>false, 'maxFiles'=>1),
		);
	}

	//批量導入物品
    public function loadGoods($arr){
	    $errNum = 0;//失敗條數
	    $successNum = 0;//成功條數
        $validateArr = $this->getList();
        $bool = $this->bool;
        foreach ($validateArr as $vaList){
            if(!in_array($vaList["name"],$arr["listHeader"])){
                Dialog::message(Yii::t('dialog','Validation Message'), $vaList["name"].Yii::t("procurement"," Not Find"));
                return false;
            }
        }
        foreach ($arr["listBody"] as $list){
            $arrList = array();
            $continue = true;
            $this->start_title = current($list);
            foreach ($validateArr as $vaList){
                $key = array_search($vaList["name"],$arr["listHeader"]);
                $value = $this->validateStr($list[$key],$vaList,$bool);
                if($value['status'] == 1){
                    $arrList[$vaList["sqlName"]] = $value["data"];
                }else{
                    $continue = false;
                    array_push($this->error_list,$value["error"]);
                    break;
                }
            }
            if($continue){
                $city = Yii::app()->user->city();
                $uid = Yii::app()->user->id;
                if(!$bool&&!empty($this->update_id)){
                    $arrList["luu"] = $uid;
                    $arrList["inventory"] = floatval($arrList["inventory"])+floatval($this->add_num);
                    //疊加
                    Yii::app()->db->createCommand()->update($this->dbName,$arrList, 'id=:id', array(':id'=>$this->update_id));
                }else{
                    //新增
                    $arrList["lcu"] = $uid;
                    if($this->dbName == "Warehouse"){
                        $arrList["city"] = $city;
                    }
                    Yii::app()->db->createCommand()->insert($this->dbName, $arrList);
                }
                $successNum++;
            }else{
                $errNum++;
            }
        }
        $error = implode("<br>",$this->error_list);
        Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Success Num：').$successNum."<br>".Yii::t('procurement','Error Num：').$errNum."<br>".$error);
    }

    private function validateStr($value,$list,$bool){
        if(($value === "")&&($list["value"] === "")){
            return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."不能为空");
        }
        if(!empty($list["sql"])){
            switch ($list["sql"]){
                case 1:
                    if(empty($value)){
                        return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."不能为空");
                    }
                    //物品id及名稱驗證(不包括倉庫)
                    $rows = Yii::app()->db->createCommand()->select("*")->from($this->dbName)
                        ->where($list["value"], array(':name'=>$value))->queryRow();
                    if($rows){
                        return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."已經存在($value)");
                    }else{
                        return array("status"=>1,"data"=>$value);
                    }
                    break;
                case 2:
                    //物品分類
                    $rows = Yii::app()->db->createCommand()->select("*")->from('opr_classify')
                        ->where($list["value"], array(':name'=>$value))->queryRow();
                    if ($rows) {
                        return array("status"=>1,"data"=>$rows["id"]);
                    } else {
                        return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."沒有找到($value)");
                    }
                    break;
                case 3:
                    if(empty($value)){
                        return array("status"=>1,"data"=>"");
                    }
                    //國內貨標籤
                    $rows = Yii::app()->db->createCommand()->select("*")->from('opr_stickies')
                        ->where($list["value"], array(':name'=>$value))->queryRow();
                    if ($rows) {
                        return array("status"=>1,"data"=>$value);
                    } else {
                        return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."沒有找到($value)");
                    }
                    break;
                case 4:
                    if(empty($value)){
                        return array("status"=>0,"error"=>$this->start_title."：".$list["name"]."不能为空");
                    }
                    //倉庫物品驗證
                    $rows = Yii::app()->db->createCommand()->select("*")->from($this->dbName)
                        ->where($list["value"], array(':name'=>$value))->queryRow();
                    if($list["sqlName"] == "name"){
                        if(empty($this->update_id)&&!$rows){
                            return array("status"=>1,"data"=>$value);
                        }
                        if(!empty($this->update_id)&&$rows){
                            return array("status"=>1,"data"=>$value);
                        }
                        return array("status"=>0,"error"=>$this->start_title."："."存货编码与存货名称不對應");
                    }else{
                        if($rows){
                            $this->add_num = $rows["inventory"];
                            $this->update_id = $rows["id"];
                        }else{
                            $this->add_num = 0;
                            $this->update_id = 0;
                        }
                        return array("status"=>1,"data"=>$value);
                    }
                    break;
                case 5:
                    //混合規則驗證
                    if(empty($value)){
                        return array("status"=>1,"data"=>0);
                    }
                    $rows = Yii::app()->db->createCommand()->select("*")->from("opr_goods_rules")
                        ->where($list["value"], array(':name'=>$value))->queryRow();
                    if($rows){
                        return array("status"=>1,"data"=>$rows["id"]);
                    }else{
                        return array("status"=>0,"error"=>$this->start_title."："."没找到对应的混合规则（$value）");
                    }
                    break;
                default:
                    return array("status"=>0,"error"=>$this->start_title."："."404");
            }
        }else{
            if(empty($value)){
                return array("status"=>1,"data"=>$list["value"]);
            }else{
                return array("status"=>1,"data"=>$value);
            }
        }
    }

    private function getList(){
        $city = Yii::app()->user->city();
        $arr = array();
        switch ($this->orderClass){
            case "Warehouse":
                $this->bool = false;
                $this->dbName="opr_warehouse";
                $arr = array(
                    array("name"=>"存货编码","sqlName"=>"goods_code","value"=>"city='$city' and goods_code=:name","sql"=>"4"),
                    array("name"=>"存货名称","sqlName"=>"name","value"=>"city='$city' and name=:name","sql"=>"4"),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"class_type='Warehouse' and name=:name","sql"=>"2"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"成本","sqlName"=>"costing","value"=>"0.00"),
                    array("name"=>"是否允许小数","sqlName"=>"decimal_num","value"=>"否"),
                    array("name"=>"安全库存","sqlName"=>"inventory","value"=>""),
                );
                break;
            case "Document":
                $this->dbName="opr_goods_do";
                $arr = array(
                    array("name"=>"存货编码","sqlName"=>"goods_code","value"=>"goods_code=:name","sql"=>"1"),
                    array("name"=>"存货名称","sqlName"=>"name","value"=>"name=:name","sql"=>"1"),
                    array("name"=>"规格型号","sqlName"=>"type","value"=>"无"),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"class_type='Domestic' and name=:name","sql"=>"2"),
                    array("name"=>"标签","sqlName"=>"stickies_id","value"=>"id=:name","sql"=>"3"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"来源地","sqlName"=>"origin","value"=>""),
                    array("name"=>"混合規則","sqlName"=>"rules_id","value"=>"id=:name","sql"=>"5"),
                    array("name"=>"数量倍率","sqlName"=>"multiple","value"=>"1"),
                    array("name"=>"最大数量","sqlName"=>"big_num","value"=>"9999"),
                    array("name"=>"最小数量","sqlName"=>"small_num","value"=>"1"),
                );
                break;
            case "Import":
                $this->dbName="opr_goods_im";
                $arr = array(
                    array("name"=>"存货编码","sqlName"=>"goods_code","value"=>"goods_code=:name","sql"=>"1"),
                    array("name"=>"存货名称","sqlName"=>"name","value"=>"name=:name","sql"=>"1"),
                    array("name"=>"规格型号","sqlName"=>"type","value"=>"无"),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"class_type='Import' and name=:name","sql"=>"2"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"来源地","sqlName"=>"origin","value"=>""),
                    array("name"=>"混合規則","sqlName"=>"rules_id","value"=>"id=:name","sql"=>"5"),
                    array("name"=>"长","sqlName"=>"len","value"=>"0.00"),
                    array("name"=>"宽","sqlName"=>"width","value"=>"0.00"),
                    array("name"=>"高","sqlName"=>"height","value"=>"0.00"),
                    array("name"=>"净重","sqlName"=>"net_weight","value"=>"0.00"),
                    array("name"=>"毛重","sqlName"=>"gross_weight","value"=>"0.00"),
                    array("name"=>"数量倍率","sqlName"=>"multiple","value"=>"1"),
                    array("name"=>"最大数量","sqlName"=>"big_num","value"=>"9999"),
                    array("name"=>"最小数量","sqlName"=>"small_num","value"=>"1"),
                );
                break;
            case "Fast":
                $this->dbName="opr_goods_fa";
                $arr = array(
                    array("name"=>"存货编码","sqlName"=>"goods_code","value"=>"goods_code=:name","sql"=>"1"),
                    array("name"=>"存货名称","sqlName"=>"name","value"=>"name=:name","sql"=>"1"),
                    array("name"=>"规格型号","sqlName"=>"type","value"=>"无"),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"class_type='Fast' and name=:name","sql"=>"2"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"来源地","sqlName"=>"origin","value"=>""),
                    array("name"=>"混合規則","sqlName"=>"rules_id","value"=>"id=:name","sql"=>"5"),
                    array("name"=>"数量倍率","sqlName"=>"multiple","value"=>"1"),
                    array("name"=>"最大数量","sqlName"=>"big_num","value"=>"9999"),
                    array("name"=>"最小数量","sqlName"=>"small_num","value"=>"1"),
                );
                break;
        }
        return $arr;
    }
}
