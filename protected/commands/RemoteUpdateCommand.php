<?php
class RemoteUpdateCommand extends CConsoleCommand {

	public function actionMonthlyReport($duration=1, $city='') {
		$param = array();
		$hr = $duration<=1 ? '1 hour' : $duration.' hours';
		$dstr = date('Y-m-d H',strtotime('- '.$hr)).':00:00';
		$sql = "select a.id, a.city, a.year_no, a.month_no, b.data_field, b.data_value, b.manual_input, b.luu 
				from opr_monthly_hdr a, opr_monthly_dtl b 
				where a.id=b.hdr_id and b.lcd<>b.lud and b.lud >= '$dstr'
			";
		if (!empty($city)) $sql .= " and a.city in ('".str_replace(",","','",$city)."')";
		$sql .= " order by a.city, a.year_no, a.month_no, b.data_field";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$lastid = 0;
			$temp = array();
			foreach ($rows as $row) {
				if ($lastid!=$row['id']) {
					if (!empty($temp)) $param[] = $temp;
					$lastid = $row['id'];
					$temp = array(
								'city'=>$row['city'], 
								'year_no'=>$row['year_no'],
								'month_no'=>$row['month_no'],
								'data'=>array(),
							);
				}
				$temp['data'][] = array(
									'data_field'=>$row['data_field'],
									'data_value'=>$row['data_value'],
									'manual_input'=>$row['manual_input'],
									'luu'=>$row['luu'],
								);
			}
			if (!empty($temp)) $param[] = $temp;
			
			$data = json_encode($param);
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			curl_setopt($curl, CURLOPT_URL, 'http://dms.lbsapps.cn/apiu/operation/updatemonthlyreport');
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(
				'Authorization: SvrKey 2j23923290jk238293wesjksd8238201',
				'Content-Type: application/json',
			));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			$result = curl_exec($curl);
			curl_close($curl);
			if (!empty($result)) {
				$rtn = json_decode($result);
				if (isset($rtn->message) && !empty($rtn->message)) echo "MSG: ".$rtn->message."\n";
				if (isset($rtn->error) && !empty($rtn->error)) echo "ERR: ".$rtn->error."\n";
			}
		}
	}

	public function actionMonthlyReportCN($duration=1, $city='') {
		$temp = array('duration'=>$duration, 'city'=>$city);
		$param = json_encode($temp);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
		curl_setopt($curl, CURLOPT_URL, 'http://dms.lbsapps.cn/apiu/operation/listmonthlyreport');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Authorization: SvrKey 2j23923290jk238293wesjksd8238201',
			'Content-Type: application/json',
		));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		$result = curl_exec($curl);
		curl_close($curl);
		if (!empty($result)) {
			$data = json_decode($result);

			$message = '';
			$suffix = Yii::app()->params['envSuffix'];
			$connection = Yii::app()->db;
			$transaction=$connection->beginTransaction();
			
			try {
				foreach ($data as $row) {
					$city = $row->city;
					$year = $row->year_no;
					$month = $row->month_no;
					$sql = "select id
							from operation$suffix.opr_monthly_hdr where city='$city' and year_no=$year and month_no=$month";
					$rs = Yii::app()->db->createCommand($sql)->queryRow();
					if ($rs!==false) {
						$id = $rs['id'];
						foreach ($row->data as $item) {
							$sql2 = "update operation$suffix.opr_monthly_dtl 
										set data_value=:datavalue, manual_input=:minput, luu=:luu
									where hdr_id=:id and data_field=:datafield
								";
							$command=$connection->createCommand($sql2);
							if (strpos($sql2,':id')!==false)
								$command->bindParam(':id',$id,PDO::PARAM_INT);
							if (strpos($sql2,':datafield')!==false)
								$command->bindParam(':datafield',$item->data_field,PDO::PARAM_STR);
							if (strpos($sql2,':datavalue')!==false)
								$command->bindParam(':datavalue',$item->data_value,PDO::PARAM_STR);
							if (strpos($sql2,':minput')!==false)
								$command->bindParam(':minput',$item->manual_input,PDO::PARAM_STR);
							if (strpos($sql2,':luu')!==false)
								$command->bindParam(':luu',$item->luu,PDO::PARAM_STR);
							$command->execute();
							$message .= "CITY: $city /YEAR: $year /MTH: $month /FLD: ".$item->data_field." /VAL: ".$item->data_value."\n";
						}
					}
				}
				$transaction->commit();
				echo $message."\n";
				
			} catch(Exception $e) {
				echo "ERR: ".$e->getMessage();
				$transaction->rollback();
			}
		}
	}
}
?>
