<?php
//2024年9月28日09:28:46

class WareClassList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'id'=>"物料id",
			'goods_code'=>"物料编号",
			'name'=>"物料名称",
			'class_str'=>"分类名称",
			'class_report'=>"清洁/灭虫/其它",
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$city = Yii::app()->user->city();
		$sql1 = "select a.id,a.goods_code,a.name,b.class_str,b.class_report
				from opr_warehouse a
				LEFT JOIN opr_warehouse_class b ON a.id=b.warehouse_id
				where a.local_bool=0 
			";
		$sql2 = "select count(a.id)
				from opr_warehouse a
				LEFT JOIN opr_warehouse_class b ON a.id=b.warehouse_id
				where a.local_bool=0 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'goods_code':
					$clause .= General::getSqlConditionClause('a.goods_code', $svalue);
					break;
				case 'name':
					$clause .= General::getSqlConditionClause('a.name', $svalue);
					break;
				case 'class_str':
					$clause .= General::getSqlConditionClause('b.class_str', $svalue);
					break;
				case 'class_report':
					$clause .= General::getSqlConditionClause('b.class_report', $svalue);
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
						'goods_code'=>$record['goods_code'],
						'name'=>$record['name'],
						'class_str'=>$record['class_str'],
						'class_report'=>$record['class_report'],
					);
			}
		}
		$session = Yii::app()->session;
		$session['wareClass_ya01'] = $this->getCriteria();
		return true;
	}

}
