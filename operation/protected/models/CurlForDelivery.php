<?php
//外勤领料的curl
class CurlForDelivery extends CurlForJD{
    protected $info_type="delivery";

    //审核单个外勤领料
    public function sendJDCurlForOne($curlData){
        $data = $curlData;
        $rtn = $this->sendData($data,"/delivery/sendJDCurlForOne");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //批量审核外勤领料
    public function sendJDCurlForFull($curlData){
        $data = $curlData;
        $rtn = $this->sendData($data,"/delivery/sendJDCurlForFull");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //退回单个外勤领料
    public function backJDCurlForGoods($curlData){
        $data = $curlData;
        $rtn = $this->sendData($data,"/delivery/backJDCurlForGoods");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //批量退回外勤领料
    public function backJDCurlForOrder($curlData){
        $data = $curlData;
        $rtn = $this->sendData($data,"/delivery/backJDCurlForOrder");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //获取金蝶系统的物料详情
    public static function getWarehouseGoodsForJD($curlData){
        $data = $curlData;
        $rtn = self::getData($data,"/kapi/v2/lbs/im/getInventoryAvbQty");
        return $rtn;
    }
}
