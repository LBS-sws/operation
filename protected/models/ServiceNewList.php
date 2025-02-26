<?php

class ServiceNewList extends CListPageModel
{
    public $year;
    public $month;


    public function rules()
    {
        return array(
            array('year, month, attr, pageNum, noOfItem, totalRow, searchField, searchValue, orderField, orderType, filter, dateRangeValue','safe',),
        );
    }

    public function getCriteria() {
        return array(
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
            'service_code'=>Yii::t('rank','number code'),
			'code'=>Yii::t('rank','employee code'),
			'name'=>Yii::t('rank','employee name'),
			'city_name'=>Yii::t('rank','city'),
			'service_month'=>Yii::t('rank','month'),
			'service_year'=>Yii::t('rank','year'),
			'service_num'=>Yii::t('rank','service number'),
            'score_num'=>Yii::t('rank','service score'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
	    if(empty($this->year)||!is_numeric($this->year)){
	        $this->year = date("Y");
        }

		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.code,b.name,f.name as city_name 
				from opr_service_new a
				 LEFT JOIN hr$suffix.hr_employee b ON a.employee_id = b.id
				 LEFT JOIN security$suffix.sec_city f ON b.city = f.code
				where b.city in ($city_allow)  
			";
		$sql2 = "select count(a.id) 
				from opr_service_new a
				 LEFT JOIN hr$suffix.hr_employee b ON a.employee_id = b.id
				 LEFT JOIN security$suffix.sec_city f ON b.city = f.code
				where b.city in ($city_allow)  
			";
		$clause = " and a.service_year='{$this->year}' ";
		if(!empty($this->month)&&is_numeric($this->month)){
		    $clause.=" and a.service_month='{$this->month}' ";
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

		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by a.id desc ";
        }

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();

		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
					$this->attr[] = array(
						'id'=>$record['id'],
						'service_year'=>$record['service_year'].Yii::t("rank","year unit"),
						'service_month'=>$record['service_month'].Yii::t("rank","month unit"),
						'service_code'=>$record['service_code'],
						'service_num'=>$record['service_num'],
						'score_num'=>$record['score_num'],
						'name'=>$record['name']." ({$record['code']})",
						'city_name'=>$record['city_name'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['serviceNew_c01'] = $this->getCriteria();
		return true;
	}
}
