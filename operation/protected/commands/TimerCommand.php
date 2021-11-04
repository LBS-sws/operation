<?php
class TimerCommand extends CConsoleCommand {
    public function run() {
        echo "start:\n";
        //外勤領料總覽列表速度優化
        CargoCostList::resetGoodsPrice();
        echo "end\n";
    }
}
?>