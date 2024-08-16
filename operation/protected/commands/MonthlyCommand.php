 <?php
class MonthlyCommand extends CConsoleCommand {
	protected $webroot;

	protected $year;
	protected $month;
	
//
// Initiate Records in Database for Monthly Report
// TABLE: swo_monthly_hdr, swo_monthly_dtl
//	
	public function actionInitRecord($group='1', $year='', $month='') {
		$this->year = (empty($year)) ? date('Y') : $year;
		$this->month = (empty($month)) ? date('m') : $month;
		echo "YEAR: ".$this->year."\tMONTH: ".$this->month."\n";
        $suffix = Yii::app()->params['envSuffix'];

		//每月删除一次token记录表(开始)
        Yii::app()->db->createCommand()->delete("operation{$suffix}.opr_token_history", 'lcd<:lcd',
            array(':lcd'=>"{$this->year}-{$this->month}-01"));
        //每月删除一次token记录表(结束)

        $rows = Yii::app()->db->createCommand()->select("code")
            ->from("security{$suffix}.sec_city_info")
            ->where("field_id='OPERA' and field_value='1'")//城市开启了“营运报告（营运系统）”权限
            ->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$city = $row['code'];
				echo "CITY: $city\n";
				$sql = "select count(id) from opr_monthly_hdr 
						where city='$city' and group_id='$group' and year_no=".$this->year." and month_no=".$this->month
					;
				$rc = Yii::app()->db->createCommand($sql)->queryScalar();
				if ($rc!==false && $rc==0) {
					echo "RECORD INIT...\n";
					$connection = Yii::app()->db;
					$transaction=$connection->beginTransaction();
				
					try {
						$hid = $this->addHeader($connection, $city, $group);
						$this->addDetail($connection, $hid, $group);
						$transaction->commit();
					} catch(Exception $e) {
						$transaction->rollback();
						echo "EXCEPTION ERROR: ".$e->getMessage()."\n";
						Yii::app()->end();
					}
				}
			}
		}
	}
	
	// Add monthly header records
	protected function addHeader(&$connection, $city, $group='1') {
		$sql = "insert into opr_monthly_hdr(city, year_no, month_no, status, group_id, lcu, luu) 
				values(:city, :year, :month, 'Y', :group, :uid, :uid)
			";
		$uid = 'admin';
		$command=$connection->createCommand($sql);
		if (strpos($sql,':city')!==false) $command->bindParam(':city',$city,PDO::PARAM_STR);
		if (strpos($sql,':year')!==false) $command->bindParam(':year',$this->year,PDO::PARAM_INT);
		if (strpos($sql,':month')!==false) $command->bindParam(':month',$this->month,PDO::PARAM_INT);
		if (strpos($sql,':uid')!==false) $command->bindParam(':uid',$uid,PDO::PARAM_STR);
		if (strpos($sql,':group')!==false) $command->bindParam(':group',$group,PDO::PARAM_STR);
		$command->execute();
		return Yii::app()->db->getLastInsertID();
	}
	
	// Add monthly detail records
	protected function addDetail(&$connection, $hid, $group='1') {
		$select = "select code from opr_monthly_field 
					where status='Y' and group_id='$group'
					order by code
				";
		$rows = Yii::app()->db->createCommand($select)->queryAll();
		if (count($rows) > 0) {
			foreach ($rows as $row) {
				$sql = "insert into opr_monthly_dtl(hdr_id, data_field, lcu, luu) 
						values(:hid, :code, :uid, :uid)
					";
				$uid = 'admin';
				$command=$connection->createCommand($sql);
				if (strpos($sql,':hid')!==false) $command->bindParam(':hid',$hid,PDO::PARAM_INT);
				if (strpos($sql,':code')!==false) $command->bindParam(':code',$row['code'],PDO::PARAM_STR);
				if (strpos($sql,':uid')!==false) $command->bindParam(':uid',$uid,PDO::PARAM_STR);
				$command->execute();
			}
		}
	}
	
}
?>