<?php

class ActivityList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'activity_code'=>Yii::t('procurement','Activity Code'),
			'activity_title'=>Yii::t('procurement','Activity Title'),
			'start_time'=>Yii::t('procurement','Start Time'),
			'end_time'=>Yii::t('procurement','End Time'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from opr_order_activity
				where id>0 
			";
		$sql2 = "select count(id)
				from opr_order_activity
				where id>0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'activity_code':
					$clause .= General::getSqlConditionClause('activity_code', $svalue);
					break;
				case 'activity_title':
					$clause .= General::getSqlConditionClause('activity_title', $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
		} else
			$order = " order by id desc";

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
						'activity_code'=>$record['activity_code'],
						'activity_title'=>$record['activity_title'],
						'start_time'=>$record['start_time'],
						'end_time'=>$record['end_time']
					);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ya01'] = $this->getCriteria();
		return true;
	}

}
