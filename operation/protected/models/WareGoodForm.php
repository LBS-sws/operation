<?php

class WareGoodForm extends CFormModel
{
    /* User Fields */
    public $start_date;
    public $end_date;
    public $searchU=1;//是否查询派单系统
    public $searchType=2;//查询类型 1:销售出库 2：技术员领料
    public $four_start_date;

    public $data=array();

    public $th_sum=0;//所有th的个数

    public $downJsonText='';

    public $u_load_data=array();//查询时长数组
    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'start_date'=>"开始时间",
            'end_date'=>"结束时间",
            'searchU'=>"是否查询派单系统",
            'searchType'=>"查询类型",
        );
    }

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('start_date,end_date,searchU,searchType','safe'),
            array('start_date,end_date','required'),
            array('start_date','validateDate'),
        );
    }

    public function validateDate($attribute, $params) {
        $this->start_date = General::toDate($this->start_date);
        $this->end_date = General::toDate($this->end_date);
        $this->four_start_date = date("Y/m/d",strtotime($this->start_date." -21 days"));
        if($this->end_date<$this->start_date){
            $this->addError($attribute, "查询时间异常");
        }
    }

    public function setCriteria($criteria)
    {
        if (count($criteria) > 0) {
            foreach ($criteria as $k=>$v) {
                $this->$k = $v;
            }
        }
    }

    public function getCriteria() {
        return array(
            'four_start_date'=>$this->four_start_date,
            'end_date'=>$this->end_date,
            'searchU'=>$this->searchU,
            'searchType'=>$this->searchType,
            'start_date'=>$this->start_date
        );
    }

    public static function getCitySetList($city_allow=""){
        $list=array();
        $suffix = Yii::app()->params['envSuffix'];
        $cityWhere="";
        if($city_allow!=="all"){
            $city_allow = empty($city_allow)?Yii::app()->user->city_allow():$city_allow;
            $cityWhere=" and b.code in ({$city_allow})";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("a.code,a.name as city_name,b.show_type,b.add_type,b.region_code,b.region_code as region,f.name as region_name")
            ->from("swoper{$suffix}.swo_city_set b")
            ->leftJoin("security{$suffix}.sec_city a","a.code=b.code")
            ->leftJoin("security{$suffix}.sec_city f","b.region_code=f.code")
            ->where("b.show_type=1 {$cityWhere}")
            ->order("b.z_index desc,a.name asc")
            ->queryAll();
        if ($rows){
            foreach ($rows as $row){
                $list[$row["code"]] = $row;
            }
        }
        return $list;
    }

    protected function getUMaterialsCostData($start,$end,$city_allow){
        $data = SystemU::getMaterialsCost($start,$end,$city_allow,false);
        return $data["data"];
    }

    protected function getMyTotalCostData($start,$end,$city_allow,$judge_type=0){
        switch ($judge_type){
            case 1://销售出库
                $whereSql = " and b.judge_type=1";
                break;
            case 2://技术员领料
                $whereSql = " and b.judge_type=2";
                break;
            default:
                $whereSql = "";
        }
        $rows = Yii::app()->db->createCommand()
            ->select("b.city,ifnull(f.class_report,'其它') as report_str,sum(ifnull(a.total_price,0)) as sum_amt")
            ->from("opr_order_goods a")
            ->leftJoin("opr_order b","a.order_id=b.id")
            ->leftJoin("opr_warehouse_class f","a.goods_id=f.warehouse_id")
            ->where("b.city in ({$city_allow}) {$whereSql} and date_format(b.audit_time,'%Y-%m-%d') BETWEEN '{$start}' and '{$end}'")
            ->group("b.city,ifnull(f.class_report,'其它')")
            ->queryAll();
        $data = array();
        if($rows){
            foreach ($rows as $row){
                switch ($row["report_str"]){
                    case "灭虫":
                        $keyStr="miechong_amt";
                        break;
                    case "清洁":
                        $keyStr="qingjie_amt";
                        break;
                    default:
                        $keyStr="other_amt";
                }
                if(!key_exists($row["city"],$data)){
                    $data[$row["city"]]=array("miechong_amt"=>0,"qingjie_amt"=>0,"other_amt"=>0,);
                }
                $data[$row["city"]][$keyStr]+=$row["sum_amt"];
            }
        }
        return $data;
    }

    public function retrieveData() {
        $this->u_load_data['load_start'] = time();
        $this->data = array();
        $city_allow = Yii::app()->user->city_allow();
        $startDate = $this->start_date;
        $endDate = $this->end_date;
        $citySetList = self::getCitySetList($city_allow);
        $city_allow = array_keys($citySetList);
        $my_city_allow = "'".implode("','",$city_allow)."'";
        $city_allow = implode(",",$city_allow);
        $this->u_load_data['u_load_start'] = time();
        //签到签离统计
        $uOneWareGood=array();
        $uFourWareGood=array();
        if($this->searchU==1){
            $uOneWareGood = self::getUMaterialsCostData($this->start_date,$this->end_date,$city_allow);
            $uFourWareGood = self::getUMaterialsCostData($this->four_start_date,$this->end_date,$city_allow);
        }
        $this->u_load_data['u_load_end'] = time();
        $myOneCost = $this->getMyTotalCostData($this->start_date,$this->end_date,$my_city_allow,$this->searchType);
        $myFourCost = $this->getMyTotalCostData($this->four_start_date,$this->end_date,$my_city_allow,$this->searchType);
        $allTemp = $this->defMoreCity();
        if($citySetList){
            $data = array();
            foreach ($citySetList as $cityRow){
                $regionCode = $cityRow["region_code"];
                $cityCode = $cityRow["code"];
                if(!key_exists($regionCode,$data)){
                    $data[$regionCode]=array(
                        "regionCode"=>$regionCode,
                        "regionName"=>$cityRow["region_name"],
                        "regionTemp"=>$allTemp,
                        "list"=>array(),
                    );
                    $data[$regionCode]["regionTemp"]["city"]=$regionCode;
                    $data[$regionCode]["regionTemp"]["city_name"]=$cityRow["region_name"];
                }
                $temp = $allTemp;
                $temp["city"]=$cityCode;
                $temp["city_name"]=$cityRow["city_name"];
                if(isset($uOneWareGood[$cityCode])){
                    $temp["one_g_qingjie_amt"]=$uOneWareGood[$cityCode]["service_qing"];
                    $temp["one_g_miechong_amt"]=$uOneWareGood[$cityCode]["service_mie"];
                    $temp["one_g_other_amt"]=$uOneWareGood[$cityCode]["service_other"];
                    $temp["one_g_all_amt"]=$uOneWareGood[$cityCode]["service_total"];
                    $temp["one_l_qingjie_amt"]=$uOneWareGood[$cityCode]["theory_qing"];
                    $temp["one_l_miechong_amt"]=$uOneWareGood[$cityCode]["theory_mie"];
                    $temp["one_l_other_amt"]=$uOneWareGood[$cityCode]["theory_other"];
                    $temp["one_l_all_amt"]=$uOneWareGood[$cityCode]["theory_total"];
                }
                if(isset($uFourWareGood[$cityCode])){
                    $temp["four_g_qingjie_amt"]=$uFourWareGood[$cityCode]["service_qing"];
                    $temp["four_g_miechong_amt"]=$uFourWareGood[$cityCode]["service_mie"];
                    $temp["four_g_other_amt"]=$uFourWareGood[$cityCode]["service_other"];
                    $temp["four_g_all_amt"]=$uFourWareGood[$cityCode]["service_total"];
                    $temp["four_l_qingjie_amt"]=$uFourWareGood[$cityCode]["theory_qing"];
                    $temp["four_l_miechong_amt"]=$uFourWareGood[$cityCode]["theory_mie"];
                    $temp["four_l_other_amt"]=$uFourWareGood[$cityCode]["theory_other"];
                    $temp["four_l_all_amt"]=$uFourWareGood[$cityCode]["theory_total"];
                }
                if(isset($myOneCost[$cityCode])){
                    $temp["one_s_qingjie_amt"]=$myOneCost[$cityCode]["qingjie_amt"];
                    $temp["one_s_miechong_amt"]=$myOneCost[$cityCode]["miechong_amt"];
                    $temp["one_s_other_amt"]=$myOneCost[$cityCode]["other_amt"];
                }
                if(isset($myFourCost[$cityCode])){
                    $temp["four_s_qingjie_amt"]=$myFourCost[$cityCode]["qingjie_amt"];
                    $temp["four_s_miechong_amt"]=$myFourCost[$cityCode]["miechong_amt"];
                    $temp["four_s_other_amt"]=$myFourCost[$cityCode]["other_amt"];
                }
                $this->resetTempRow($temp);
                //将城市数值添加到区域内
                foreach ($temp as $key=>$item){
                    if(!empty($item)&&is_numeric($item)){
                        $data[$regionCode]["regionTemp"][$key]+=$item;
                    }
                }
                $data[$regionCode]["list"][]=$temp;
            }
            $this->data = $data;
        }
        $session = Yii::app()->session;
        $session['wareGood_c01'] = $this->getCriteria();
        $this->u_load_data['load_end'] = time();
        return true;
    }

    public function addTempByList(&$temp,$uLists){
        foreach ($uLists as $key=>$lists){
            foreach ($lists as $itemKey=>$itemVlaue){
                $uKey = $key."_".$itemKey;
                if(key_exists($uKey,$temp)){
                    $temp[$uKey] = $itemVlaue;
                }
            }
        }
    }

    public static function getOfficeNameByUID($u_office_id){
        $suffix = Yii::app()->params['envSuffix'];
        $row = Yii::app()->db->createCommand()->select("name")
            ->from("hr{$suffix}.hr_office")
            ->where("u_id=:u_id",array(":u_id"=>$u_office_id))
            ->queryRow();
        return $row?$row['name']:$u_office_id;
    }

    //設置該城市的默認值
    private function defMoreCity(){
        $moreList = array(
            "qingjie_amt"=>0,//清洁
            "miechong_amt"=>0,//灭虫
            "other_amt"=>0,//其它
            "all_amt"=>0,//汇总
        );
        $topList=array(
            array("prv"=>"one_g_","name"=>"过去一周工单金额","type"=>"number"),
            array("prv"=>"one_l_","name"=>"过去一周理论领料金额","type"=>"number"),
            array("prv"=>"one_s_","name"=>"过去一周实际领料金额","type"=>"number"),
            array("prv"=>"one_r_l_g_","name"=>"过去一周理论领料占比","type"=>"rate"),//理论÷工单
            array("prv"=>"one_r_s_g_","name"=>"过去一周实际领料占比","type"=>"rate"),//实际÷工单
            array("prv"=>"one_r_s-l_","name"=>"过去一周实际-理论","type"=>"rate"),//实际-理论
            array("prv"=>"four_g_","name"=>"过去四周工单金额","type"=>"number"),
            array("prv"=>"four_l_","name"=>"过去四周理论领料金额","type"=>"number"),
            array("prv"=>"four_s_","name"=>"过去四周实际领料金额","type"=>"number"),
            array("prv"=>"four_r_l_g_","name"=>"过去四周理论领料占比","type"=>"rate"),
            array("prv"=>"four_r_s_g_","name"=>"过去四周实际领料占比","type"=>"rate"),
            array("prv"=>"four_r_s-l_","name"=>"过去四周实际-理论","type"=>"rate"),
        );
        $list = array(
            "city"=>"",
            "city_name"=>"",
        );
        foreach ($topList as $row){
            foreach ($moreList as $key=>$value){
                $list[$row["prv"].$key]=$row["type"]=="number"?$value:"";
            }
        }
        return $list;
    }

    protected function resetTempRow(&$temp){
        $temp["one_s_all_amt"]=0;
        $temp["one_s_all_amt"]+=$temp["one_s_qingjie_amt"];
        $temp["one_s_all_amt"]+=$temp["one_s_miechong_amt"];
        $temp["one_s_all_amt"]+=$temp["one_s_other_amt"];
        $temp["four_s_all_amt"]=0;
        $temp["four_s_all_amt"]+=$temp["four_s_qingjie_amt"];
        $temp["four_s_all_amt"]+=$temp["four_s_miechong_amt"];
        $temp["four_s_all_amt"]+=$temp["four_s_other_amt"];
        $moreList = array(
            "qingjie_amt"=>0,//清洁
            "miechong_amt"=>0,//灭虫
            "other_amt"=>0,//其它
            "all_amt"=>0,//汇总
        );
        $topList=array(
            array("prv"=>"one_r_l_g_","name"=>"过去一周理论领料占比","type"=>"rate","left"=>"one_l_","right"=>"one_g_"),//理论÷工单
            array("prv"=>"one_r_s_g_","name"=>"过去一周实际领料占比","type"=>"rate","left"=>"one_s_","right"=>"one_g_"),//实际÷工单
            array("prv"=>"one_r_s-l_","name"=>"过去一周实际-理论","type"=>"number","left"=>"one_r_s_g_","right"=>"one_r_l_g_"),//实际-理论
            array("prv"=>"four_r_l_g_","name"=>"过去四周理论领料占比","type"=>"rate","left"=>"four_l_","right"=>"four_g_"),
            array("prv"=>"four_r_s_g_","name"=>"过去四周实际领料占比","type"=>"rate","left"=>"four_s_","right"=>"four_g_"),
            array("prv"=>"four_r_s-l_","name"=>"过去四周实际-理论","type"=>"number","left"=>"four_r_s_g_","right"=>"four_r_l_g_"),
        );
        foreach ($topList as $row){
            foreach ($moreList as $key=>$value){
                if($row["type"]=="number"){
                    $temp[$row["prv"].$key]=$temp[$row["left"].$key]-$temp[$row["right"].$key];
                }else{
                    $temp[$row["prv"].$key]=empty($temp[$row["right"].$key])?0:round($temp[$row["left"].$key]/$temp[$row["right"].$key],4);
                }
            }
        }
    }

    public function resetTdRow(&$list,$bool=false){
        if($bool){
            $this->resetTempRow($list);
        }
    }

    //顯示提成表的表格內容
    public function comparisonHtml(){
        $contentHead='<div class="col-lg-12" style="padding-top: 15px;">
                        <div class="row panel panel-default" style="border-color: #333">
                            <!-- Default panel contents -->
                            <div class="panel-heading">
                                <h3 style="margin-top:10px;">{:head:}
                                </h3>
                            </div>
                            <!-- Table -->
                            <div class="table-responsive">';

        $contentEnd='</div></div></div>';
        $tabs =array();
        $arrs=array(
            array("title"=>"领料成本动态追踪","type"=>1,"name"=>"one"),
            array("title"=>"动态领料成本明细","type"=>2,"name"=>"two"),
            array("title"=>"动态领料成本追踪-按清洁灭虫其它","type"=>3,"name"=>"three"),
            array("title"=>"动态领料成本明细-按清洁灭虫其它","type"=>4,"name"=>"four"),
        );
        foreach ($arrs as $key=>$arr){
            //领料成本动态追踪
            $contentTable = str_replace("{:head:}",$arr["title"],$contentHead);
            $contentTable.=$this->getTableHtml($arr["type"]);
            $contentTable.=$contentEnd;
            $contentTable.=TbHtml::hiddenField("excel[{$arr["name"]}]",$this->downJsonText);
            $tabs[] = array(
                'label'=>$arr["title"],
                'content'=>$contentTable,
                'active'=>$key==0?true:false,
            );
        }
        return TbHtml::tabbableTabs($tabs);
    }
    public function getTableHtml($type=1){
        $html= '<table id="comparison'.$type.'" data-type="'.$type.'" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml($type);
        $html.=$this->tableBodyHtml($type);
        $html.=$this->tableFooterHtml($type);
        $html.="</table>";
        return $html;
    }

    protected function getTopArrByOne(){
        $colspan=array(
            array("name"=>"过去一周"),//过去一周
            array("name"=>"过去四周"),//过去四周
        );
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>"实际领料金额与理论领料金额差距",
                "colspan"=>$colspan
            ),//
            array("name"=>"理论领料金额占比（理论领料金额/工单金额）",
                "colspan"=>$colspan
            ),//
        );
        return $topList;
    }

    protected function getTopArrByTwo(){
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>"过去一周服务金额","rowspan"=>2),//过去一周服务金额
            array("name"=>"过去一周理论领料金额","rowspan"=>2),//
            array("name"=>"过去一周实际领料金额","rowspan"=>2),//
            array("name"=>"理论领料占比","rowspan"=>2),//
            array("name"=>"实际领料占比","rowspan"=>2),//
            array("name"=>"差异","rowspan"=>2),//
            array("name"=>"过去四周服务金额","rowspan"=>2),//
            array("name"=>"过去四周理论领料金额","rowspan"=>2),//
            array("name"=>"过去四周实际领料金额","rowspan"=>2),//
            array("name"=>"理论领料占比","rowspan"=>2),//
            array("name"=>"实际领料占比","rowspan"=>2),//
            array("name"=>"差异","rowspan"=>2),//
        );
        return $topList;
    }

    protected function getTopArrByThree(){
        $colspan=array(
            array("name"=>"清洁"),//清洁
            array("name"=>"灭虫"),//清洁
            array("name"=>"其它"),//清洁
        );
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>"过去一周实际领料金额与理论领料金额差距",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周实际领料金额与理论领料金额差距",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去一周理论领料金额占比（理论领料金额/工单金额）",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周理论领料金额占比（理论领料金额/工单金额）",
                "colspan"=>$colspan
            ),//
        );
        return $topList;
    }

    protected function getTopArrByFour(){
        $colspan=array(
            array("name"=>"清洁"),//清洁
            array("name"=>"灭虫"),//清洁
            array("name"=>"其它"),//清洁
        );
        $topList=array(
            array("name"=>Yii::t("summary","City"),"rowspan"=>2),//城市
            array("name"=>"过去一周工单金额",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去一周理论领料金额",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去一周实际领料金额",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去一周理论领料占比",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去一周实际领料占比",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去一周实际-理论",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周工单金额",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周理论领料金额",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周实际领料金额",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周理论领料占比",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周实际领料占比",
                "colspan"=>$colspan
            ),//
            array("name"=>"过去四周实际-理论",
                "colspan"=>$colspan
            ),//
        );
        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml($type){
        $this->th_sum = 0;
        switch ($type){
            case 1:
                $topList = self::getTopArrByOne();
                break;
            case 2:
                $topList = self::getTopArrByTwo();
                break;
            case 3:
                $topList = self::getTopArrByThree();
                break;
            default:
                $topList = self::getTopArrByFour();
        }
        $trOne="";
        $trTwo="";
        $html="<thead>";
        foreach ($topList as $list){
            $clickName=$list["name"];
            $colList=key_exists("colspan",$list)?$list['colspan']:array();
            $style = "";
            $colNum=0;
            if(key_exists("background",$list)){
                $style.="background:{$list["background"]};";
            }
            if(key_exists("color",$list)){
                $style.="color:{$list["color"]};";
            }
            if(!empty($colList)){
                foreach ($colList as $col){
                    $colNum++;
                    $trTwo.="<th style='{$style}'><span>".$col["name"]."</span></th>";
                    $this->th_sum++;
                }
            }else{
                $this->th_sum++;
            }
            $colNum = empty($colNum)?1:$colNum;
            $trOne.="<th style='{$style}' colspan='{$colNum}'";
            if($colNum>1){
                $trOne.=" class='click-th'";
            }
            if(key_exists("rowspan",$list)){
                $trOne.=" rowspan='{$list["rowspan"]}'";
            }
            if(key_exists("startKey",$list)){
                $trOne.=" data-key='{$list['startKey']}'";
            }
            $trOne.=" ><span>".$clickName."</span></th>";
        }
        $html.=$this->tableHeaderWidth();//設置表格的單元格寬度
        $html.="<tr>{$trOne}</tr><tr>{$trTwo}</tr>";
        $html.="</thead>";
        return $html;
    }

    //設置表格的單元格寬度
    private function tableHeaderWidth(){
        $html="<tr>";
        for($i=0;$i<$this->th_sum;$i++){
            $width=90;
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml($type){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data,$type);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
            $html.=TbHtml::hiddenField("excel".$type,$this->downJsonText);
        }
        return $html;
    }

    private function getDataAllKeyStrByOne(){
        $bodyKey = array(
            "city"=>array("show"=>false,"type"=>"number"),
            "city_name"=>array("show"=>true,"type"=>"number"),
            "one_r_s-l_all_amt"=>array("show"=>true,"type"=>"rate"),
            "four_r_s-l_all_amt"=>array("show"=>true,"type"=>"rate"),
            "one_r_l_g_all_amt"=>array("show"=>true,"type"=>"rate"),
            "four_r_l_g_all_amt"=>array("show"=>true,"type"=>"rate"),
        );
        return $bodyKey;
    }

    private function getDataAllKeyStrByTwo(){
        $moreList = array(
            "all_amt"=>0,//汇总
        );
        $topList=array(
            array("prv"=>"one_g_","name"=>"过去一周工单金额","type"=>"number"),
            array("prv"=>"one_l_","name"=>"过去一周理论领料金额","type"=>"number"),
            array("prv"=>"one_s_","name"=>"过去一周实际领料金额","type"=>"number"),
            array("prv"=>"one_r_l_g_","name"=>"过去一周理论领料占比","type"=>"rate"),//理论÷工单
            array("prv"=>"one_r_s_g_","name"=>"过去一周实际领料占比","type"=>"rate"),//实际÷工单
            array("prv"=>"one_r_s-l_","name"=>"过去一周实际-理论","type"=>"rate"),//实际-理论
            array("prv"=>"four_g_","name"=>"过去四周工单金额","type"=>"number"),
            array("prv"=>"four_l_","name"=>"过去四周理论领料金额","type"=>"number"),
            array("prv"=>"four_s_","name"=>"过去四周实际领料金额","type"=>"number"),
            array("prv"=>"four_r_l_g_","name"=>"过去四周理论领料占比","type"=>"rate"),
            array("prv"=>"four_r_s_g_","name"=>"过去四周实际领料占比","type"=>"rate"),
            array("prv"=>"four_r_s-l_","name"=>"过去四周实际-理论","type"=>"rate"),
        );
        $bodyKey = array(
            "city"=>array("show"=>false,"type"=>"number"),
            "city_name"=>array("show"=>true,"type"=>"number"),
        );
        foreach ($topList as $row){
            foreach ($moreList as $key=>$value){
                $bodyKey[$row["prv"].$key]=array("show"=>true,"type"=>$row["type"]);
            }
        }
        return $bodyKey;
    }

    private function getDataAllKeyStrByThree(){
        $moreList = array(
            "qingjie_amt"=>0,//清洁
            "miechong_amt"=>0,//灭虫
            "other_amt"=>0,//其它
            //"all_amt"=>0,//汇总
        );
        $topList=array(
            array("prv"=>"one_r_s-l_","name"=>"过去一周实际-理论","type"=>"rate"),//实际-理论
            array("prv"=>"four_r_s-l_","name"=>"过去四周实际-理论","type"=>"rate"),
            array("prv"=>"one_r_l_g_","name"=>"过去一周理论领料占比","type"=>"rate"),//理论÷工单
            array("prv"=>"four_r_l_g_","name"=>"过去四周理论领料占比","type"=>"rate"),
        );
        $bodyKey = array(
            "city"=>array("show"=>false,"type"=>"number"),
            "city_name"=>array("show"=>true,"type"=>"number"),
        );
        foreach ($topList as $row){
            foreach ($moreList as $key=>$value){
                $bodyKey[$row["prv"].$key]=array("show"=>true,"type"=>$row["type"]);
            }
        }
        return $bodyKey;
    }

    private function getDataAllKeyStrByFour(){
        $moreList = array(
            "qingjie_amt"=>0,//清洁
            "miechong_amt"=>0,//灭虫
            "other_amt"=>0,//其它
            //"all_amt"=>0,//汇总
        );
        $topList=array(
            array("prv"=>"one_g_","name"=>"过去一周工单金额","type"=>"number"),
            array("prv"=>"one_l_","name"=>"过去一周理论领料金额","type"=>"number"),
            array("prv"=>"one_s_","name"=>"过去一周实际领料金额","type"=>"number"),
            array("prv"=>"one_r_l_g_","name"=>"过去一周理论领料占比","type"=>"rate"),//理论÷工单
            array("prv"=>"one_r_s_g_","name"=>"过去一周实际领料占比","type"=>"rate"),//实际÷工单
            array("prv"=>"one_r_s-l_","name"=>"过去一周实际-理论","type"=>"rate"),//实际-理论
            array("prv"=>"four_g_","name"=>"过去四周工单金额","type"=>"number"),
            array("prv"=>"four_l_","name"=>"过去四周理论领料金额","type"=>"number"),
            array("prv"=>"four_s_","name"=>"过去四周实际领料金额","type"=>"number"),
            array("prv"=>"four_r_l_g_","name"=>"过去四周理论领料占比","type"=>"rate"),
            array("prv"=>"four_r_s_g_","name"=>"过去四周实际领料占比","type"=>"rate"),
            array("prv"=>"four_r_s-l_","name"=>"过去四周实际-理论","type"=>"rate"),
        );
        $bodyKey = array(
            "city"=>array("show"=>false,"type"=>"number"),
            "city_name"=>array("show"=>true,"type"=>"number"),
        );
        foreach ($topList as $row){
            foreach ($moreList as $key=>$value){
                $bodyKey[$row["prv"].$key]=array("show"=>true,"type"=>$row["type"]);
            }
        }
        return $bodyKey;
    }

    //获取td对应的键名
    private function getDataAllKeyStr($type){
        switch ($type){
            case 1:
                return $this->getDataAllKeyStrByOne();
            case 2:
                return $this->getDataAllKeyStrByTwo();
            case 3:
                return $this->getDataAllKeyStrByThree();
            default:
                return $this->getDataAllKeyStrByFour();
        }
    }

    //將城市数据寫入表格
    private function showServiceHtml($data,$type){
        $bodyKey = $this->getDataAllKeyStr($type);
        $html="";
        if(!empty($data)){
            //last_u_all
            foreach ($data as $regionList){
                if(!empty($regionList["list"])) {
                    foreach ($regionList["list"] as $cityList) {
                        $this->resetTdRow($cityList);
                        $html.="<tr data-city='{$cityList['city']}'>";
                        foreach ($bodyKey as $keyStr=>$keyRow){
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            if(!$keyRow["show"]){//不显示
                                continue;
                            }
                            $tdClass = self::getTextColorForKeyStr($text,$keyRow,$keyStr);

                            $exprData = self::tdClick($tdClass,$keyStr,$cityList["city"]);//点击后弹窗详细内容
                            $excelText = self::showExcelNum($text,$keyRow,$keyStr);
                            $this->downJsonText["excel"][$regionList['regionCode']]['list'][$cityList['city']][$keyStr]=$excelText;

                            $html.="<td class='{$tdClass}' {$exprData}><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $html.=$this->printTableTr($regionList["regionTemp"],$bodyKey);
                    $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
        }
        return $html;
    }

    protected function clickList(){
        $moreList = array(
            "qingjie_amt"=>array("exprName"=>"清洁","exprFun"=>"funQing"),//清洁
            "miechong_amt"=>array("exprName"=>"灭虫","exprFun"=>"funMie"),//灭虫
            "other_amt"=>array("exprName"=>"其它","exprFun"=>"funOther"),//其它
            "all_amt"=>array("exprName"=>"全部","exprFun"=>"funAll"),//汇总
        );
        $topList=array(
            //array("prv"=>"one_g_","name"=>"过去一周工单金额","type"=>"number"),
            //array("prv"=>"one_l_","name"=>"过去一周理论领料金额","type"=>"number"),
            array("prv"=>"one_s_","name"=>"过去一周实际领料金额","type"=>"number","funStr"=>"one"),
            //array("prv"=>"four_g_","name"=>"过去四周工单金额","type"=>"number"),
            //array("prv"=>"four_l_","name"=>"过去四周理论领料金额","type"=>"number"),
            array("prv"=>"four_s_","name"=>"过去四周实际领料金额","type"=>"number","funStr"=>"four"),
        );
        $bodyKey=array();
        foreach ($topList as $row){
            foreach ($moreList as $key=>$value){
                $title = $row["name"]."({$value["exprName"]})";
                $type = $value["exprFun"]."_".$row["funStr"];
                $bodyKey[$row["prv"].$key]=array("title"=>$title,"type"=>$type);
            }
        }
        return $bodyKey;
    }

    private function tdClick(&$tdClass,$keyStr,$city){
        $expr = " data-city='{$city}'";
        $list = $this->clickList();
        if(key_exists($keyStr,$list)){
            $tdClass.=" td_detail";
            $expr.= " data-type='{$list[$keyStr]['type']}'";
            $expr.= " data-title='{$list[$keyStr]['title']}'";
        }

        return $expr;
    }

    //設置百分比顏色
    public static function showExcelNum($text,$keyRow,$keyStr){
        if (strpos($keyStr,'_r_s-l_')!==false){//实际-理论
            $rateNum = floatval($text);
            $text =array("bg"=>"FFFFFF","color"=>"","text"=>$text);
            if($rateNum>5){
                $text["bg"]="FFF3CA";
            }
            if($rateNum>0){
                $text["color"]="C00000";
            }elseif ($rateNum<=-5){
                $text["color"]="00B050";
            }else{
                $text["color"]="BFBFBF";
            }
        }
        /*
    if($keyRow["type"]=="rate"){
        $rateNum = floatval($text);
        if($rateNum>=1&&$rateNum<=3){
            $text =array("bg"=>"c4e3f3","text"=>$text);
        }elseif ($rateNum>3&&$rateNum<=5){
            $text =array("bg"=>"fcf8e3","text"=>$text);
        }elseif ($rateNum>5){
            $text =array("bg"=>"ebcccc","text"=>$text);
        }
    }
    */
        return $text;
    }

    //設置百分比顏色
    public static function getTextColorForKeyStr(&$text,$keyRow,$keyStr){
        $tdClass = "";
        if($keyRow["type"]=="rate"){
            $rateNum = floatval($text);
            $rateNum*=100;
            if (strpos($keyStr,'_r_s-l_')!==false){//实际-理论
                if($rateNum>5){
                    $tdClass.=" bgFFF3CA";
                }
                if($rateNum>0){
                    $tdClass.=" crC00000";
                }elseif ($rateNum<=-5){
                    $tdClass.=" cr00B050";
                }else{
                    $tdClass.=" crBFBFBF";
                }
            }
            $text = "".$rateNum."%";
        }

        return $tdClass;
    }

    protected function printTableTr($data,$bodyKey){
        $this->resetTdRow($data,true);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr=>$keyRow){
            if(!$keyRow["show"]){//不显示
                continue;
            }
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = self::getTextColorForKeyStr($text,$keyRow,$keyStr);

            $excelText = self::showExcelNum($text,$keyRow,$keyStr);
            $this->downJsonText["excel"][$data['city']]['count'][$keyStr]=$excelText;
            $html.="<td class='{$tdClass}' style='font-weight: bold'><span>{$text}</span></td>";
        }
        $html.="</tr>";
        return $html;
    }

    public function tableFooterHtml($type){
        $html="<tfoot>";
        $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        $html.="</tfoot>";
        return $html;
    }

    //下載
    public function downExcel($postExcelData){
        $arrs=array(
            array("title"=>"领料成本动态追踪","type"=>1,"name"=>"one","headList"=>$this->getTopArrByOne()),
            array("title"=>"动态领料成本明细","type"=>2,"name"=>"two","headList"=>$this->getTopArrByTwo()),
            array("title"=>"动态领料成本追踪-按清洁灭虫其它","type"=>3,"name"=>"three","headList"=>$this->getTopArrByThree()),
            array("title"=>"动态领料成本明细-按清洁灭虫其它","type"=>4,"name"=>"four","headList"=>$this->getTopArrByFour()),
        );
        $this->validateDate("","");
        $excel = new DownStatistics();
        $excel->init();
        $dateStr="过去一周日期：{$this->start_date} ~ {$this->end_date}\n";
        $dateStr.="过去四周日期：{$this->four_start_date} ~ {$this->end_date}";
        foreach ($arrs as $key=>$arr){
            $headList = $arr["headList"];
            $titleName = $arr["title"];
            if($key!=0){
                $excel->addSheet($titleName);
            }else{
                $excel->setSheetName($titleName);
            }
            $excelData=key_exists($arr["name"],$postExcelData)?json_decode($postExcelData[$arr["name"]],true):array("excel"=>array());
            $excelData = $excelData["excel"];
            $excel->SetHeaderTitle($titleName);
            $excel->SetHeaderString($dateStr);
            $excel->outHeader($key);
            $excel->setSummaryHeader($headList);
            $excel->setCheckWeekData($excelData);
        }
        $titleName="领料成本统计";
        $excel->outExcel($titleName);
    }

}