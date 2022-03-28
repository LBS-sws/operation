<?php

class RankingQuarterList extends CListPageModel
{
    public $year;
    public $month;
    public $allCity=0;


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
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
	    if(empty($this->year)||!is_numeric($this->year)){
	        $this->year = date("Y");
        }
	    if(empty($this->month)||!is_numeric($this->month)){
	        $this->month = date("n");
        }
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
        $searchSql = "b.city in ($city_allow)";
        if(Yii::app()->user->validFunction('YN06')&&$this->allCity==1){
            $searchSql = "a.id>0";
        }else{
            $this->allCity=0;
        }
        $searchSql.= " and a.rank_year='{$this->year}' ";
        $monthScope =self::quarterScope($this->month);
        $searchSql.=" and a.rank_month in ({$monthScope}) ";
        $sqlText=Yii::app()->db->createCommand()
            ->select("a.employee_id,max(a.two_num) as two_num,max(a.pin_num) as pin_num,IFNULL(max(a.review_num),0) as review_num,sum(a.other_score) as other_score,b.code,b.name,f.name as city_name")
            ->from("opr_technician_rank a")
            ->leftJoin("hr$suffix.hr_employee b","a.employee_id = b.id")
            ->leftJoin("security$suffix.sec_city f","b.city = f.code")
            ->where("$searchSql")->group("a.employee_id,b.code,b.name,f.name")->getText();
		$sql1 = "select staff.*,(staff.two_num+staff.pin_num+staff.review_num+staff.other_score) as score_sum 
				from ($sqlText) staff  
			";
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
                    'rank_month'=>self::getQuarterList($this->month,true),
                    'score_sum'=>floatval($record['score_sum']),
                    'name'=>$record['name']." ({$record['code']})",
                    'city_name'=>$record['city_name'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['rankingQuarter_c01'] = $this->getCriteria();
		return true;
	}

	public static function quarterScope(&$month){
        $month = is_numeric($month)?$month:1;
	    $list=array();
	    $key = 1;
	    for ($i=1;$i<=12;$i+=3){
	        if($month>=$i&&$month<$i+3){
                $key=$i;
                break;
            }
        }
        $month = $key;
        for ($i=$key;$i<$key+3;$i++){
            $list[]=$i;
        }
        return implode(",",$list);
    }

    public static function getQuarterList($key=0,$bool=false){
	    $list = array(
	        1=>"1-3".Yii::t("rank","month unit"),
	        4=>"4-6".Yii::t("rank","month unit"),
	        7=>"7-9".Yii::t("rank","month unit"),
	        10=>"10-12".Yii::t("rank","month unit"),
        );
	    if($bool){
            if(key_exists($key,$list)){
                return $list[$key];
            }else{
                return $key;
            }
        }else{
	        return $list;
        }
    }
}
