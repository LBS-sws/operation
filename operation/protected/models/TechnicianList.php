<?php

class TechnicianList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'order_code'=>Yii::t('procurement','Order Code'),
			'order_user'=>Yii::t('procurement','Order User'),
            'lcd'=>Yii::t('procurement','Apply time'),
			'technician'=>Yii::t('procurement','Operator User'),
            'lud'=>Yii::t('procurement','Operator Status'),
			'status'=>Yii::t('procurement','Order Status'),
		);
	}

	public function retrieveDataByPage($pageNum=1)
	{
	    //order_user = '$userName' OR technician = '$userName'
		$city = Yii::app()->user->city();
		$userName = Yii::app()->user->name;
		$sql1 = "select *
				from opr_order
				where (status !='pending' AND status !='cancelled') 
			";
		$sql2 = "select count(id)
				from opr_order
				where (status !='pending' AND status !='cancelled') 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'order_code':
					$clause .= General::getSqlConditionClause('order_code', $svalue);
					break;
				case 'order_user':
					$clause .= General::getSqlConditionClause('order_user', $svalue);
					break;
				case 'technician':
					$clause .= General::getSqlConditionClause('technician', $svalue);
					break;
				case 'status':
					$clause .= General::getSqlConditionClause('status', $svalue);
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
                if ($record['status']!="approve" && $record['status'] !="reject"){
                    $operator = "No operations";
                }else{
                    $operator = "already operation";
                }
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'order_code'=>$record['order_code'],
                    'order_user'=>$record['order_user'],
                    'lcd'=>$record['lcd'],
                    'technician'=>$record['technician'],
                    'lud'=>Yii::t("procurement",$operator),
                    'status'=>$record['status'],
                );
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ya01'] = $this->getCriteria();
		return true;
	}

}
