<?php

class Monthly2ApprList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
			'city_name'=>Yii::t('misc','City'),
			'status'=>Yii::t('misc','Status'),
			'wfstatusdesc'=>Yii::t('workflow','Flow Status'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1, $type='P')
	{
		$type = Yii::app()->user->validFunction('YN07') ? 'PA' : 'PH';
		
		$wf = new WorkflowOprpt2;
		$wf->connection = Yii::app()->db;
		$list = $wf->getPendingRequestIdList('OPRPT2', $type, Yii::app()->user->id);
		if (empty($list)) $list = '0';
		
		$suffix = Yii::app()->params['envSuffix'];
		$exlist = Yii::app()->params['cityExclude2'];
		$exclude = empty($exlist) ? '' : " and a.city not in ($exlist) ";
		$citylist = Yii::app()->user->validFunction('YN07') ? '' : ' and a.city in ('.Yii::app()->user->city_allow().') ';
		$sql1 = "select a.*, b.name as city_name 
				from opr_monthly_hdr a, security$suffix.sec_city b 
				where a.group_id='2' $citylist and a.city=b.code 
				and a.id in ($list) AND b.ka_bool=0 $exclude
			";
		$sql2 = "select count(a.id)
				from opr_monthly_hdr a, security$suffix.sec_city b 
				where a.group_id='2' $citylist and a.city=b.code 
				and a.id in ($list) AND b.ka_bool=0 $exclude
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'year_no':
					$clause .= General::getSqlConditionClause('a.year_no', $svalue);
					break;
				case 'month_no':
					$clause .= General::getSqlConditionClause('a.month_no', $svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name', $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by a.year_no desc, a.month_no desc";

		$sql = $sql2.$clause;
		$this->totalRow = Yii::app()->db->createCommand($sql)->queryScalar();
		
		$sql = $sql1.$clause.$order;
		$sql = $this->sqlWithPageCriteria($sql, $this->pageNum);
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		
		$list = array();
		$this->attr = array();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$this->attr[] = array(
						'id'=>$record['id'],
						'year_no'=>$record['year_no'],
						'month_no'=>$record['month_no'],
						'city'=>$record['city'],
						'city_name'=>$record['city_name'],
				);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ye03'] = $this->getCriteria();
		return true;
	}

}
