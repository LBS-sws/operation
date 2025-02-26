<?php

class RankingMonthForm extends CFormModel
{
	/* User Fields */
	public $id;
	public $rank;//排名
	public $name;
	public $employee_id;
	public $employee_code;
	public $employee_name;
	public $city;
	public $rank_year;
	public $rank_month;
	public $score_sum;
	public $other_score;
    public $year_type=0;//考核详情专用

	public $startDate;
	public $endDate;

	public $lud;

	public $arrList;//数据列表

    //由于后续添加了夜单及创新服务需要改成判断(2023/01/01开始）
    public static function getSqlDate($rankYear,$rankMonth){
        $arrOne=array( //add:是否疊加  reset:月排行是否显示
            "integral_num"=>array("add"=>true,"reset"=>true,"label"=>"integral score","otherRank"=>true),//學分
            "charity_num"=>array("add"=>true,"reset"=>true,"label"=>"charity score","otherRank"=>true),//慈善分
            "pin_num"=>array("add"=>false,"reset"=>true,"label"=>"pin score","otherRank"=>true),//襟章得分
            "service_num"=>array("add"=>true,"reset"=>true,"label"=>"service score"),//服務得分
        );
        if(strtotime("{$rankYear}/{$rankMonth}/01")>=strtotime("2023/01/01")){
            $arrOne["night_num"]=array("add"=>true,"reset"=>true,"label"=>"night score");//夜单得分
            $arrOne["create_num"]=array("add"=>true,"reset"=>true,"label"=>"create score");//创新服务得分
        }
        $arrTwo=array(
            "prize_num"=>array("add"=>true,"reset"=>true,"label"=>"prize score","otherRank"=>true),//表揚信得分
            "complain_num"=>array("add"=>true,"reset"=>true,"label"=>"complain score","otherRank"=>true,"minName"=>"follow-up score"),//客诉跟进得分
            "quality_num"=>array("add"=>true,"reset"=>true,"label"=>"quality score","otherRank"=>true,"minName"=>"user quality score"),//質檢得分
            "review_num"=>array("add"=>false,"reset"=>false,"label"=>"review score","otherRank"=>true),//考核得分
            "letter_num"=>array("add"=>true,"reset"=>true,"label"=>"letter score","otherRank"=>true),//心意信得分
            "recommend_num"=>array("add"=>true,"reset"=>true,"label"=>"recommend score","otherRank"=>true),//推薦人得分
            "two_num"=>array("add"=>false,"reset"=>true,"label"=>"two score"),//兩項得分
            "new_num"=>array("add"=>true,"reset"=>true,"label"=>"new score","otherRank"=>true),//介绍新生意得分
            "sales_num"=>array("add"=>true,"reset"=>true,"label"=>"sales score","otherRank"=>true),//洗地易销售得分
            "deduct_num"=>array("add"=>true,"reset"=>true,"label"=>"deduct score"),//扣分分數
        );
        return array_merge($arrOne,$arrTwo);
    }

    //學分
    private function integral_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $score=Yii::app()->db->createCommand()->select("sum(credit_point)")->from("spoint{$suffix}.gr_credit_request")
            ->where("state=3 and apply_date between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score)*50:0;
    }

    //學分table
    private function integral_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $rows=Yii::app()->db->createCommand()->select("b.credit_code,b.credit_name,a.id,a.credit_point,a.apply_date")
            ->from("spoint{$suffix}.gr_credit_request a")
            ->leftJoin("spoint{$suffix}.gr_credit_type b","a.credit_type=b.id")
            ->where("a.state=3 and a.apply_date between '$this->startDate' and '$this->endDate' and a.employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>学分名称</th><th>申请时间</th><th>学分数值</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $score = floatval($row["credit_point"])*50;
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['credit_code']}-{$row['credit_name']}</td><td>{$row['apply_date']}</td><td>{$row['credit_point']}</td><td class='text-right'>{$score}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //慈善分
    private function charity_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $score=Yii::app()->db->createCommand()->select("sum(credit_point)")->from("charity{$suffix}.cy_credit_request")
            ->where("state=3 and apply_date between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score)*500:0;
    }

