<?php

class OrderList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'order_code'=>Yii::t('procurement','Order Code'),
			'goods_list'=>Yii::t('procurement','Goods List'),
			'order_user'=>Yii::t('procurement','Order User'),
			'technician'=>Yii::t('procurement','Technician'),
			'status'=>Yii::t('procurement','Order Status'),
		);
	}

    //根據訂單id查訂單所有物品
    public function getGoodsListToId($order_id){
        $rs = Yii::app()->db->createCommand()->select("b.name,b.price,b.unit,b.type,a.goods_num,a.confirm_num")
            ->from("opr_order_goods a,opr_goods b")->where('a.order_id=:order_id and a.goods_id = b.id',array(':order_id'=>$order_id))->queryAll();
        return $rs;
    }

	public function retrieveDataByPage($pageNum=1)
	{
	    //order_user = '$userName' OR technician = '$userName'
		$city = Yii::app()->user->city();
		$userName = Yii::app()->user->name;
		$sql1 = "select *
				from opr_order
				where (id>-1) 
			";
		$sql2 = "select count(id)
				from opr_order
				where (id>-1) 
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
					$this->attr[] = array(
						'id'=>$record['id'],
                        'order_code'=>$record['order_code'],
						'goods_list'=>$this->getGoodsListToId($record['id']),
						'order_user'=>$record['order_user'],
						'technician'=>$record['technician'],
						'status'=>$record['status'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['criteria_ya01'] = $this->getCriteria();
		return true;
	}

}
