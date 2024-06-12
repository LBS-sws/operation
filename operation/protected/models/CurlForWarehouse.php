<?php
//仓库物品的curl
class CurlForWareHouse extends CurlForJD{

    //物品新增(所有物品)
    public static function addGoodAll(){
        $rows = Yii::app()->db->createCommand()->select("*")->from("opr_warehouse")
            ->where("id>0")->queryAll();
        $data = array();
        if($rows){
            foreach ($rows as $row){
                $temp = self::getDataForRow($row);
                $temp["curl_type"]="add";
                $data[]=$temp;
            }
            //curl发送数据
            parent::sendDataForJD($data,"/warehouse/addGoodAll");
        }
    }

    //物品新增(城市内所有物品)
    public static function addGoodForCity($city){
        $rows = Yii::app()->db->createCommand()->select("*")->from("opr_warehouse")
            ->where("city=:city",array(":city"=>$city))->queryAll();
        $data = array();
        if($rows){
            foreach ($rows as $row){
                $temp = self::getDataForRow($row);
                $temp["curl_type"]="add";
                $data[]=$temp;
            }
            //curl发送数据
            parent::sendDataForJD($data,"/warehouse/addGoodForCity");
        }
    }

    //物品新增
    public static function addGood($good_id){
        $data = self::getGoodDataForID($good_id);
        if(!empty($data)){
            $data["curl_type"]="add";
            //curl发送数据
            parent::sendDataForJD($data,"/warehouse/addGoodOne");
        }
    }

    //物品修改
    public static function editGood($good_id,$oldGoodList=array()){
        $oldInventory = key_exists("inventory",$oldGoodList)?floatval($oldGoodList["inventory"]):0;
        $data = self::getGoodDataForID($good_id);
        if(!empty($data)){
            if($oldInventory!=$data["inventory"]){//库存不一致
                $data["curl_type"]="edit";
                //curl发送数据
                parent::sendDataForJD($data,"/warehouse/editGoodOne");
            }
        }
    }

    protected static function getGoodDataForID($good_id){
        $row = Yii::app()->db->createCommand()->select("*")->from("opr_warehouse")
            ->where("id=:id",array(":id"=>$good_id))->queryRow();
        if($row){
            return self::getDataForRow($row);
        }
        return array();
    }

    protected static function getDataForRow($row){
        $data=array(
            "lbs_id"=>$row["id"],//物料id
            "lbs_good_no"=>$row["goods_code"],//物料编号
            "lbs_good_name"=>$row["name"],//物料名称
            "classify_id"=>$row["classify_id"],//分类id
            "city"=>$row["city"],//城市code
            "city_name"=>CGeneral::getCityName($row["city"]),//城市code
            "classify_name"=>ClassifyForm::getClassifyToId($row["classify_id"]),//分类名称
            "unit"=>$row["unit"],//单位
            "costing"=>is_numeric($row["costing"])?floatval($row["costing"]):0,//成本
            "inventory"=>is_numeric($row["inventory"])?floatval($row["inventory"]):0,//库存
            "min_num"=>is_numeric($row["min_num"])?floatval($row["min_num"]):0,//安全库存
            "jd_warehouse_no"=>$row["jd_warehouse_no"],
            "jd_good_no"=>$row["jd_good_no"],
        );
        return $data;
    }
}
