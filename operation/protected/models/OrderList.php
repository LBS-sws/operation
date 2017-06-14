<?php

class OrderList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'goods_id'=>Yii::t('procurement','Goods Name'),
			'order_num'=>Yii::t('procurement','Order Number'),
			'order_user'=>Yii::t('procurement','Order User'),
			'technician'=>Yii::t('procurement','Technician'),
			'status'=>Yii::t('procurement','Order Status'),
		);
	}

	//根據物品名稱模糊查詢出所有物品ID
	public function getSqlGoodIdToName($goodName){
        $sql = "And goods_id in (";
        //where(array('like', 'name', '%Qiang%'))
        $rs = Yii::app()->db->createCommand()->select("id")->from("opr_goods")->where(array('like', 'name', '%'.$goodName.'%'))->queryAll();
        if(count($rs) < 1){
            $sql = "";
        }else{
            foreach ($rs as $key => $row){
                if ($key != 0){
                    $sql.=",";
                }
                $sql.=$row["id"];
            }
            $sql.=") ";
        }
        return $sql;
    }

	//根據物品id查物品名字
	public function getGoodNameToId($id){
        $rs = Yii::app()->db->createCommand()->select()->from("opr_goods")->where('id=:id',array(':id'=>$id))->queryAll();
        if(count($rs) != 1){
            return "";
        }
        return $rs[0]["name"];
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
				case 'goods_id':
					$clause .= $this->getSqlGoodIdToName($svalue);
					break;
				case 'order_num':
					$clause .= General::getSqlConditionClause('order_num', $svalue);
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
						'goods_id'=>$this->getGoodNameToId($record['goods_id']),
						'order_num'=>$record['order_num'],
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
