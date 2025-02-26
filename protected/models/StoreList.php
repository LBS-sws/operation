<?php
//2024年9月28日09:28:46

class StoreList extends CListPageModel
{
	/**
	 * Declares customized attribute labels.
	 * If not declared here, an attribute would have a label that is
	 * the same as its name with the first letter in upper case.1
	 */
	public function attributeLabels()
	{
		return array(
			'name'=>Yii::t('procurement','Store Name'),
			'jd_store_no'=>Yii::t('procurement','JD warehouse no'),
			'store_type'=>Yii::t('procurement','store type'),
			'z_display'=>Yii::t('procurement','display'),
			'city_name'=>Yii::t('procurement','city name'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
		$suffix = Yii::app()->params['envSuffix'];
        $city_allow = Yii::app()->user->city_allow();
		$sql1 = "select a.*,b.name as city_name 
				from opr_store a
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code
				where a.city in ({$city_allow})
			";
		$sql2 = "select count(a.id)
				from opr_store a
				LEFT JOIN security{$suffix}.sec_city b ON a.city=b.code
				where a.city in ({$city_allow})
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'name':
					$clause .= General::getSqlConditionClause('a.name',$svalue);
					break;
				case 'jd_store_no':
					$clause .= General::getSqlConditionClause('a.jd_store_no',$svalue);
					break;
				case 'city_name':
					$clause .= General::getSqlConditionClause('b.name',$svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
            $order .= " order by {$this->orderField} ";
			if ($this->orderType=='D') $order .= "desc ";
		}else{
            $order .= " order by b.name asc,a.id desc ";
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
						'name'=>$record['name'],
						'jd_store_no'=>$record['jd_store_no'],
						'city_name'=>$record['city_name'],
                        'store_type'=>$record['store_type']==1?Yii::t('procurement',"store default"):Yii::t('procurement',"store normal"),
                        'z_display'=>$record['z_display']==1?Yii::t('procurement',"show"):Yii::t('procurement',"none"),
					);
			}
		}
		$session = Yii::app()->session;
		$session['store_c01'] = $this->getCriteria();
		return true;
	}

}
