<?php

class MonthlyConfList extends CListPageModel
{
	public $year_no;
	public $month_no;
	
	public function rules()	{
		$rtn1 = parent::rules();
		$rtn2 = array(
			array('year_no, month_no','safe'),
		);
		return array_merge($rtn1, $rtn2);
	}

	public function attributeLabels()
	{
		return array(	
			'city_name'=>Yii::t('misc','City'),
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
			'status'=>Yii::t('misc','Status'),
			'wfstatusdesc'=>Yii::t('workflow','Flow Status'),
		);
	}

	public function init() {
		$this->year_no = date("Y");
		$this->month_no = date("m") + 0;
		parent::init();
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$citylist = Yii::app()->user->city_allow();
		$year = $this->year_no;
		$month = $this->month_no;

		$sql1 = "select a.*, b.name as city_name, 
					(select case workflow$suffix.RequestStatus('OPRPT',a.id,a.lcd)
							when '' then '3DF' 
							when 'PA' then '0PA' 
							when 'PS' then '2PS' 
							when 'ED' then '1ED' 
					end) as wfstatus,
					workflow$suffix.RequestStatusDesc('OPRPT',a.id,a.lcd) as wfstatusdesc
				from opr_monthly_hdr a, security$suffix.sec_city b
				where a.city in ($citylist) and a.city=b.code
				and a.year_no=$year and a.month_no=$month
			";
		$sql2 = "select count(a.id)
				from opr_monthly_hdr a, security$suffix.sec_city b
				where a.city in ($citylist) and a.city=b.code
				and a.year_no=$year and a.month_no=$month
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name', $svalue);
					break;
				case 'year_no':
					$clause .= General::getSqlConditionClause('a.year_no', $svalue);
					break;
				case 'month_no':
					$clause .= General::getSqlConditionClause('a.month_no', $svalue);
					break;
				case 'wfstatusdesc':
					$clause .= General::getSqlConditionClause("workflow$suffix.RequestStatusDesc('OPRPT',a.id,a.lcd)",$svalue);
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by wfstatus desc";

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
						'city'=>$record['city'],
						'city_name'=>$record['city_name'],
						'year_no'=>$record['year_no'],
						'month_no'=>$record['month_no'],
						'wfstatus'=>$record['wfstatus'],
						'wfstatusdesc'=>$record['wfstatusdesc'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ya02'] = $this->getCriteria();
		return true;
	}

	public function getCriteria() {
		$rtn1 = parent::getCriteria();
		$rtn2 = array(
				'year_no'=>$this->year_no,
				'month_no'=>$this->month_no,
			);
		return array_merge($rtn1, $rtn2);
	}
}
