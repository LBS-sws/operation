<?php

/**
 * Created by PhpStorm.
 * User: 沈超
 * Date: 2017/6/16 0016
 * Time: 上午 11:26
 */
class OrderGoods extends CActiveRecord{

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
    public function tableName()
    {
        return 'opr_order_goods';
    }
    public function primaryKey()
    {
        return 'id';
    }

    public function getArrGoodsClass(){
        return array(
            ""=>"",
            "Import"=>Yii::t("procurement","Import"),      //進口貨
            "Domestic"=>Yii::t("procurement","Domestic"), //國內貨
            "Fast"=>Yii::t("procurement","Fast")           //快速貨
        );
    }
    //訂單狀態發生改變時發送郵件
    public function formEmail($subject,$message,$to_addr = 0,$description=""){
        $uid = Yii::app()->user->id;
        $from_addr = Yii::app()->params['adminEmail'];
        $to_addr = empty($to_addr)?json_encode(array("it@lbsgroup.com.hk")):json_encode($to_addr);
        $description = empty($description)?"訂單通知":$description;
        Yii::app()->db->createCommand()->insert('swoper.swo_email_queue', array(
            'request_dt'=>date('Y-m-d H:i:s'),
            'from_addr'=>$from_addr,
            'to_addr'=>$to_addr,
            'subject'=>$subject,//郵件主題
            'description'=>$description,//郵件副題
            'message'=>$message,//郵件內容（html）
            'status'=>"P",
            'lcu'=>$uid,
            'lcd'=>date('Y-m-d H:i:s'),
        ));

    }
}