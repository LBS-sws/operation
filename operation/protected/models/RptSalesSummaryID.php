<?php
class RptSalesSummaryID extends CReport {
	protected function fields() {
		$year = $this->criteria['YEAR'];
		$month = $this->criteria['MONTH'];
		$city = $this->criteria['CITY'];
				
		$suffix = Yii::app()->params['envSuffix'];
		$exlist = Yii::app()->params['cityExclude'];
		$exclude = empty($exlist) ? '' : " and a.city not in ($exlist) ";

		$allowcities = City::model()->getDescendantList($city);
		$allowcities = "'$city'".(empty($allowcities) ? "" : ",").$allowcities;
		
		$citylist = General::getCityListWithNoDescendant($allowcities);
		$list = '';
		foreach ($citylist as $key=>$value) {
			$list .= (empty($list) ? '' : ',')."'".$key."'";
		}
		$citycond = $this->criteria['HQ']=='Y' ? '' : " and a.city in ($list) ";

		$rtn = array(
			'data_field'=>array('label'=>'','width'=>30,'align'=>'L'),
		);
		$sql = "select
					a.city, c.name as city_name 
				from
					opr_monthly_hdr a, security$suffix.sec_city c
				where 
					a.city=c.code and a.group_id='2' and a.year_no=$year and a.month_no=$month $citycond $exclude
				order by
					a.city
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($rows as $row) {
			$rtn[$row['city']] = array('label'=>$row['city_name'],'width'=>15,'align'=>'R');
		}
		$rtn['total'] = array('label'=>'总合计','width'=>15,'align'=>'R');
		return $rtn;
	}

	public function retrieveData() {
		$this->year = $this->criteria['YEAR'];
		$year = $this->year;
		$this->month = $this->criteria['MONTH'];
		$month = $this->month;
		$city = $this->criteria['CITY'];
				
		$suffix = Yii::app()->params['envSuffix'];
		$exlist = Yii::app()->params['cityExclude'];
		$exclude = empty($exlist) ? '' : " and a.city not in ($exlist) ";

		$allowcities = City::model()->getDescendantList($city);
		$allowcities = "'$city'".(empty($allowcities) ? "" : ",").$allowcities;
		
		$citylist = General::getCityListWithNoDescendant($allowcities);
		$list = '';
		foreach ($citylist as $key=>$value) {
			$list .= (empty($list) ? '' : ',')."'".$key."'";
		}
		$citycond = $this->criteria['HQ']=='Y' ? '' : " and a.city in ($list) ";

		$data = array();
		$sql = "select code, name from opr_monthly_field where group_id='2' and status='Y' order by code";
		$fields = Yii::app()->db->createCommand($sql)->queryAll();
		foreach ($fields as $field) {
			$fieldid = $field['code'];
			$fieldname = $field['name'];
			$data = array('data_field'=>$fieldname);
			$total = 0;

			$sqla = "select a.city, c.name as city_name, b.data_value, 
						workflow$suffix.RequestStatus('OPRPT2',a.id,a.lcd) as wfstatus
					from
						opr_monthly_hdr a, opr_monthly_dtl b, security$suffix.sec_city c
					where 
						a.city=c.code and a.group_id='2' and a.year_no=$year and a.month_no=$month and 
						a.id=b.hdr_id and b.data_field=$fieldid $citycond $exclude
					order by
						a.city
				";
			$rows = Yii::app()->db->createCommand($sqla)->queryAll();
			foreach ($rows as $row) {
				$citycode = $row['city'];
				$amt = $row['wfstatus']=='ED' ? $row['data_value'] : '0';
				$total += $amt;
				$data[$citycode] = $amt;
			}

			$data['total'] = $total;
			$this->data[] = $data;
		}

		return (count($data) > 0);
	}

	public function getReportName() {
		$city_name = isset($this->criteria) ? ' - '.General::getCityName($this->criteria['CITY']) : '';
		return (isset($this->criteria) ? Yii::t('report',$this->criteria['RPT_NAME']) : Yii::t('report','Nil'))
			.($this->criteria['HQ']=='Y' ? '' : $city_name);
	}

	public function genReport() {
		$this->retrieveData();
		$this->title = $this->getReportName();
		$this->subtitle = Yii::t('report','Date').': '.$this->year.'/'.substr('00'.$this->month,-2);
		return $this->exportExcel();
	}
}
?>