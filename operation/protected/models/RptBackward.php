<?php
class RptBackward extends CReport {
	protected function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>15,'align'=>'L'),
			'order_code'=>array('label'=>Yii::t('procurement','Order Code'),'width'=>15,'align'=>'L'),
			'disp_name'=>array('label'=>Yii::t('report','Order User Name'),'width'=>30,'align'=>'L'),
			'goods_code'=>array('label'=>Yii::t('report','Item Code'),'width'=>25,'align'=>'L'),
			'goods_name'=>array('label'=>Yii::t('report','Item Name'),'width'=>30,'align'=>'L'),
			'unit'=>array('label'=>Yii::t('procurement','Unit'),'width'=>15,'align'=>'L'),
			'goods_class'=>array('label'=>Yii::t('report','Item Class'),'width'=>25,'align'=>'L'),
			'back_store_name'=>array('label'=>Yii::t('report','Black Store Name'),'width'=>15,'align'=>'R'),
			'back_num'=>array('label'=>Yii::t('procurement','Black Number'),'width'=>15,'align'=>'R'),
			'back_user'=>array('label'=>Yii::t('procurement','Black User'),'width'=>15,'align'=>'R'),
			'lcd'=>array('label'=>Yii::t('procurement','Black Time'),'width'=>15,'align'=>'C'),
		);
	}
	
	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Date').':'.$this->criteria['START_DT'].' - '.$this->criteria['END_DT'].' / '
			.Yii::t('report','Order Person').':'.$this->criteria['USER_NAMES']
			;
        if (isset($this->criteria['CITY'])&&!empty($this->criteria['CITY'])) {
            $this->subtitle.= empty($this->subtitle)?"":" ；";
            $this->subtitle.= Yii::t('report','City').': ';
            $this->subtitle.= General::getCityNameForList($this->criteria['CITY']);
        }
		return $this->exportExcel();
	}

	public function retrieveData() {
		$start_dt = $this->criteria['START_DT'];
		$end_dt = $this->criteria['END_DT'];
		$city = $this->criteria['CITY'];
		$user_ids = $this->criteria['USER_IDS'];
        $lcu = $this->criteria['UID'];
        if(!General::isJSON($city)){
            $citylist = strpos($city,"'")!==false?$city:"'{$city}'";
        }else{
            $citylist = json_decode($city,true);
            $citylist = "'".implode("','",$citylist)."'";
        }
        $searchSQL=" AND f.city IN ({$citylist})";
        if(!empty($user_ids)){
            $user_ids = "'".str_replace("~","','",$user_ids)."'";
            $searchSQL.=" AND f.username in ({$user_ids})";
        }
        if(!empty($start_dt)){
            $start_dt = date("Y/m/d",strtotime($start_dt));
            $searchSQL.=" AND DATE_FORMAT(a.lcd,'%Y/%m/%d') >= '{$start_dt}'";
        }
        if(!empty($end_dt)){
            $end_dt = date("Y/m/d",strtotime($end_dt));
            $searchSQL.=" AND DATE_FORMAT(a.lcd,'%Y/%m/%d') <= '{$end_dt}'";
        }

        $searchCity=array();//查询的城市
        $searchUser=array();//查询的领料人
        $rows = Yii::app()->db->createCommand()
            ->select("f.city,f.order_code,f.order_user,
            b.goods_code,b.name as goods_name,b.unit,b.jd_classify_name,
            g.name as back_store_name,
            a.back_num,a.lcu as back_user,a.lcd
            ")
            ->from("opr_warehouse_back a")
            ->leftJoin("opr_warehouse b","b.id = a.warehouse_id")
            ->leftJoin("opr_store g","g.id = a.store_id")
            ->leftJoin("opr_order f","f.id = a.order_id")
            ->where("a.order_id>0 AND f.judge = 0 {$searchSQL}")
            ->order("f.audit_time desc,f.id desc")->queryAll();

		if (count($rows) > 0) {
			foreach ($rows as $row) {
                if(key_exists($row["city"],$searchCity)){
                    $cityName = $searchCity[$row["city"]];
                }else{
                    $cityName = CGeneral::getCityName($row["city"]);
                    $searchCity[$row["city"]] = $cityName;
                }
                if(key_exists($row["order_user"],$searchUser)){
                    $employeeName = $searchUser[$row["order_user"]];
                }else{
                    $employeeName = RptPickingList::getUserDisName($row["order_user"]);
                    $searchUser[$row["order_user"]]=$employeeName;
                }
                if(key_exists($row["back_user"],$searchUser)){
                    $backUser = $searchUser[$row["back_user"]];
                }else{
                    $backUser = RptPickingList::getUserDisName($row["back_user"]);
                    $searchUser[$row["back_user"]]=$backUser;
                }
                $temp = array();
				$temp['city_name'] = $cityName;
				$temp['order_code'] = $row['order_code'];
				$temp['order_user'] = $row['order_user'];
				$temp['disp_name'] = $employeeName;
				$temp['goods_code'] = $row['goods_code'];
				$temp['goods_name'] = $row['goods_name'];
				$temp['unit'] = $row['unit'];
				$temp['goods_class'] = $row['jd_classify_name'];
				$temp['back_store_name'] = $row['back_store_name'];
                $temp['back_num'] = floatval($row['back_num']);
				$temp['lcd'] = $row['lcd'];
				$temp['back_user'] = $backUser;
				$this->data[] = $temp;
			}
		}
		return true;
	}

	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		$city_name = '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>