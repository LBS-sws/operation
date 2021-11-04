<?php
class TimerCommand extends CConsoleCommand {
    public function run() {
        echo "start:\n";
        //外勤領料總覽列表速度優化
        if(date("Y/m/d")=="2021/11/04"){//測試珠海地區（更新2021年9月的外勤領料金額）
            $year =2021;
            $month=9;
            $city="ZH";
        }else{
            $year="";
            $month="";
            $city="";
        }
        CargoCostList::resetGoodsPrice($year,$month,$city);
        echo "end\n";
    }
}
?>