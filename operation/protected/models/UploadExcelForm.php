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
            foreach ($validateArr as $vaList){
                $key = array_search($vaList["name"],$arr["listHeader"]);
                $value = $this->validateStr($list[$key],$vaList,$bool);
                if(!$value){
                    $continue = false;
                    break;
                }else{
                    $arrList[$vaList["sqlName"]] = $value;
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
                    $arrList["city"] = $city;
                    Yii::app()->db->createCommand()->insert($this->dbName, $arrList);
                }
                $successNum++;
            }else{
                $errNum++;
            }
        }
        Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Success Num：').$successNum."<br>".Yii::t('procurement','Error Num：').$errNum);
    }

    private function validateStr($value,$list,$bool){
        if(empty($value)&&empty($list["value"])){
            return false;
        }
        if(!empty($list["sql"])){
            $rows = Yii::app()->db->createCommand()->select("*")->from($this->dbName)
                ->where($list["value"], array(':name'=>$value))->queryRow();
            if($list["sql"] == 2){
                if($rows){
                    return $value;
                }else{
                    return false;
                }
            }else{
                if(!$bool){
                    if($rows){
                        $this->add_num = $rows["inventory"];
                        $this->update_id = $rows["id"];
                    }
                    return $value;
                }else{
                    if($rows){
                        return false;
                    }else{
                        return $value;
                    }
                }
            }
        }else{
            if(empty($value)){
                return $list["value"];
            }else{
                return $value;
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
                    array("name"=>"存货编码","sqlName"=>"goods_code","value"=>"city='$city' and goods_code=:name","sql"=>"1"),
                    array("name"=>"存货名称","sqlName"=>"name","value"=>"city='$city' and name=:name","sql"=>"1"),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"classify_id=:name","sql"=>"2"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"安全库存","sqlName"=>"inventory","value"=>""),
                );
                break;
            case "Document":
                $this->dbName="opr_goods_do";
                $arr = array(
                    array("name"=>"存货编码","sqlName"=>"goods_code","value"=>"goods_code=:name","sql"=>"1"),
                    array("name"=>"存货名称","sqlName"=>"name","value"=>"name=:name","sql"=>"1"),
                    array("name"=>"规格型号","sqlName"=>"type","value"=>""),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"classify_id=:name","sql"=>"2"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"来源地","sqlName"=>"origin","value"=>""),
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
                    array("name"=>"规格型号","sqlName"=>"type","value"=>""),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"classify_id=:name","sql"=>"2"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"来源地","sqlName"=>"origin","value"=>""),
                    array("name"=>"长","sqlName"=>"len","value"=>""),
                    array("name"=>"宽","sqlName"=>"width","value"=>""),
                    array("name"=>"高","sqlName"=>"height","value"=>""),
                    array("name"=>"净重","sqlName"=>"net_weight","value"=>""),
                    array("name"=>"毛重","sqlName"=>"gross_weight","value"=>""),
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
                    array("name"=>"规格型号","sqlName"=>"type","value"=>""),
                    array("name"=>"主计量单位","sqlName"=>"unit","value"=>""),
                    array("name"=>"所属分类码","sqlName"=>"classify_id","value"=>"classify_id=:name","sql"=>"2"),
                    array("name"=>"参考售价","sqlName"=>"price","value"=>""),
                    array("name"=>"来源地","sqlName"=>"origin","value"=>""),
                    array("name"=>"数量倍率","sqlName"=>"multiple","value"=>"1"),
                    array("name"=>"最大数量","sqlName"=>"big_num","value"=>"9999"),
                    array("name"=>"最小数量","sqlName"=>"small_num","value"=>"1"),
                );
                break;
        }
        return $arr;
    }
}
