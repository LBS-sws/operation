<?php
//外勤领料的curl
class CurlForDelivery extends CurlForJD{
    protected $info_type="delivery";

    protected function saveJDData($rtn){//保存成功的金蝶id
        if($rtn['code']==200){
            $out = $rtn['outData'];
            $json = json_decode($out, true);
            foreach ($json["data"]["result"] as $row){
                $orderCode = $row["keys"]["lbs_apikey"];
                $jd_order_code = $row["number"];//2024年7月26日14:09:16改为保存编号
                $lbs_order_id = Yii::app()->db->createCommand()->select("id")
                    ->from("opr_order")
                    ->where("order_code=:code AND judge=0",array(':code'=>$orderCode))
                    ->queryRow();
                if($lbs_order_id){
                    $rs = Yii::app()->db->createCommand()->select("id,field_id")->from("opr_send_set_jd")
                        ->where("set_type ='technician' and table_id=:table_id and field_id=:field_id",array(
                            ':field_id'=>"jd_order_code",':table_id'=>$lbs_order_id["id"],
                        ))->queryRow();
                    if($rs){
                        Yii::app()->db->createCommand()->update('opr_send_set_jd',array(
                            "field_value"=>$jd_order_code
                        ),"id=:id",array(':id'=>$rs["id"]));
                    }else{
                        Yii::app()->db->createCommand()->insert('opr_send_set_jd',array(
                            "table_id"=>$lbs_order_id["id"],
                            "set_type"=>'technician',
                            "field_id"=>"jd_order_code",
                            "field_value"=>$jd_order_code,
                            "lcd"=>date_format(date_create(),"Y/m/d H:i:s"),
                        ));
                    }
                }
            }
        }
    }

    protected function resetJDData($data){
        $list=array("data"=>array());
        if(!empty($data["data"])){
            foreach ($data["data"] as $row){
                $jdCity = self::getJDCityCodeForCity($row["city"]);
                $backBool = isset($row["back_type"])&&$row["back_type"]==1;
                $employeeList = self::getEmployeeCodeForUsername($row["order_user"]);
                $temp=array(
                    "id"=>0,//
                    "org_number"=>$jdCity,//库存组织.编码
                    "billtype_number"=>"im_MaterialReqOutBill_STD_BT_S",//单据类型.编码
                    "biztype_number"=>self::getJDBizType($row),//业务类型.编码
                    "invscheme_number"=>self::getJDInvscheme($row),//库存事务.编码
                    "biztime"=>$row["apply_date"],//业务日期
                    "bizorg_number"=>$jdCity,//需求组织.编码
                    "supplyownertype"=>"bos_org",//货主类型
                    "supplyowner_number"=>$jdCity,//供应货主.编码
                    "bookdate"=>$row["apply_date"],//记账日期
                    "lbs_lbswarehouser"=>$row["luu_name"],//LBS库管员
                    "settlecurrency_number"=>"CNY",//币别.货币代码
                    "comment"=>$row["remark"],//备注
                    "lbs_apikey"=>$row["order_code"],//第三方单据标识
                    "billentry"=>array(),//物料明细
                );
                if(!empty($employeeList)){
                    $temp["requser_number"]=$employeeList["code"];//领用人.工号
                    $temp["lbs_lbsapplierdept"]=$employeeList["department_name"];//申请人部门
                }
                if($row["jd_order_type"]==1){//销售出库
                    $temp["lbs_customer_number"]=$row["jd_company_code"];//客户编码
                }
                if($backBool){//退库时的对应ERP出库单据编号
                    $jd_order_code = TechnicianList::getJDOrderTypeForId($row["order_id"],"jd_order_code");
                    $temp["lbs_erpsrcbillno"]=empty($jd_order_code)?null:$jd_order_code;//
                }
                if(!empty($row["goods_item"])){
                    foreach ($row["goods_item"] as $goodRow){
                        $qty = $backBool?(-1*$goodRow["back_num"]):$goodRow["confirm_num"];
                        $temp["billentry"][]=array(
                            "linetype_number"=>"010",//行类型.编码
                            "material_number"=>$goodRow["goods_code"],//物料编码.编码
                            "unit_number"=>$goodRow["jd_unit_code"],//计量单位.编码
                            "qty"=>$qty,//物料明细.数量
                            "warehouse_number"=>$goodRow["jd_store_no"],//仓库.编码
                            "outinvtype_number"=>"110",//出库库存类型.编码
                            "outinvstatus_number"=>"110",//出库库存状态.编码
                            "outownertype"=>"bos_org",//物料明细.出库货主类型
                            "outowner_number"=>$jdCity,//出库货主.编码
                            "outkeepertype"=>"bos_org",//物料明细.出库保管者类型
                            "outkeeper_number"=>$jdCity,//出库保管者.编码
                            "entrycomment"=>$goodRow["note"],//物料明细.备注
                            "lbs_sendgooddesc"=>$goodRow["remark"],//物料发货说明.备注
                            "lbs_eapikey"=>$goodRow["lbs_order_store_id"],//物料明细.第三方明细标识
                        );
                    }
                }
                $list["data"][]=$temp;
            }
        }
        return $list;
    }

