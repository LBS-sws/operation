<?php
//仓库物品的 curl
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
    protected static function getEditGoodsList($good_id,$oldGoodList=array()){
        $sendBool = false;
        $data = self::getGoodDataForID($good_id);
        $updateList = array(
            "inventory"=>"inventory",
            "name"=>"good_name",
            "classify_id"=>"classify_id",
            "unit"=>"unit",
            "display"=>"display",
        );
        if(!empty($oldGoodList)&&!empty($data)){
            foreach ($updateList as $key=>$item){
                if($oldGoodList[$key]!=$data[$item]){
                    $sendBool = true;
                    break;
                }
            }
            if($sendBool){//物料被修改
                $data["curl_type"]="edit";
                $data["old_inventory"]=$oldGoodList["inventory"];
                $data["change_inventory"]=$data["inventory"]-$oldGoodList["inventory"];
            }
        }

        return array("sendBool"=>$sendBool,"data"=>$data);
    }

    //物品修改
    public static function editGood($good_id,$oldGoodList=array()){
        $editList = self::getEditGoodsList($good_id,$oldGoodList);
        if($editList["sendBool"]){//有数据变动
            //curl发送数据
            parent::sendDataForJD($editList["data"],"/warehouse/changeGoodOne");
        }
    }

    //物品修改(批量)
    public static function editGoodFull($oldGoodList=array()){
        if(!empty($oldGoodList)){
            $sendList = array();
            foreach ($oldGoodList as $good_id=>$good_list){
                $editList = self::getEditGoodsList($good_id,$good_list);
                if($editList["sendBool"]){//有数据变动
                    $sendList[]=$editList["data"];
                }
            }
            if(!empty($sendList)){
                //curl发送数据
                parent::sendDataForJD($sendList,"/warehouse/changeGoodFull");
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
        $classifyList = Yii::app()->db->createCommand()->select("name,jd_classify_no")->from("opr_classify")
            ->where("id=:id",array(":id"=>$row["classify_id"]))->queryRow();
        $data=array(
            "curl_type"=>"",
            "lbs_id"=>$row["id"],//物料id
            "timestamp"=>date_format(date_create(),"Y-m-d H:i:s"),//操作时间
            "good_no"=>$row["goods_code"],//物料编号
            "good_name"=>$row["name"],//物料名称
            "classify_id"=>$row["classify_id"],//分类id
            "city"=>$row["city"],//城市code
            "city_name"=>CGeneral::getCityName($row["city"]),//城市code
            "classify_name"=>$classifyList?$classifyList["name"]:"",//分类名称
            "unit"=>$row["unit"],//单位
            "costing"=>$row["costing"],//成本
            "inventory"=>$row["inventory"],//库存
            "old_inventory"=>isset($row["old_inventory"])?$row["old_inventory"]:0,//旧库存
            "change_inventory"=>isset($row["change_inventory"])?$row["change_inventory"]:$row["inventory"],//变更库存
            "min_num"=>$row["min_num"],//安全库存
            "display"=>$row["display"],//是否显示
            //"jd_warehouse_no"=>$row["jd_warehouse_no"],
            "jd_good_no"=>$row["jd_good_no"],
            "jd_classify_no"=>$classifyList?$classifyList["jd_classify_no"]:"",
            "update_username"=>Yii::app()->user->user_display_name(),
        );
        return $data;
    }
}
