<?php

class PriceCityList extends CListPageModel
{
	public function attributeLabels()
	{
		return array(
			'city'=>Yii::t('user','City'),
            'price_type'=>Yii::t('procurement','price type'),
		);
	}
	
	public function retrieveDataByPage($pageNum=1)
	{
        $suffix = Yii::app()->params['envSuffix'];
		$city = Yii::app()->user->city();
		$sql1 = "select a.code,a.name as city_name,b.price_type
				from security$suffix.sec_city a 
				left join opr_city_price b on a.code = b.city 
				where code is not null 
			";
        $sql2 = "select count(a.code) 
				from security$suffix.sec_city a 
				left join opr_city_price b on a.code = b.city 
				where code is not null 
			";
		$clause = "";
		if (!empty($this->searchField) && !empty($this->searchValue)) {
			$svalue = str_replace("'","\'",$this->searchValue);
			switch ($this->searchField) {
				case 'city':
					$clause .= General::getSqlConditionClause('a.name', $svalue);
					break;
				case 'price_type':
					$clause .= General::getSqlConditionClause('b.price_type', $svalue);
					break;
			}
		}
		
		$order = "";
		if (!empty($this->orderField)) {
			$order .= " order by ".$this->orderField." ";
			if ($this->orderType=='D') $order .= "desc ";
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
						'id'=>$record['code'],
						'city_name'=>$record['city_name'],
						'price_type'=>$this->getPriceCityList($record['price_type'],true),
					);
			}
		}
		$session = Yii::app()->session;
		$session['priceCity_op01'] = $this->getCriteria();
		return true;
	}

	public function getPriceCityList($id='',$bool=false){
	    $arr = array(
	        1=>Yii::t("procurement","price one"),
	        2=>Yii::t("procurement","price two")
        );
	    if($bool){
            if(key_exists($id,$arr)){
                return $arr[$id];
            }else{
                return $arr[1];
            }
        }
	    return $arr;
    }
}
