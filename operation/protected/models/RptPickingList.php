<?php
class RptPickingList extends CReport {
	protected function fields() {
		return array(
			'city_name'=>array('label'=>Yii::t('misc','City'),'width'=>15,'align'=>'L'),
			'order_code'=>array('label'=>Yii::t('procurement','Order Code'),'width'=>15,'align'=>'L'),
			//'order_user'=>array('label'=>Yii::t('report','Order User ID'),'width'=>15,'align'=>'L'),
			'disp_name'=>array('label'=>Yii::t('report','Order User Name'),'width'=>30,'align'=>'L'),
			'goods_code'=>array('label'=>Yii::t('report','Item Code'),'width'=>25,'align'=>'L'),
			'goods_name'=>array('label'=>Yii::t('report','Item Name'),'width'=>30,'align'=>'L'),
			'unit'=>array('label'=>Yii::t('procurement','Unit'),'width'=>15,'align'=>'L'),
			'goods_class'=>array('label'=>Yii::t('report','Item Class'),'width'=>25,'align'=>'L'),
			'goods_num'=>array('label'=>Yii::t('report','Req. Qty.'),'width'=>15,'align'=>'R'),
			'store_name'=>array('label'=>Yii::t('report','send store'),'width'=>15,'align'=>'R'),
			'confirm_num'=>array('label'=>Yii::t('report','send number'),'width'=>15,'align'=>'R'),
			'lcd'=>array('label'=>Yii::t('report','Apply Date'),'width'=>15,'align'=>'C'),
			'audit_time'=>array('label'=>Yii::t('report','Approved Date'),'width'=>15,'align'=>'C'),
            'note'=>array('label'=>Yii::t('procurement','Demand Note'),'width'=>50,'align'=>'L'),
            'remark'=>array('label'=>Yii::t('procurement','Headquarters Note'),'width'=>50,'align'=>'L'),
            'order_remark'=>array('label'=>Yii::t('procurement','Remark'),'width'=>50,'align'=>'L'),
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
            $searchSQL.=" AND DATE_FORMAT(f.audit_time,'%Y/%m/%d') >= '{$start_dt}'";
        }
        if(!empty($end_dt)){
            $end_dt = date("Y/m/d",strtotime($end_dt));
            $searchSQL.=" AND DATE_FORMAT(f.audit_time,'%Y/%m/%d') <= '{$end_dt}'";
        }

        $searchCity=array();//查询的城市
        $searchUser=array();//查询的领料人
        $rows = Yii::app()->db->createCommand()
            ->select("f.city,f.order_code,f.order_user,f.lcd,f.audit_time,f.remark as order_remark,
            b.goods_code,b.name as goods_name,b.unit,b.jd_classify_name,
            a.id,a.goods_num,a.confirm_num,a.note,a.remark
            ")
            ->from("opr_order_goods a")
            ->leftJoin("opr_warehouse b","b.id = a.goods_id")
            ->leftJoin("opr_order f","f.id = a.order_id")
            ->where("f.judge = 0 AND f.status in ('finished','approve') {$searchSQL}")
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
                    $employeeName = self::getUserDisName($row["order_user"]);
                    $searchUser[$row["order_user"]]=$employeeName;
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
				$temp['goods_num'] = floatval($row['goods_num']);
				$temp['store_name'] = "";
				$temp['confirm_num'] = floatval($row['confirm_num']);
                $temp['lcd'] = General::toDate($row['lcd']);
                $temp['audit_time'] = General::toDate($row['audit_time']);
                $temp['note'] = $row['note'];
                $temp['remark'] = $row['remark'];
                $temp['order_remark'] = $row['order_remark'];
				//查询仓库资料
				$storeLists = Yii::app()->db->createCommand()
                    ->select("a.store_num,b.name")
                    ->from("opr_order_goods_store a")
                    ->leftJoin("opr_store b","b.id = a.store_id")
                    ->where("a.order_goods_id=".$row["id"])->queryAll();
				if($storeLists){
                    foreach ($storeLists as $key=>$storeList){
                        if($key>0){
                            $tempCopy = $temp;
                            $tempCopy['store_name'] = $storeList["name"];
                            $tempCopy['confirm_num'] = floatval($storeList['store_num']);
                            $this->data[] = $tempCopy;
                        }else{
                            $temp['store_name'] = $storeList["name"];
                            $temp['confirm_num'] = floatval($storeList['store_num']);
                        }
                    }
                }
				$this->data[] = $temp;
			}
		}
		return true;
	}

    public static function getUserDisName($username) {
        $suffix = Yii::app()->params['envSuffix'];
        $sql = "select disp_name from security$suffix.sec_user where username='$username'";
        return Yii::app()->db->createCommand($sql)->queryScalar();
    }

	public function getReportName() {
		//$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		$city_name = '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil')).$city_name;
	}
}
?>