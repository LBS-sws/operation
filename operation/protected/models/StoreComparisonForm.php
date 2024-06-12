<?php

class StoreComparisonForm extends CFormModel
{
	/* User Fields */
    public $search_city;//查询城市
    public $city_name;//查询城市

    public $data=array();

	public $th_sum=0;//所有th的个数

    public $downJsonText='';
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.
	 */
	public function attributeLabels()
	{
		return array(
            'search_city'=>Yii::t('summary','search city'),
		);
	}

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('search_city','safe'),
            array('search_city','required'),
            array('search_city','validateCity'),
        );
    }

    public function validateCity($attribute, $params) {
        $city_allow = Yii::app()->user->city_allow();
        if (strpos($city_allow,"'{$this->search_city}'")===false){
            $this->addError($attribute,"查询城市异常，请刷新重试");
        }else{
            $this->city_name = CGeneral::getCityName($this->search_city);
        }
    }

    public function setCriteria($criteria){
        if (count($criteria) > 0) {
            foreach ($criteria as $k=>$v) {
                $this->$k = $v;
            }
        }
    }

    public function getCriteria() {
        return array(
            'search_city'=>$this->search_city,
        );
    }

    private function getLBSWarehouse(){
        $list = array();
        $rows = Yii::app()->db->createCommand()->select("goods_code,name,inventory,jd_good_no,city")->from("opr_warehouse")
            ->where("city=:city and display=1",array(":city"=>$this->search_city))->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(empty($row["jd_good_no"])){
                    $key=$row["city"].$row["goods_code"];
                }else{
                    $key="".$row["jd_good_no"];
                }
                $list[$key]=array(
                    "lbs_good_no"=>$row["goods_code"],
                    "lbs_good_name"=>$row["name"],
                    "lbs_store_sum"=>$row["inventory"],
                    "jd_good_no"=>$row["jd_good_no"],
                );
            }
        }
        return $list;
    }

    private function getJDWarehouse(){
        $searchData=array(
            "org_number"=>$this->search_city,
        );
        $list = array();
        $jdData = CurlForDelivery::getWarehouseGoodsForJD(array("data"=>$searchData));
        if($jdData["code"]==200){
            foreach ($jdData["outData"] as $row){
                $row["material_number"]="".$row["material_number"];
                if(!key_exists($row["material_number"],$list)){
                    $list[$row["material_number"]]=array(
                        "jd_good_no"=>$row["material_number"],
                        "jd_good_name"=>$row["material_name"],
                        "jd_store_sum"=>0,
                        "jd_good_text"=>"",
                        "jd_warehouse_list"=>array(),
                    );
                    $list[$row["material_number"]]["jd_store_sum"]+=$row["qty"];
                    $list[$row["material_number"]]["jd_warehouse_list"][]=$row;
                    $list[$row["material_number"]]["jd_good_text"].=empty($list[$row["material_number"]]["jd_good_text"])?"":";";
                    $list[$row["material_number"]]["jd_good_text"].="仓库编码：".$row["warehouse_number"].",";//
                    $list[$row["material_number"]]["jd_good_text"].="仓库库存：".$row["qty"];//
                }
            }
        }
        return $list;
    }

    public function retrieveData() {
        $data = array(
            "errorNone"=>array("count"=>0,"name"=>"LBS未填写金蝶物料编号","list"=>array()),
            "error"=>array("count"=>0,"name"=>"库存不一致","list"=>array()),
            "errorLBS"=>array("count"=>0,"name"=>"LBS不存在该物品","list"=>array()),
            "errorJD"=>array("count"=>0,"name"=>"金蝶系统不存在该物品","list"=>array()),
            "success"=>array("count"=>0,"name"=>"库存一致","list"=>array()),
        );

        $lbsData = $this->getLBSWarehouse();
        $jdData = $this->getJDWarehouse();

        if(!empty($lbsData)){
            foreach ($lbsData as $lbsRow){
                $good_no = $lbsRow["jd_good_no"];
                if(empty($good_no)){
                    $data["errorNone"]["list"][]=$this->getComparisonRow($lbsRow,array(),"errorNone");
                }elseif(key_exists($good_no,$jdData)){
                    if($jdData["jd_store_sum"]!=$lbsRow["lbs_store_sum"]){
                        $data["error"]["list"][]=$this->getComparisonRow($lbsRow,array(),"error");
                    }else{
                        $data["success"]["list"][]=$this->getComparisonRow($lbsRow,array(),"success");
                    }
                    unset($jdData[$good_no]);
                }else{
                    $data["errorJD"]["list"][]=$this->getComparisonRow($lbsRow,array(),"errorJD");
                }
            }
        }

        if(!empty($jdData)){
            foreach ($jdData as $good_no=>$jdRow){
                $data["errorLBS"]["list"][]=$this->getComparisonRow(array(),$jdRow,"errorLBS");
            }
        }

        $this->data = $data;
        $session = Yii::app()->session;
        $session['storeComparison_c01'] = $this->getCriteria();
        return true;
    }

    private function getComparisonRow($lbsRow,$jdRow,$errorType){
        $arr = array();
        switch ($errorType){
            case "errorNone"://金蝶系统不存在该物品
                $arr = array(
                    "city_name"=>$this->city_name,
                    "lbs_good_no"=>$lbsRow["lbs_good_no"],
                    "lbs_good_name"=>$lbsRow["lbs_good_name"],
                    "lbs_store_sum"=>$lbsRow["lbs_store_sum"],
                    "jd_good_no"=>"-",
                    "jd_good_name"=>"-",
                    "jd_store_sum"=>"-",
                    "jd_good_text"=>"-",
                    "comparison_text"=>"LBS未填写金蝶物料编号",
                );
                break;
            case "errorJD"://金蝶系统不存在该物品
                $arr = array(
                    "city_name"=>$this->city_name,
                    "lbs_good_no"=>$lbsRow["lbs_good_no"],
                    "lbs_good_name"=>$lbsRow["lbs_good_name"],
                    "lbs_store_sum"=>$lbsRow["lbs_store_sum"],
                    "jd_good_no"=>$lbsRow["jd_good_no"],
                    "jd_good_name"=>"-",
                    "jd_store_sum"=>"-",
                    "jd_good_text"=>"-",
                    "comparison_text"=>"金蝶系统不存在该物品",
                );
                break;
            case "errorLBS"://LBS不存在该物品
                $arr = array(
                    "city_name"=>$this->city_name,
                    "lbs_good_no"=>"-",
                    "lbs_good_name"=>"-",
                    "lbs_store_sum"=>"-",
                    "jd_good_no"=>$jdRow["jd_good_no"],
                    "jd_good_name"=>$jdRow["jd_good_name"],
                    "jd_store_sum"=>$jdRow["jd_store_sum"],
                    "jd_good_text"=>$jdRow["jd_good_text"],
                    "comparison_text"=>"LBS不存在该物品",
                );
                break;
            case "error"://库存不一致
                $arr = array(
                    "city_name"=>$this->city_name,
                    "lbs_good_no"=>$lbsRow["lbs_good_no"],
                    "lbs_good_name"=>$lbsRow["lbs_good_name"],
                    "lbs_store_sum"=>$lbsRow["lbs_store_sum"],
                    "jd_good_no"=>$jdRow["jd_good_no"],
                    "jd_good_name"=>$jdRow["jd_good_name"],
                    "jd_store_sum"=>$jdRow["jd_store_sum"],
                    "jd_good_text"=>$jdRow["jd_good_text"],
                    "comparison_text"=>"库存不一致",
                );
                break;
            case "success"://库存一致
                $arr = array(
                    "city_name"=>$this->city_name,
                    "lbs_good_no"=>$lbsRow["lbs_good_no"],
                    "lbs_good_name"=>$lbsRow["lbs_good_name"],
                    "lbs_store_sum"=>$lbsRow["lbs_store_sum"],
                    "jd_good_no"=>$jdRow["jd_good_no"],
                    "jd_good_name"=>$jdRow["jd_good_name"],
                    "jd_store_sum"=>$jdRow["jd_store_sum"],
                    "jd_good_text"=>$jdRow["jd_good_text"],
                    "comparison_text"=>"库存正常",
                );
                break;
        }

        return $arr;
    }

    protected function resetTdRow(&$list,$bool=false,$count=1){
        /*
        if(!$bool){
            $list["last_average"]=round($list["last_average"]/12,2);
        }else{
            $list["last_average"]=empty($count)?0:round($list["last_average"]/$count,2);
        }
        */
    }

    //顯示提成表的表格內容
    public function storeComparisonHtml(){
        $html= '<table id="storeComparison" class="table table-fixed table-condensed table-bordered table-hover">';
        $html.=$this->tableTopHtml();
        $html.=$this->tableBodyHtml();
        $html.=$this->tableFooterHtml();
        $html.="</table>";
        return $html;
    }

    private function getTopArr(){
        $topList=array(
            //城市
            array("name"=>Yii::t("summary","City"),"background"=>""),
            //LBS物料编号
            array("name"=>Yii::t("summary","lbs good no"),"background"=>"#f7fd9d"),
            //LBS物料名称
            array("name"=>Yii::t("summary","lbs good name"),"background"=>"#f7fd9d"),
            //LBS物料库存
            array("name"=>Yii::t("summary","lbs store sum"),"background"=>"#f7fd9d"),
            //金蝶物料编号
            array("name"=>Yii::t("summary","jd good no"),"background"=>"#fcd5b4"),
            //金蝶物料名称
            array("name"=>Yii::t("summary","jd good name"),"background"=>"#fcd5b4"),
            //金蝶物料库存
            array("name"=>Yii::t("summary","jd store sum"),"background"=>"#fcd5b4"),
            //金蝶物料库存详情
            array("name"=>Yii::t("summary","jd good text"),"background"=>"#FDE9D9"),
            //对比说明
            array("name"=>Yii::t("summary","comparison text"),"background"=>"#DCE6F1"),
        );

        return $topList;
    }

    //顯示提成表的表格內容（表頭）
    protected function tableTopHtml(){
        $this->th_sum = 0;
        $topList = self::getTopArr();
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
            if($i===0){
                $width=70;
            }elseif(in_array($i,array(3,6))){
                $width=60;
            }else{
                $width=90;
            }
            $html.="<th class='header-width' data-width='{$width}' width='{$width}px'>{$i}</th>";
        }
        return $html."</tr>";
    }

    public function tableBodyHtml(){
        $html="";
        if(!empty($this->data)){
            $this->downJsonText=array();
            $html.="<tbody>";
            $html.=$this->showServiceHtml($this->data);
            $html.="</tbody>";
            $this->downJsonText=json_encode($this->downJsonText);
            $html.=TbHtml::hiddenField("excel",$this->downJsonText);
        }
        return $html;
    }

    //获取td对应的键名
    private function getDataAllKeyStr(){
        $bodyKey = array(
            "city_name","lbs_good_no","lbs_good_name","lbs_store_sum",
            "jd_good_no","jd_good_name","jd_store_sum",
            "jd_good_text","comparison_text",
        );

        return $bodyKey;
    }

    //設置百分比顏色
    private function getTdClassForRow($row){
        $tdClass = "";
        return $tdClass;
    }

    public static function getTextColorForKeyStr($text,$keyStr){
        return "";
    }

    //將城市数据寫入表格
    protected function showServiceHtml($data){
        $bodyKey = $this->getDataAllKeyStr();
        $html="";
        if(!empty($data)){
            $allRow = array('count'=>0);//总计(所有地区)
            foreach ($data as $regionKey=>$regionList){
                if(!empty($regionList["list"])) {
                    $regionRow = array('count'=>0);//地区汇总
                    $regionCount = count($regionList["list"]);
                    foreach ($regionList["list"] as $cityKey=>$cityList) {
                        $allRow['count']++;//叠加的城市数量
                        $regionRow['count']++;//叠加的城市数量
                        $this->resetTdRow($cityList);
                        $tdClass = $regionKey=="success"?"":"text-danger";
                        $html.="<tr class='{$tdClass}'>";
                        foreach ($bodyKey as $keyStr){
                            if(!key_exists($keyStr,$regionRow)){
                                $regionRow[$keyStr]=0;
                            }
                            if(!key_exists($keyStr,$allRow)){
                                $allRow[$keyStr]=0;
                            }
                            $text = key_exists($keyStr,$cityList)?$cityList[$keyStr]:"0";
                            $regionRow[$keyStr]+=is_numeric($text)?floatval($text):0;
                            $allRow[$keyStr]+=is_numeric($text)?floatval($text):0;

                            $this->downJsonText["excel"][$regionKey]['list'][$cityKey][$keyStr]=$text;
                            $html.="<td><span>{$text}</span></td>";
                        }
                        $html.="</tr>";
                    }
                    //地区汇总
                    $regionRow["region"]=$regionKey;
                    $regionRow["city_name"]=$regionList["name"];
                    //$html.=$this->printTableTr($regionRow,$bodyKey,$regionCount);
                    //$html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
                }
            }
            //地区汇总
            $allRow["region"]="allRow";
            $allRow["city_name"]=Yii::t("summary","all total");
            //$html.=$this->printTableTr($allRow,$bodyKey,$allRow['count']);
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
            $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        }
        return $html;
    }

    protected function printTableTr($data,$bodyKey,$count=1){
        $this->resetTdRow($data,true,$count);
        $html="<tr class='tr-end click-tr'>";
        foreach ($bodyKey as $keyStr){
            $text = key_exists($keyStr,$data)?$data[$keyStr]:"0";
            $tdClass = self::getTextColorForKeyStr($text,$keyStr);
            //$inputHide = TbHtml::hiddenField("excel[{$data['region']}][count][]",$text);
            $this->downJsonText["excel"][$data['region']]['count'][$keyStr]=$text;
            $html.="<td class='{$tdClass}' style='font-weight: bold'><span>{$text}</span></td>";
        }
        $html.="</tr>";
        return $html;
    }

    public function tableFooterHtml(){
        $html="<tfoot>";
        $html.="<tr class='tr-end'><td colspan='{$this->th_sum}'>&nbsp;</td></tr>";
        $html.="</tfoot>";
        return $html;
    }

    //下載
    public function downExcel($excelData){
        if(!is_array($excelData)){
            $excelData = json_decode($excelData,true);
            $excelData = key_exists("excel",$excelData)?$excelData["excel"]:array();
        }
        $this->validateDate("","");
        $headList = $this->getTopArr();
        $excel = new DownSummary();
        $excel->colTwo=0;
        $excel->SetHeaderTitle(Yii::t("app","Store Comparison")."（{$this->search_city}）");
        $excel->init();
        $excel->setSummaryHeader($headList);
        $excel->setSummaryData($excelData);
        $excel->outExcel(Yii::t("app","Store Comparison"));
    }
}