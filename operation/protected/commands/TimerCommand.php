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
        //技术部综合排行榜更新
        $this->resetTechnicianRank();

        echo "end\n";
    }

    //技术部综合排行榜更新
    private function resetTechnicianRank(){
        $year = date("Y");
        $month = date("n");
        $serviceMoneyModel = new ServiceMoneyForm('new');
        $arr = $serviceMoneyModel->curlJobFee($year,$month);//同步U系統的服務金額
        if($arr["code"]==1){
            echo "curl success\n";
        }else{
            echo "curl error\n";
        }
        $model = new RankingMonthForm();
        $model->insertTechnician($year,$month,true);//刷新技術員排行榜
    }
}
?>