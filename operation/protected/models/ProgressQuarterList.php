<?php

class ProgressQuarterList extends CListPageModel
{
    public $year;
    public $month;
    public $allCity=0;
    public $staff_id;


    public function rules()
    {
        return array(
            array('year, month,allCity, attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public function getCriteria() {
        return array(
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
            'progress_date'=>Yii::t('rank','progress date'),
            'progress_rate'=>Yii::t('rank','progress rate'),
            'last_sum'=>Yii::t('rank','last sum'),
            'now_sum'=>Yii::t('rank','now sum'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1,$bool=true)
	{
	    if(empty($this->year)||!is_numeric($this->year)){
	        $this->year = date("Y");
        }
	    if(empty($this->month)||!is_numeric($this->month)){
	        $this->month = date("n");
        }
        $lastDate = strtotime("{$this->year}/{$this->month}/01 - 3 months");
        $lastYear = date("Y",$lastDate);
        $lastMonth = date("n",$lastDate);
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $searchSql = "b.city in ($city_allow)";
        if(Yii::app()->user->validFunction('YN10')&&$this->allCity==1){
            $searchSql = "a.id>0";
        }else{
            $this->allCity=0;
        }
        $searchNowSql= $searchSql." and a.rank_year='{$this->year}' ";
        $monthScope =RankingQuarterList::quarterScope($this->month);
        $searchNowSql.=" and a.rank_month in ({$monthScope}) ";
        $searchLastSql= $searchSql." and a.rank_year='$lastYear' ";
        $monthScope =RankingQuarterList::quarterScope($lastMonth);
        $searchLastSql.=" and a.rank_month in ({$monthScope}) ";
        $sqlNowText=Yii::app()->db->createCommand()
            ->select("a.employee_id,b.code,b.name,b.city,(max(a.two_num)+max(a.pin_num)+IFNULL(max(a.review_num),0)+sum(a.other_score)) as now_sum")
            ->from("opr_technician_rank a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where($searchNowSql)->group("a.employee_id,b.code,b.name,b.city")->getText();
        $sqlLastText=Yii::app()->db->createCommand()
            ->select("a.employee_id,(max(a.two_num)+max(a.pin_num)+IFNULL(max(a.review_num),0)+sum(a.other_score)) as last_sum")
            ->from("opr_technician_rank a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->where($searchLastSql)->group("a.employee_id")->getText();
		$sql1 = "select staff.*,d.last_sum,g.name as city_name
				from ($sqlNowText) staff  
				 LEFT JOIN ($sqlLastText) d ON staff.employee_id = d.employee_id
				 LEFT JOIN security$suffix.sec_city g ON staff.city = g.code
			";
		$clause = "";

		
		$order = "";
		$this->totalRow = 0;

		$sql = $sql1.$clause.$order;
		//$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
            $this->totalRow = count($records);
            $arr = array();
			foreach ($records as $k=>$record) {
                $scoreSum = floatval($record['now_sum']);
                $lastSum = floatval($record['last_sum']);
                $progress_rate = $lastSum>2000?round(($scoreSum-$lastSum)/$lastSum*100,2):0;
                $arr[] = array(
                    'id'=>$record['employee_id'],
                    'show'=>true,
                    'year'=>$this->year,
                    'month'=>$this->month,
                    'progress_rate'=>$progress_rate,
                    'progress_date'=>$this->year.Yii::t("rank","year unit").RankingQuarterList::getQuarterList($this->month,true),
                    'score_sum'=>$scoreSum,
                    'last_sum'=>$lastSum,
                    'name'=>$record['name']." ({$record['code']})",
                    'city_name'=>$record['city_name'],
                );
			}
            if(!empty($arr)){//排序
                $edit = array_column($arr,'progress_rate');
                array_multisort($edit,SORT_DESC,$arr);
            }
            $this->attr = $arr;
		}
        $this->searchValue='';
		if($bool){
            $session = Yii::app()->session;
            $session['progressQuarter_c01'] = $this->getCriteria();
        }
		return true;
	}
}