    protected function getJDBizType($row){
        switch ($row["jd_order_type"]){
            case 0://领料单
                if(isset($row["back_type"])&&$row["back_type"]==1){//退货
                    return "3201";
                }else{
                    return "320";
                }
                break;
            case 1://销售出库
                if(isset($row["back_type"])&&$row["back_type"]==1){//退货
                    return "LBS3211";
                }else{
                    return "LBS321";
                }
                break;
        }
        return "";
    }

    protected function getJDInvscheme($row){
        switch ($row["jd_order_type"]){
            case 0://领料单
                if(isset($row["back_type"])&&$row["back_type"]==1){//退货
                    return "3201";
                }else{
                    return "320";
                }
                break;
            case 1://销售出库
                if(isset($row["back_type"])&&$row["back_type"]==1){//退货
                    return "LBS002";
                }else{
                    return "LBS001";
                }
                break;
        }
        return "";
    }

    ///kapi/v2/lbs/im/im_materialreqoutbill/save
    //审核单个外勤领料
    public function sendJDCurlForOne($curlData){
        $data = array("data"=>array($curlData));
        $data = self::resetJDData($data);
        $rtn = $this->sendData($data,"/kapi/v2/lbs/im/im_materialreqoutbill/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        $this->saveJDData($rtn);
        return $rtn;
    }

    //批量审核外勤领料
    public function sendJDCurlForFull($curlData){
        $data = array("data"=>$curlData);
        $data = self::resetJDData($data);
        $rtn = $this->sendData($data,"/kapi/v2/lbs/im/im_materialreqoutbill/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        $this->saveJDData($rtn);
        return $rtn;
    }

    //退回单个外勤领料
    public function backJDCurlForGoods($curlData){
        $curlData["back_type"]=1;
        $data = array("data"=>array($curlData));
        $data = self::resetJDData($data);
        $rtn = $this->sendData($data,"/kapi/v2/lbs/im/im_materialreqoutbill/save");
        //$rtn = array('message'=>'', 'code'=>200,'outData'=>'');//成功时code=200；
        return $rtn;
    }

    //批量退回外勤领料
    public function backJDCurlForOrder($curlData){
        $curlData["back_type"]=1;
        $data = array("data"=>array($curlData));
        $data = self::resetJDData($data);
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
                if(key_exists("material_number",$row)){
                    $row["material_number"]="".$row["material_number"];
                    if(!key_exists($row["material_number"],$list)){
                        $list[$row["material_number"]]=array(
                            "jd_good_no"=>$row["material_number"],//物品编号
                            "jd_good_name"=>$row["material_name"],//物品名称
                            "jd_store_sum"=>0,//库存总量
                            "jd_good_text"=>"",
                            "jd_warehouse_list"=>array(),
                        );
                    }
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

    public static function getEmployeeCodeForUsername($username){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("b.code,b.name,f.name as department_name")
            ->from("hr{$suffix}.hr_binding a")
            ->leftJoin("hr{$suffix}.hr_employee b","a.employee_id=b.id")
            ->leftJoin("hr{$suffix}.hr_dept f","b.department=f.id")
            ->where("a.user_id=:user_id",array(':user_id'=>$username))
            ->queryRow();
        if($row){
            return $row;
        }else{
            return array();
        }
    }
}
