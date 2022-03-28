<?php

class RankingMonthList extends CListPageModel
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
        $citySql = "b.city in ($city_allow)";
        if(Yii::app()->user->validFunction('YN06')&&$this->allCity==1){
            $citySql = "a.id>0";
        }else{
            $this->allCity=0;
        }
		$sql1 = "select a.id,a.rank_num,a.employee_id,a.score_sum,a.rank_year,a.rank_month,b.code,b.name,f.name as city_name 
				from opr_technician_rank a
				 LEFT JOIN hr$suffix.hr_employee b ON a.employee_id = b.id
				 LEFT JOIN security$suffix.sec_city f ON b.city = f.code
				where $citySql  
			";
		$sql2 = "select count(a.id) 
				from opr_technician_rank a
				 LEFT JOIN hr$suffix.hr_employee b ON a.employee_id = b.id
				 LEFT JOIN security$suffix.sec_city f ON b.city = f.code
				where $citySql  
			";
		$clause = " and a.rank_year='{$this->year}' ";
		if(!empty($this->month)&&is_numeric($this->month)){
		    $clause.=" and a.rank_month='{$this->month}' ";
        }
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'service_code':
					$clause .= General::getSqlConditionClause('a.service_code',$svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
				case 'code':
					$clause .= General::getSqlConditionClause('b.code',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('f.name',$svalue);
					break;
			}
		}

		
		$order = " order by a.score_sum desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
        $rank = ($this->pageNum-1)*$this->noOfItem;
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
                $rank++;
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'rank'=>$rank,
                    'rank_year'=>$record['rank_year'].Yii::t("rank","year unit"),
                    'rank_month'=>$record['rank_month'].Yii::t("rank","month unit"),
                    'score_sum'=>floatval($record['score_sum']),
                    'name'=>$record['name']." ({$record['code']})",
                    'city_name'=>$record['city_name'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['rankingMonth_c01'] = $this->getCriteria();
		return true;
	}

	public static function getAllCityTypeList(){
	    return array(Yii::t("rank","local"),Yii::t("rank","all city"));
    }

	public static function getIndexTypeList(){
	    return array(
	        Yii::t("rank","now month"),
	        Yii::t("rank","now quarter"),
	        Yii::t("rank","now half"),
            Yii::t("rank","now year")
        );
    }

	public static function getIndexAction($type){
	    $arr = array("rankingMonth","rankingQuarter","rankingHalf","rankingYear");
	    if(key_exists($type,$arr)){
            return $arr[$type];
        }else{
	        return "rankingMonth";
        }
    }
}
