<?php

class WarehouseList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(	
			'goods_code'=>Yii::t('procurement','Goods Code'),
			'name'=>Yii::t('procurement','Name'),
			'unit'=>Yii::t('procurement','Unit'),
			'inventory'=>Yii::t('procurement','Inventory'),
			'price'=>Yii::t('procurement','Price（RMB）'),
            'classify_id'=>Yii::t('procurement','Classify'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select *
				from opr_warehouse
				where city = '$city' 
			";
		$sql2 = "select count(id)
				from opr_warehouse
				where city = '$city' 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'goods_code':
					$clause .= General::getSqlConditionClause('goods_code', $svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('name', $svalue);
					break;
				case 'type':
					$clause .= General::getSqlConditionClause('type', $svalue);
					break;
				case 'unit':
					$clause .= General::getSqlConditionClause('unit', $svalue);
					break;
				case 'price':
					$clause .= General::getSqlConditionClause('price', $svalue);
					break;
				case 'inventory':
					$clause .= General::getSqlConditionClause('inventory', $svalue);
					break;
				case 'classify_id':
					$clause .= $this->getClassifyToSql($svalue);
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
						'name'=>$record['name'],
						'unit'=>$record['unit'],
						'unit'=>$record['unit'],
						'classify_id'=>ClassifyForm::getClassifyToId($record['classify_id']),
						'price'=>$record['price'],
						'inventory'=>$record['inventory'],
						'goods_code'=>$record['goods_code'],
						'goodsHistory'=>$this->getGoodsHistory($record['id']),
					);
			}
		}
		$session = Yii::app()->session;
		$session['warehouse_ya01'] = $this->getCriteria();
		return true;
	}

	//獲取物品由訂單扣減的歷史 (最多顯示5條)
	private function getGoodsHistory($goods_id){
	    if (empty($goods_id)){
	        return "";
        }
        $rows = Yii::app()->db->createCommand()->select("*")
            ->from("opr_order_goods")
            ->where('goods_id=:goods_id and order_status="finished"',array(':goods_id'=>$goods_id))
            ->order('lud desc')->limit(5)->queryAll();
	    if($rows){
	        return $rows;
        }else{
	        return "";
        }
    }

    //分類的模糊查詢
    private function getClassifyToSql($str){
        $rows = Yii::app()->db->createCommand()->select("id")
            ->from("opr_classify")
            ->where("class_type = 'Warehouse' and name like '%$str%'")->queryAll();
        if($rows){
            $arr = array();
            foreach ($rows as $row){
                array_push($arr,"'".$row["id"]."'");
            }
            $sqlStr = implode(",",$arr);
            return " and classify_id in ($sqlStr)";
        }else{
            return " and classify_id in ('')";
        }
    }
}
