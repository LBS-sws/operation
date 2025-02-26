<?php
class TimerCommand extends CConsoleCommand {
    public function run() {
        echo "start:".date("Y-m-d")."\n";
        if(date("Y-m-d")=="2021-11-05"){//由於多次更新或許有遺漏，所以重新更新
            Yii::app()->db->createCommand()->update('opr_warehouse_price', array(
                'new_num'=>1,
            ), "id>0");
        }
        //外勤領料總覽列表速度優化
        CargoCostList::resetGoodsPrice();
        //技术部综合排行榜更新
        $this->resetTechnicianRank();
        //技术员综合排行榜数据输入邮件提醒
        $this->hintRankToEmail();

        echo "end\n";
    }

    //技术部综合排行榜更新
    private function resetTechnicianRank(){
        $year = date("Y");
        $month = date("n");
        $day = date("j");
        $serviceMoneyModel = new ServiceMoneyForm('new');
        $serviceMoneyModel->curlJobFee($year,$month);//同步U系統的服務金額
        $model = new RankingMonthForm();
        $model->insertTechnician($year,$month,true);//刷新技術員排行榜
        echo "year:{$year}\n";
        echo "month:{$month}\n";
        echo "day:{$day}\n";
        //还需要刷新上个月的数据
        if($day<=5||($month==5&&$day<=10)||($month==10&&$day<=10)){ //5號以後不刷新上個月的數據
            $month--;
            if($month==0){
                $month=12;
                $year--;
            }
            echo "Last month:{$year}-{$month}\n";
            unset($model);
            $model = new RankingMonthForm();
            $model->insertTechnician($year,$month,true);//刷新技術員排行榜
        }
    }

    //技术员综合排行榜数据输入邮件提醒
    private function hintRankToEmail(){
        $day = date("d");
        if($day==15||$day==28){ //每月15日及28日發郵件
            $suffix = Yii::app()->params['envSuffix'];
            $systemId = Yii::app()->params['systemId'];
            $subject = "技术员综合排行榜数据输入提醒";
            $message = "<p><b>温馨提醒：</b></p>";
            $message.= "<p><b>请各地区负责人及时提醒相关同事处理以下事项，以免影响技术同事当月技术部综合排行榜的分数</b></p>";
            $message.= "<p>1、技术部主管以下级别同事及时申请学分、慈善分，地区老总及时审核</p>";
            $message.= "<p>2、如当月有同事获得表扬信及襟章，请相关同事及时在人事系统-襟章和锦旗列表处登记，表扬信请地区老总及时审核</p>";
            $message.= "<p>3、如当月技术同事有成功介绍新生意额，请通知相关同事及时手动输入到营运系统-技术部综合排行榜-介绍新生意处</p>";
            $message.= "<p>4、如当月技术员有警告信、产生赔偿的客诉、书面的口头警告，请通知相关同事及时手动输入到营运系统-技术部综合排行榜-技术员扣分处</p>";
            $email = new Email($subject,$message,$subject);
            $rows = Yii::app()->db->createCommand()->select("b.email,b.username")->from("security$suffix.sec_city a")
                ->leftJoin("security$suffix.sec_user b","a.incharge=b.username")
                ->where("a.code not in ('XM','CS','H-N')")
                ->queryAll();
            if($rows){
                foreach ($rows as $row){
                    $email->addToAddEmail($row["email"]);
                    $email->addToAddUser($row["username"]);
                }
            }
            $email->sent("營運系統",$systemId);
        }
    }
}
?>