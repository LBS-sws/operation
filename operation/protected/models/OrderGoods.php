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
}