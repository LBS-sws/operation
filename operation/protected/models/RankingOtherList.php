<?php

class RankingOtherList extends CListPageModel
{
    public $year;
    public $month;
    public $year_type;
    public $rank_type;
    public $allCity=0;

    private $monthStr="";
    private $eprSql="";

    public function rules()
    {
        return array(
            array('year,year_type,rank_type,month,allCity, attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public function getCriteria() {
        return array(
            'year_type'=>$this->year_type,
            'rank_type'=>$this->rank_type,
            'allCity'=>$this->allCity,
            'year'=>$this->year,
            'month'=>$this->month,
            'searchField'=>$this->searchField,
            'searchValue'=>$this->searchValue,
            'orderField'=>$this->orderField,
            'orderType'=>$this->orderType,
            'noOfItem'=>$this->noOfItem,
            'pageNum'=>$this->pageNum,
            'filter'=>$this->filter,
            'dateRangeValue'=>$this->dateRangeValue,
        );
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
			'city_name'=>Yii::t('rank','city'),
			'rank_month'=>Yii::t('rank','month'),
			'rank_year'=>Yii::t('rank','year'),
			'score_sum'=>Yii::t('rank','score'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1,$bool=true)
	{
	    if(empty($this->year)||!is_numeric($this->year)){
	        $this->year = date("Y");
        }
        $this->getThisMonth();
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $searchSql = "b.city in ($city_allow)";
        if(Yii::app()->user->validFunction('YN06')&&$this->allCity==1){
            $searchSql = "a.id>0";
        }else{
            $this->allCity=0;
        }
        $searchSql.= " and a.rank_year='{$this->year}' ".$this->eprSql;
        $rankTypeList = self::getRankTypeList();
        if(!key_exists($this->rank_type,$rankTypeList)){
            $this->rank_type = "integral_num";
        }
        if($this->rank_type=="pin_num"){
            $selectSql = "max(a.pin_num) as score_sum";
        }else{
            $selectSql = "sum(a.{$this->rank_type}) as score_sum";
        }
        $sqlText=Yii::app()->db->createCommand()
            ->select("a.employee_id,{$selectSql},b.code,b.name,f.name as city_name")
            ->from("opr_technician_rank a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("security$suffix.sec_city f","b.city = f.code")
            ->where("$searchSql")->group("a.employee_id,b.code,b.name,f.name")->getText();
		$sql1 = "select staff.* from ($sqlText) staff ";
		$sql2 = "select a.employee_id 
				from opr_technician_rank a
				 LEFT JOIN hr$suffix.hr_employee b ON a.employee_id = b.id
				where $searchSql GROUP BY a.employee_id
			";
		$clause = "";

		
		$order = " order by score_sum desc";

		$sql = $sql2.$clause;
		$totalRow = Yii::app()->db->createCommand($sql)->queryAll();
		$this->totalRow = $totalRow?count($totalRow):0;

		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
        $rank = ($this->pageNum-1)*$this->noOfItem;
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $this->attr[] = array(
                    'id'=>$record['employee_id'],
                    'year'=>$this->year,
                    'month'=>$this->month,
                    'rank'=>$rank+$k+1,
                    'rank_year'=>$this->year.Yii::t("rank","year unit"),
                    'rank_month'=>$this->monthStr,
                    'score_sum'=>floatval($record['score_sum']),
                    'name'=>$record['name']." ({$record['code']})",
                    'city_name'=>$record['city_name'],
                );
			}
		}
        $this->searchValue='';
		if($bool){
            $session = Yii::app()->session;
            $session['rankingOther_c01'] = $this->getCriteria();
        }
		return true;
	}

	//營運系統首頁需要調用（顯示本月、本季度、半年度、年度排行）
    public function resetMonth($year_type){
	    $yearTypeList = RankingMonthList::getIndexTypeList();
        $year_type = key_exists($year_type,$yearTypeList)?$year_type:1;
        switch ($year_type){
            case 1: //本季度
                RankingQuarterList::quarterScope($this->month);
                $this->month+=12;
                break;
            case 2: //半年度
                RankingHalfList::quarterHalf($this->month);
                $this->month+=24;
                break;
            case 3: //年度
                $this->month = 32; //1-12月
        }
    }

	public static function getRankTypeList(){
        $list = RankingMonthForm::$sqlDate;
        $arr = array();
        foreach ($list as $key=>$item){
            $name = Yii::t("rank",$item["label"]);
            if(key_exists("minName",$item)){ //如果有簡略名稱，顯示簡稱
                $name = Yii::t("rank",$item["minName"]);
            }
            if(key_exists("otherRank",$item)&&$item["otherRank"]===true){
                $arr[$key] = Yii::t("rank",$item["label"]);
                $arr[$key] = $name;
            }
        }
        return $arr;
    }

	public static function getYearTypeList(){
        $arr = array(
            1=>Yii::t("rank","monthly"),//月度
            2=>Yii::t("rank","quarter"),//季度
            3=>Yii::t("rank","semi-annual"),//半年度
            4=>Yii::t("rank","annual"),//年度
        );
        return $arr;
    }

	public static function getAllMonthList($bool=true){
        $arr = array();
        if($bool){
            $arr['']='';
        }
        for($i=1;$i<=12;$i++){
            $arr[$i] = $i.Yii::t("rank","month unit");
        }
        $arr[13] = "1-3".Yii::t("rank","month unit");
        $arr[16] = "4-6".Yii::t("rank","month unit");
        $arr[19] = "7-9".Yii::t("rank","month unit");
        $arr[22] = "10-12".Yii::t("rank","month unit");
        $arr[25] = "1-6".Yii::t("rank","month unit");
        $arr[31] = "7-12".Yii::t("rank","month unit");
        $arr[32] = "1-12".Yii::t("rank","month unit");
        return $arr;
    }

    private function getThisMonth(){
	    $list = self::getAllMonthList(false);
        $monthScope="";
	    if(key_exists($this->month,$list)){
	        if($this->month<=12){ //月度
                $this->year_type = 1;
                $monthScope=$this->month;
            }elseif($this->month<=22){ //季度
                $this->year_type = 2;
                $monthScope=$this->month-12;
                $monthScope=RankingQuarterList::quarterScope($monthScope);
            }elseif($this->month<=31){ //半年度
                $this->year_type = 3;
                $monthScope=$this->month-24;
                $monthScope=RankingHalfList::quarterHalf($monthScope);
            }else{
                $this->year_type = 4;
            }
        }else{
            $this->year_type = 1;
            $this->month = date("n");
            $monthScope=$this->month;
        }
        $this->monthStr=$list[$this->month];
	    if(!empty($monthScope)){
            $this->eprSql.=" and a.rank_month in ({$monthScope}) ";
        }
    }
}
