<?php

class PurchaseList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'activity_code'=>Yii::t('procurement','Activity Code'),
			'activity_title'=>Yii::t('procurement','Activity Title'),
			'start_time'=>Yii::t('procurement','Start Time'),
			'end_time'=>Yii::t('procurement','End Time'),
            'activity_status'=>Yii::t('procurement','Activity Status'),
/*			'order_sum'=>Yii::t('procurement','Order Sum'),
			'order_type_sum'=>Yii::t('procurement','Order Type Sum'),*/
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
			    $OrderSumArr = $this->getOrderSumToId($record['id']);
                $this->attr[] = array(
                    'id'=>$record['id'],
                    'activity_code'=>$record['activity_code'],
                    'activity_title'=>$record['activity_title'],
                    'start_time'=>$record['start_time'],
                    'end_time'=>$record['end_time'],
                    'order_sum'=>$OrderSumArr["sum"],
                    'order_type_sum'=>$OrderSumArr["list"],
                    'activity_status'=>$this->compareDate($record['start_time'],$record['end_time'])
                );
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ya01'] = $this->getCriteria();
		return true;
	}

	//對比時間有沒有到期
	public function compareDate($startDate,$endDate){
        $nowDate = date("Y-m-d");
        if (strtotime($nowDate)>strtotime($endDate)){
            return "End";
        }elseif(strtotime($nowDate)<strtotime($startDate)){
            return "Wait";
        }else{
            return "Run";
        }
    }

	//對比時間有沒有到期
	public function getOrderSumToId($activity_id){
	    $arr=array();
        $rows = Yii::app()->db->createCommand()->select("order_class")->from("opr_order")
            ->where('activity_id=:activity_id AND judge=1 AND status != "pending" AND status != "cancelled"', array(':activity_id'=>$activity_id))->queryAll();
        if($rows){
            foreach ($rows as $row){
                if(empty($arr[$row["order_class"]])){
                    $arr[$row["order_class"]] = 0;
                }
                $arr[$row["order_class"]]++;
            }
        }
        return array(
            "sum"=>count($rows),
            "list"=>$arr
        );
    }
}
