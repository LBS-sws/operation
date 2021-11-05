<?php
class TimerCommand extends CConsoleCommand {
    public function run() {
        echo "start:\n";
        if(date("Y-m-d")=="2021-11-05"){//由於多次更新或許有遺漏，所以重新更新
            Yii::app()->db->createCommand()->update('opr_warehouse_price', array(
                'new_num'=>1,
            ), "id>0");
        }
        //外勤領料總覽列表速度優化
        CargoCostList::resetGoodsPrice();
        echo "end\n";
    }
}
?>