    //慈善分table
    private function charity_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $rows=Yii::app()->db->createCommand()->select("b.charity_code,b.charity_name,a.id,a.credit_point,a.apply_date")
            ->from("charity{$suffix}.cy_credit_request a")
            ->leftJoin("charity{$suffix}.cy_credit_type b","a.credit_type=b.id")
            ->where("a.state=3 and a.apply_date between '$this->startDate' and '$this->endDate' and a.employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>慈善分名称</th><th>申请时间</th><th>慈善分数值</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $score = floatval($row["credit_point"])*500;
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['charity_code']}-{$row['charity_name']}</td><td>{$row['apply_date']}</td><td>{$row['credit_point']}</td><td class='text-right'>{$score}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //襟章得分
    private function pin_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $score=Yii::app()->db->createCommand()->select("inventory_id")->from("hr{$suffix}.hr_pin")
            ->where("apply_date <= '$this->endDate' and employee_id=:id",
                array(":id"=>$employee_id))->group("inventory_id")->queryAll();
        return $score?count($score)*50:0;
    }

    //襟章得分table
    private function pin_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $rows=Yii::app()->db->createCommand()->select("inventory_id,min(id) as id")->from("hr{$suffix}.hr_pin")
            ->where("apply_date <= '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->group("inventory_id")->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>登记编号</th><th>获章日期</th><th>襟章名称</th><th>襟章数量</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $row = Yii::app()->db->createCommand()->select("a.id,a.pin_code,a.apply_date,a.pin_num,b.name")->from("hr{$suffix}.hr_pin a")
                    ->leftJoin("hr{$suffix}.hr_pin_inventory c","a.inventory_id=c.id")
                    ->leftJoin("hr{$suffix}.hr_pin_name b","c.pin_name_id=b.id")
                ->where("a.id=:id",array(":id"=>$row["id"]))->queryRow();
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['pin_code']}</td><td>{$row['apply_date']}</td><td>{$row['name']}</td><td>{$row['pin_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //服務得分
    private function service_num($year,$month,$employee_id){
        $score=Yii::app()->db->createCommand()->select("sum(score_num)")->from("opr_service_money")
            ->where("service_year=:year and service_month=:month and employee_id=:id",
                array(":year"=>$year,":month"=>$month,":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score):0;
    }

    //服務得分table
    private function service_num_table(){
        $rows=Yii::app()->db->createCommand()->select("service_code,service_year,id,service_month,service_money,score_num")
            ->from("opr_service_money")
            ->where("service_date between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>同步编号</th><th>时间</th><th>服务金额</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['service_code']}</td><td>{$row['service_year']}年{$row['service_month']}月</td><td class='text-right'>{$row['service_money']}</td><td class='text-right'>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //夜单得分
    private function night_num($year,$month,$employee_id){
        $score=Yii::app()->db->createCommand()->select("sum(night_score)")->from("opr_service_money")
            ->where("service_year=:year and service_month=:month and employee_id=:id",
                array(":year"=>$year,":month"=>$month,":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score):0;
    }

    //夜单得分table
    private function night_num_table(){
        $rows=Yii::app()->db->createCommand()->select("service_code,service_year,id,service_month,night_money,night_score")
            ->from("opr_service_money")
            ->where("service_date between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>同步编号</th><th>时间</th><th>服务金额</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['service_code']}</td><td>{$row['service_year']}年{$row['service_month']}月</td><td class='text-right'>{$row['night_money']}</td><td class='text-right'>{$row['night_score']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //创新服务得分
    private function create_num($year,$month,$employee_id){
        $score=Yii::app()->db->createCommand()->select("sum(create_score)")->from("opr_service_money")
            ->where("service_year=:year and service_month=:month and employee_id=:id",
                array(":year"=>$year,":month"=>$month,":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score):0;
    }

    //创新服务得分table
    private function create_num_table(){
        $rows=Yii::app()->db->createCommand()->select("service_code,service_year,id,service_month,create_money,create_score")
            ->from("opr_service_money")
            ->where("service_date between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>同步编号</th><th>时间</th><th>服务金额</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['service_code']}</td><td>{$row['service_year']}年{$row['service_month']}月</td><td class='text-right'>{$row['create_money']}</td><td class='text-right'>{$row['create_score']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //表揚信得分
    private function prize_num($year,$month,$employee_id){
        //錦旗的日期由嘉許日期改成錄入日期（2022-09-16修改）prize_date改成lcd
        $suffix = Yii::app()->params['envSuffix'];
        $score=Yii::app()->db->createCommand()->select("count(id)")->from("hr{$suffix}.hr_prize")
            ->where("status=3 and lcd between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score)*100:0;
    }

    //表揚信得分table
    private function prize_num_table(){
        //錦旗的日期由嘉許日期改成錄入日期（2022-09-16修改）prize_date改成lcd
        $suffix = Yii::app()->params['envSuffix'];
        $rows=Yii::app()->db->createCommand()->select("id,lcd,prize_pro,prize_type")->from("hr{$suffix}.hr_prize")
            ->where("status=3 and lcd between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>嘉许项目</th><th>客户奖励</th><th>录入日期</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            $list = array(
                1=>"清洁",
                2=>"灭虫",
                3=>"清洁灭虫",
            );
            foreach ($rows as $row){
                $row["prize_pro"] = key_exists($row["prize_pro"],$list)?$list[$row["prize_pro"]]:"";
                $row["prize_type"] = $row["prize_type"]==1?"锦旗":"表扬信";
                $row["lcd"] = CGeneral::toMyDate($row["lcd"]);
                $row["score_num"] = 100;
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['prize_pro']}</td><td>{$row['prize_type']}</td><td>{$row['lcd']}</td><td>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //客诉跟进得分
    private function complain_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $name = $this->employee_name." ({$this->employee_code})";
        $score=Yii::app()->db->createCommand()->select("count(id)")->from("swoper{$suffix}.swo_followup")
            ->where("entry_dt between '$this->startDate' and '$this->endDate' and follow_staff like '%{$name}%' and resp_tech not like '%{$name}%'"
            )->queryScalar();
        return is_numeric($score)?floatval($score)*100:0;
    }

    //客诉跟进得分table
    private function complain_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $name = $this->employee_name." ({$this->employee_code})";
        $rows=Yii::app()->db->createCommand()->select("id,company_name,entry_dt,resp_tech,follow_staff")->from("swoper{$suffix}.swo_followup")
            ->where("entry_dt between '$this->startDate' and '$this->endDate' and follow_staff like '%{$name}%' and resp_tech not like '%{$name}%'"
            )->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>客诉日期</th><th>客户名称</th><th>负责技术员</th><th>跟进(此投诉)技术员</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $row['score_num']=100;
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['entry_dt']}</td><td>{$row['company_name']}</td><td>{$row['resp_tech']}</td><td>{$row['follow_staff']}</td><td>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='7'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //質檢得分
    private function quality_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $name = " ".$this->employee_name." ({$this->employee_code})";
        $row=Yii::app()->db->createCommand()->select("count(id) as sum_qc,AVG(qc_result) as avg_qc")->from("swoper{$suffix}.swo_qc")
            ->where("qc_dt between '$this->startDate' and '$this->endDate' and job_staff=:name",
                array(":name"=>$name))->queryRow();
        if($row){
            $list = $this->qualityScoreCompute($row["sum_qc"],$row["avg_qc"]);
            return $list["score"];
        }
        return 0;
    }

    //質檢得分table
    private function quality_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $name = " ".$this->employee_name." ({$this->employee_code})";
        $rows=Yii::app()->db->createCommand()->select("id,qc_dt,qc_staff,qc_result,company_name,lcd,lud")->from("swoper{$suffix}.swo_qc")
            ->where("qc_dt between '$this->startDate' and '$this->endDate' and job_staff=:name",
                array(":name"=>$name))->queryAll();
        $count = 0;
        $avg = 0;
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>质检日期</th><th>客户名称</th><th>质检部同事</th><th>	质检成绩</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $avg+=floatval($row["qc_result"]);
                $count++;
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td data-lcd='{$row['lcd']}' data-lud='{$row['lud']}'>{$row['qc_dt']}</td><td>{$row['company_name']}</td><td>{$row['qc_staff']}</td><td>{$row['qc_result']}</td></tr>";
            }
            $avg = $avg/$count;
            $list = $this->qualityScoreCompute($count,$avg);
            $html.= "</tbody><tfoot><tr><td colspan='5' class='text-right'>公式：{$list['compute']}</td><td>得分：{$list['score']}</td></tr></tfoot>";
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
            $html.= "</tbody>";
        }

        return $html;
    }

    //考核得分
    private function review_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $year_type = $month>6?2:1;
        $row=Yii::app()->db->createCommand()->select("review_sum")->from("hr{$suffix}.hr_review")
            ->where("year_type='{$year_type}' and  year='{$year}' and status_type=3 and employee_id=:id",
                array(":id"=>$employee_id))->queryRow();
        if($row){//考核成绩已出现
            return $this->reviewScore($row["review_sum"]);
        }else{//没有考核成绩刷新以前的成绩
            if($month>6){
                $year_type=1;
            }else{
                $year--;
                $year_type=2;
            }
            $row=Yii::app()->db->createCommand()->select("review_sum")->from("hr{$suffix}.hr_review")
                ->where("year_type='{$year_type}' and  year='{$year}' and status_type=3 and employee_id=:id",
                    array(":id"=>$employee_id))->queryRow();
            if($row){
                $monthList=$year_type==1?array(1,2,3,4,5,6):array(7,8,9,10,11,12);
                $monthList = implode(",",$monthList);
                $review_num = $this->reviewScore($row["review_sum"]);
                Yii::app()->db->createCommand()->update("opr_technician_rank",array(
                    "review_num"=>$review_num
                ),"employee_id=:id and rank_year='$year' and rank_month in ({$monthList})",array(":id"=>$employee_id));
            }
            return 0;
        }
    }

    //考核得分table
    private function review_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $sql="";
        if(!empty($this->year_type)){
            $sql="year_type='$this->year_type' and ";
        }
        $rows=Yii::app()->db->createCommand()->select("id,year_type,review_sum")->from("hr{$suffix}.hr_review")
            ->where("{$sql} year='$this->rank_year' and status_type=3 and employee_id=:id",
                array(":id"=>$this->employee_id))->order("year_type asc")->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>年份</th><th>月份</th><th>评核总得分</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $row["year_type"]=$row["year_type"]==1?"1-6月":"7-12月";
                $row["score_num"]=$this->reviewScore($row["review_sum"]);
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$this->rank_year}</td><td>{$row['year_type']}</td><td>{$row['review_sum']}</td><td>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
            $html.= "</tbody>";
        }

        return $html;
    }

    //心意信得分
    private function letter_num($year,$month,$employee_id){
        $score = 0;
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("letter_num")->from("hr{$suffix}.hr_letter")
            ->where("state=4 and employee_id=:id and lud between '$this->startDate' and '$this->endDate'",
                array(":id"=>$employee_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                if ($row["letter_num"]==1){
                    $score+=50;
                }elseif ($row["letter_num"]==2){
                    $score+=100;
                }elseif ($row["letter_num"]==3){
                    $score+=200;
                }
            }
        }
        return $score;
    }

    //心意信得分table
    private function letter_num_table(){
        $score = 0;
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("letter_num,id,lud,letter_title")->from("hr{$suffix}.hr_letter")
            ->where("state=4 and employee_id=:id and lud between '$this->startDate' and '$this->endDate'",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>标题</th><th>审核日期</th><th>审核得分</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            $list=array(
                1=>50,
                2=>100,
                3=>200,
            );
            foreach ($rows as $row){
                $row['score_num']=key_exists($row["letter_num"],$list)?$list[$row["letter_num"]]:0;
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['letter_title']}</td><td>{$row['lud']}</td><td>{$row['letter_num']}</td><td class='text-right'>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //推薦人得分
    private function recommend_num($year,$month,$employee_id){
        //新入职的员工必须满足：1、职位类别：服务 2、技术员：是 3、评核类型：技术员（2022-10-20新加的逻辑）
        $startDate = date("Y/m/d",strtotime($this->startDate."-3 months"));
        $endDate = date("Y/m/d",strtotime($this->endDate."-3 months"));
        $date = date("Y/m/d",strtotime($this->endDate));
        $suffix = Yii::app()->params['envSuffix'];
        $score = Yii::app()->db->createCommand()->select("count(a.id)")
            ->from("hr{$suffix}.hr_employee a")
            ->leftJoin("hr{$suffix}.hr_dept b","a.position=b.id")
            ->where("b.dept_class='Technician' and b.technician=1 and b.review_type=2 and a.recommend_user=:id 
            and replace(a.entry_time,'-', '/') between '$startDate' and '$endDate'
            and TIMESTAMPDIFF(MONTH,a.entry_time,if(a.staff_status = -1,a.leave_time,'{$date}'))>=3
            ",array(":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score)*500:0;
    }

    //推薦人得分table
    private function recommend_num_table(){
        $startDate = date("Y/m/d",strtotime($this->startDate."-3 months"));
        $endDate = date("Y/m/d",strtotime($this->endDate."-3 months"));
        $date = date("Y/m/d",strtotime($this->endDate));
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.id,a.code,a.name,a.entry_time")
            ->from("hr{$suffix}.hr_employee a")
            ->leftJoin("hr{$suffix}.hr_dept b","a.position=b.id")
            ->where("b.dept_class='Technician' and b.technician=1 and b.review_type=2 and a.recommend_user=:id 
            and replace(a.entry_time,'-', '/') between '$startDate' and '$endDate'
            and TIMESTAMPDIFF(MONTH,a.entry_time,if(a.staff_status = -1,a.leave_time,'{$date}'))>=3
            ",array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>推荐人编号</th><th>推荐人姓名</th><th>员工编号</th><th>员工姓名</th><th>入职时间</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['code']}</td><td>{$row['name']}</td><td>{$row['entry_time']}</td><td>500</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //兩項得分
    private function two_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $twoRows=Yii::app()->db->createCommand()->select("id")->from("hr{$suffix}.hr_pin_name")
            ->where("pin_type = 1")->queryAll();
        if($twoRows){
            $score = true;
            foreach ($twoRows as $row){
                $bool=Yii::app()->db->createCommand()->select("a.id")->from("hr{$suffix}.hr_pin a")
                    ->leftJoin("hr{$suffix}.hr_pin_inventory b","a.inventory_id=b.id")
                    ->where("a.apply_date <= '$this->endDate' and a.employee_id=:id and b.pin_name_id='{$row["id"]}'",
                        array(":id"=>$employee_id))->queryRow();
                if(!$bool){
                    $score = false;
                    break;
                }
            }
            return $score?300:0;
        }else{
            return 0;
        }
    }

    //兩項得分table
    private function two_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $twoRows=Yii::app()->db->createCommand()->select("id")->from("hr{$suffix}.hr_pin_name")
            ->where("pin_type = 1")->queryAll();
        $rows=Yii::app()->db->createCommand()->select("inventory_id,min(id) as id")->from("hr{$suffix}.hr_pin")
            ->where("apply_date <= '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->group("inventory_id")->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>登记编号</th><th>获章日期</th><th>襟章名称</th><th>襟章数量</th></tr>";
        $html.= "</thead><tbody>";
        if($twoRows){
            $score = "";
            foreach ($twoRows as $twoRow){
                $row=Yii::app()->db->createCommand()->select("a.id,a.pin_code,a.apply_date,a.pin_num,c.name")->from("hr{$suffix}.hr_pin a")
                    ->leftJoin("hr{$suffix}.hr_pin_inventory b","a.inventory_id=b.id")
                    ->leftJoin("hr{$suffix}.hr_pin_name c","b.pin_name_id=c.id")
                    ->where("a.apply_date <= '$this->endDate' and a.employee_id=:id and b.pin_name_id='{$twoRow["id"]}'",
                        array(":id"=>$this->employee_id))->order('a.apply_date asc')->queryRow();
                if(!$row){
                    $score = "";
                    break;
                }else{
                    $score.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['pin_code']}</td><td>{$row['apply_date']}</td><td>{$row['name']}</td><td>{$row['pin_num']}</td></tr>";
                }
            }
            $html.=empty($score)?"<tr><td colspan='6'>无</td></tr>":$score;
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //介绍新生意得分
    private function new_num($year,$month,$employee_id){
        $score=Yii::app()->db->createCommand()->select("sum(score_num)")->from("opr_service_new")
            ->where("service_year=:year and service_month=:month and employee_id=:id",
                array(":year"=>$year,":month"=>$month,":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score):0;
    }

    //介绍新生意得分table
    private function new_num_table(){
        $rows=Yii::app()->db->createCommand()->select("service_code,service_year,id,service_month,service_num,score_num")
            ->from("opr_service_new")
            ->where("service_date between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>单数编号</th><th>时间</th><th>服务单数</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['service_code']}</td><td>{$row['service_year']}年{$row['service_month']}月</td><td>{$row['service_num']}</td><td class='text-right'>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //洗地易销售得分
    private function sales_num($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
        $name = $this->employee_name." ({$this->employee_code})";
        $score = Yii::app()->db->createCommand()->select("sum(a.qty)")
            ->from("swoper{$suffix}.swo_logistic_dtl a")
            ->leftJoin("swoper{$suffix}.swo_logistic b","a.log_id=b.id")
            ->leftJoin("swoper{$suffix}.swo_task f","a.task=f.id")
            ->where("a.money>0 and f.task_type='FLOOR' and b.salesman=:name and b.log_dt between '$this->startDate' and '$this->endDate'",
                array(":name"=>$name))->queryScalar();
        return is_numeric($score)?floatval($score)*100:0;
    }

    //洗地易销售得分table
    private function sales_num_table(){
        $suffix = Yii::app()->params['envSuffix'];
        $name = $this->employee_name." ({$this->employee_code})";
        $rows = Yii::app()->db->createCommand()->select("b.id,b.log_dt,b.company_name,a.qty")
            ->from("swoper{$suffix}.swo_logistic_dtl a")
            ->leftJoin("swoper{$suffix}.swo_logistic b","a.log_id=b.id")
            ->leftJoin("swoper{$suffix}.swo_task f","a.task=f.id")
            ->where("a.money>0 and f.task_type='FLOOR' and b.salesman=:name and b.log_dt between '$this->startDate' and '$this->endDate'",
                array(":name"=>$name))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>出单日期</th><th>客户名称</th><th>任务数量</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $row['log_dt']=CGeneral::toDate($row['log_dt']);
                $row['score_num'] = $row['qty']*100;
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['log_dt']}</td><td>{$row['company_name']}</td><td>{$row['qty']}</td><td class='text-right'>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //扣分分數
    private function deduct_num($year,$month,$employee_id){
        $score=Yii::app()->db->createCommand()->select("sum(score_num)")->from("opr_service_deduct")
            ->where("service_year=:year and service_month=:month and employee_id=:id",
                array(":year"=>$year,":month"=>$month,":id"=>$employee_id))->queryScalar();
        return is_numeric($score)?floatval($score):0;
    }

    //扣分分數table
    private function deduct_num_table(){
        $rows=Yii::app()->db->createCommand()->select("service_code,deduct_date,id,deduct_type,score_num")
            ->from("opr_service_deduct")
            ->where("deduct_date between '$this->startDate' and '$this->endDate' and employee_id=:id",
                array(":id"=>$this->employee_id))->queryAll();
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>扣分编号</th><th>扣分日期</th><th>扣分类型</th><th>得分</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $row["deduct_type"] = ServiceDeductList::getDeductType($row["deduct_type"],true);
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['service_code']}</td><td>{$row['deduct_date']}</td><td>{$row['deduct_type']}</td><td class='text-right'>{$row['score_num']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='6'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

    //月排行列表
    private function month_detail_table(){
        $monthList=array(0);
        for ($i=$this->rank_month;$i<=$this->rank_month+2;$i++){
            $monthList[]=$i;
        }
        $monthList=implode(",",$monthList);
        $rows=Yii::app()->db->createCommand()->select("id,rank_num,rank_year,rank_month,score_sum")
            ->from("opr_technician_rank")
            ->where("rank_year = '$this->rank_year' and rank_month in ($monthList) and employee_id=:id",
                array(":id"=>$this->employee_id))->order("rank_month asc")->queryAll();
        return $this->getMonthTable($rows);
    }

    //半年排行列表
    private function half_detail_table(){
        $monthList=array(0);
        for ($i=$this->rank_month;$i<=$this->rank_month+5;$i++){
            $monthList[]=$i;
        }
        $monthList=implode(",",$monthList);
        $rows=Yii::app()->db->createCommand()->select("id,rank_num,rank_year,rank_month,score_sum")
            ->from("opr_technician_rank")
            ->where("rank_year = '$this->rank_year' and rank_month in ($monthList) and employee_id=:id",
                array(":id"=>$this->employee_id))->order("rank_month asc")->queryAll();
        return $this->getMonthTable($rows);
    }

    //年排行列表
    private function year_detail_table(){
        $rows=Yii::app()->db->createCommand()->select("id,rank_num,rank_year,rank_month,score_sum")
            ->from("opr_technician_rank")
            ->where("rank_year = '$this->rank_year' and employee_id=:id",
                array(":id"=>$this->employee_id))->order("rank_month asc")->queryAll();
        return $this->getMonthTable($rows);
    }

    private function getMonthTable($rows){
        $html = "<thead>";
        $html.="<tr><th>员工编号</th><th>员工姓名</th><th>年份</th><th>月份</th><th>分数</th></tr>";
        $html.= "</thead><tbody>";
        if($rows){
            foreach ($rows as $row){
                $row["rank_year"].=Yii::t("rank","year unit");
                $row["rank_month"].=Yii::t("rank","month unit");
                $html.="<tr data-id='{$row["id"]}'><td>{$this->employee_code}</td><td>{$this->employee_name}</td><td>{$row['rank_year']}</td><td>{$row['rank_month']}</td><td class='text-right'>{$row['score_sum']}</td></tr>";
            }
        }else{
            $html.="<tr><td colspan='5'>无</td></tr>";
        }
        $html.= "</tbody>";
        return $html;
    }

	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'rank'=>Yii::t('rank','rank'),
            'code'=>Yii::t('rank','employee code'),
            'name'=>Yii::t('rank','employee name'),
            'employee_id'=>Yii::t('rank','employee name'),
            'city_name'=>Yii::t('rank','city'),
            'rank_month'=>Yii::t('rank','month'),
            'rank_year'=>Yii::t('rank','year'),
            'score_sum'=>Yii::t('rank','Score Sum'),
            'lud'=>Yii::t('rank','Update Date Last'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
            array('id,employee_id,rank_month,rank_year','safe'),
		);
	}

	public function retrieveData($index,$rank=0)
	{
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("a.*,b.city as employee_city,b.code as employee_code,b.name as employee_name")->from("opr_technician_rank a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id=b.id")
            ->where("a.id=:id",array(":id"=>$index))->queryRow();
		if ($row) {
            $this->arrList=array();
			$this->id = $row['id'];
			$this->rank = $rank;
			$this->name = $row['employee_name']."({$row['employee_code']})";
			$this->employee_id = $row['employee_id'];
			$this->city = $row['employee_city'];
			$this->employee_code = $row['employee_code'];
			$this->employee_name = $row['employee_name'];
			$this->rank_year = $row['rank_year'];
			$this->rank_month = $row['rank_month'];
			$this->score_sum = floatval($row['score_sum']);
			$this->other_score = floatval($row['other_score']);
			$this->lud = $row['lud'];
			$sqlDate = self::getSqlDate($this->rank_year,$this->rank_month);
			foreach ($sqlDate as $item=>$rule){
                $this->arrList[$item] = key_exists($item,$row)?floatval($row[$item]):"";
            }
            return true;
		}else{
		    return false;
        }
	}

	//异步请求获取表格
    public function ajaxDetailForHtml(){
        $suffix = Yii::app()->params['envSuffix'];
	    $id = key_exists("id",$_GET)?$_GET["id"]:0;
	    $year = key_exists("year",$_GET)?$_GET["year"]:0;
	    $month = key_exists("month",$_GET)?$_GET["month"]:0;
	    $type = key_exists("type",$_GET)?$_GET["type"]:0;
	    $value = key_exists("value",$_GET)?$_GET["value"]:0;
        $row = Yii::app()->db->createCommand()->select("id,name,code")->from("hr$suffix.hr_employee")
            ->where("id=:id",array(":id"=>$id))->queryRow();
        $this->employee_id = $row["id"];
        $this->employee_code = $row["code"];
        $this->employee_name = $row["name"];
        $this->rank_year = $year;
        $this->rank_month = $month>=1&&$month<=12?$month:date("n");
        $list = self::getSqlDate($this->rank_year,$this->rank_month);
        $list["month_detail"]=array();
        $list["half_detail"]=array();
        $list["year_detail"]=array();
	    if(empty($id)||empty($year)||empty($month)||empty($type)||!key_exists($value,$list)||!$row){
	        return "<p>数据异常，请刷新重试</p>";
        }
        $this->resetStartDateEndDate($year,$month,$type);
        $value.="_table";
        $html = "<table class='table table-bordered table-striped table-hover'>";
        $html.=$this->$value();
        $html.="</table>";
        return $html;
    }

	public function resetStartDateEndDate($year,$month,$type=1){
        $startDate = date("Y-m-d 00:00:00",strtotime("{$year}/{$month}/1"));
        switch ($type){
            case 1://每月
                $endDate = date("Y-m-d 23:59:59",strtotime("{$startDate} + 1 month - 1 day"));
                break;
            case 2://季度
                $endDate = date("Y-m-d 23:59:59",strtotime("{$startDate} + 3 month - 1 day"));
                break;
            case 3://半年度
                $this->year_type=$month==1?1:2;
                $endDate = date("Y-m-d 23:59:59",strtotime("{$startDate} + 6 month - 1 day"));
                break;
            case 4://年度
                $this->year_type=0;
                $endDate = date("Y-m-d 23:59:59",strtotime("{$startDate} + 1 year - 1 day"));
                break;
            default:
                $endDate = date("Y-m-d 23:59:59",strtotime("{$startDate} + 1 month - 1 day"));
        }
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

	public function insertTechnician($year,$month,$resetBool=true){
        $city_allow = ServiceMoneyForm::getMMRANKCity();
        $year = is_numeric($year)?intval($year):2022;
	    $month = is_numeric($month)?intval($month):1;
        $month = $month>=1&&$month<=12?$month:date("n");
	    $this->resetStartDateEndDate($year,$month);
        $suffix = Yii::app()->params['envSuffix'];
        $rows = Yii::app()->db->createCommand()->select("a.id,a.name,a.code")->from("hr$suffix.hr_employee a")
            ->leftJoin("hr$suffix.hr_dept b","a.position=b.id")
            ->where("a.city in ({$city_allow}) and b.review_status=1 and b.review_type=2 and b.dept_class='Technician' and a.staff_status=0")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $this->employee_id = $row["id"];
                $this->employee_code = $row["code"];
                $this->employee_name = $row["name"];
                $id = Yii::app()->db->createCommand()->select("id")->from("opr_technician_rank")
                    ->where("rank_year=:year and rank_month=:month and employee_id=:id",
                        array(":year"=>$year,":month"=>$month,":id"=>$row["id"]))->queryScalar();
                if(!$id){
                    Yii::app()->db->createCommand()->insert("opr_technician_rank",array(
                        "rank_year"=>$year,
                        "rank_month"=>$month,
                        "employee_id"=>$row["id"],
                        "lcu"=>"系统",
                    ));
                    $id=Yii::app()->db->getLastInsertID();
                }
                $this->id = $id;
                if($resetBool){ //刷新分数
                    $arr=array("score_sum"=>0,"other_score"=>0,"lud"=>date("Y-m-d H:i:s"));
                    $sqlDate = self::getSqlDate($year,$month);
                    foreach ($sqlDate as $item=>$rule){
                        $arr[$item] = $this->$item($year,$month,$row["id"]);
                        $arr["score_sum"]+=$arr[$item];
                        if($rule["add"]){
                            $arr["other_score"]+=$arr[$item];
                        }
                    }
                    Yii::app()->db->createCommand()->update("opr_technician_rank",$arr,"id=:id",array(":id"=>$id));
                }
            }
            if ($resetBool){
                $this->updateRankNum($year,$month);//修改排名
            }
        }
    }

    public function resetOneRank($year,$month,$employee_id){
        $suffix = Yii::app()->params['envSuffix'];
	    $staffRow = Yii::app()->db->createCommand()->select("id,code,name")->from("hr{$suffix}.hr_employee")
            ->where("id=:id",array(":id"=>$employee_id))->queryRow();
	    if($staffRow){
	        $this->employee_id = $employee_id;
            $this->employee_code = $staffRow["code"];
            $this->employee_name = $staffRow["name"];
        }
        $id = Yii::app()->db->createCommand()->select("id")->from("opr_technician_rank")
            ->where("rank_year=:year and rank_month=:month and employee_id=:id",
                array(":year"=>$year,":month"=>$month,":id"=>$employee_id))->queryScalar();
        if($id){
            $this->resetStartDateEndDate($year,$month);
            $arr=array("score_sum"=>0,"other_score"=>0);
            $sqlDate = self::getSqlDate($year,$month);
            foreach ($sqlDate as $item=>$rule){
                $arr[$item] = $this->$item($year,$month,$employee_id);
                $arr["score_sum"]+=$arr[$item];
                if($rule["add"]){
                    $arr["other_score"]+=$arr[$item];
                }
            }
            Yii::app()->db->createCommand()->update("opr_technician_rank",$arr,"id=:id",array(":id"=>$id));
        }
    }

    //保存员工的排名名次
    private function updateRankNum($year,$month){
	    $lastScore=0;//上一次的分数
	    $rank = 0;
        $rows = Yii::app()->db->createCommand()->select("id")->from("opr_technician_rank")
            ->where("rank_year=:year and rank_month=:month",
                array(":year"=>$year,":month"=>$month))->order("score_sum desc")->queryAll();
        if($rows){
            foreach ($rows as $row){
                $rank++;
                Yii::app()->db->createCommand()->update("opr_technician_rank",array("rank_num"=>$rank),"id=:id",array(":id"=>$row["id"]));
            }
        }

    }

    //考核计算公式
    private function reviewScore($review_num){
        $review_num = is_numeric($review_num)?floatval($review_num):0;
        $arr=array(
            array("min"=>-100,"max"=>60,"value"=>0),
            array("min"=>60,"max"=>64,"value"=>150),
            array("min"=>64,"max"=>67,"value"=>200),
            array("min"=>67,"max"=>70,"value"=>250),
            array("min"=>70,"max"=>73,"value"=>350),
            array("min"=>73,"max"=>76,"value"=>450),
            array("min"=>76,"max"=>78,"value"=>550),
            array("min"=>78,"max"=>80,"value"=>650),
            array("min"=>80,"max"=>86,"value"=>800),
            array("min"=>86,"max"=>91,"value"=>950),
            array("min"=>91,"max"=>96,"value"=>1100),
            array("min"=>96,"max"=>200,"value"=>1300),
        );
        foreach ($arr as $row){
            if($row["min"]<=$review_num&&$row["max"]>$review_num){
                return $row["value"];
            }
        }
        return $review_num;
    }
    //质检计算公式
    private function qualityScoreCompute($sum,$avg){
        $rate = $avg;
        if($rate<=80){
            $rate=0;
        }elseif ($rate<=85){
            $rate=100;
        }elseif ($rate<=90){
            $rate=200;
        }elseif ($rate<=95){
            $rate=300;
        }else{
            $rate=500;
        }
        $score = $rate*(1+$sum*0.02);
        return array("rate"=>$rate,"score"=>$score,"compute"=>"(1+{$sum}×0.02)×{$rate}");
    }
}