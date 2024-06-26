<?php
//外勤领料的curl
class CurlForDelivery extends CurlForJD{
    protected $info_type="delivery";

    ///kapi/v2/lbs/im/im_materialreqoutbill/save
    //审核单个外勤领料
    public function sendJDCurlForOne($curlData){
        $data = array("data"=>array($curlData));
        $rtn = $this->sendData($data,"/kapi/v2/lbs/im/im_materialreqoutbill/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //批量审核外勤领料
    public function sendJDCurlForFull($curlData){
        $data = array("data"=>$curlData);
        $rtn = $this->sendData($data,"/kapi/v2/lbs/im/im_materialreqoutbill/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //退回单个外勤领料
    public function backJDCurlForGoods($curlData){
        $data = array("data"=>array($curlData));
        $rtn = $this->sendData($data,"/kapi/v2/lbs/im/im_materialreqoutbill/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //批量退回外勤领料
    public function backJDCurlForOrder($curlData){
        $data = array("data"=>array($curlData));
        $rtn = $this->sendData($data,"/kapi/v2/lbs/im/im_materialreqoutbill/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //获取金蝶系统的物料详情
    public static function getWarehouseGoodsForJD($curlData){
        $data = $curlData;
        $rtn = self::getData($data,"/kapi/v2/lbs/im/getInventoryAvbQty");
        return $rtn;
    }

    //获取金蝶系统的物料详情(库存情况)
    public static function getWarehouseGoodsStoreForJD($curlData){
        $list = array();
        $data = $curlData;
        $jdData = self::getData($data,"/kapi/v2/lbs/im/getInventoryAvbQty");
        if($jdData["code"]==200){
            foreach ($jdData["outData"] as $row){
                $row["material_number"]="".$row["material_number"];
                if(!key_exists($row["material_number"],$list)){
                    $list[$row["material_number"]]=array(
                        "jd_good_no"=>$row["material_number"],//物品编号
                        "jd_good_name"=>$row["material_name"],//物品名称
                        "jd_store_sum"=>0,//库存总量
                        "jd_good_text"=>"",
                        "jd_warehouse_list"=>array(),
                    );
                    $list[$row["material_number"]]["jd_store_sum"]+=$row["qty"];
                    $list[$row["material_number"]]["jd_warehouse_list"][$row["warehouse_number"]]=$row;
                    $list[$row["material_number"]]["jd_good_text"].=empty($list[$row["material_number"]]["jd_good_text"])?"":"\n";
                    $list[$row["material_number"]]["jd_good_text"].="仓库({$row["warehouse_number"]}):库存(".$row["qty"].");";//
                }
            }
        }
        return $list;
    }

    public static function getJDCityCodeForCity($city){
        $suffix = Yii::app()->params['envSuffix'];
        $list = Yii::app()->db->createCommand()->select("field_value")
            ->from("security{$suffix}.sec_city_info")
            ->where("code=:code and field_id='JD_city'",array(':code'=>$city))
            ->queryRow();
        if($list){
            return $list["field_value"];
        }else{
            return "";
        }
    }
}
