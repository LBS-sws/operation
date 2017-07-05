<?php

class MonthlyForm extends CFormModel
{
	public $id;
	public $year_no;
	public $month_no;
	public $lcd;
	public $record = array();
	public $wfstatus;
	public $wfstatusdesc;
	public $city;
	public $city_name;
	public $listform;

	public function attributeLabels()
	{
		return array(
			'city'=>Yii::t('misc','City'),
			'city_name'=>Yii::t('misc','City'),
			'year_no'=>Yii::t('report','Year'),
			'month_no'=>Yii::t('report','Month'),
			'wfstatusdesc'=>Yii::t('workflow','Flow Status'),
		);
	}

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('id, year_no, month_no, lcd, city, city_name, wfstatus, wfstatusdesc, listform','safe'),
			array('record','validateRecord'),
		);
	}

	public function validateRecord($attribute, $params){
		$message = '';
		foreach ($this->record as $data) {
			if (isset($data['updtype']) && $data['updtype']=='M') {
				if (isset($data['fieldtype'])) {
					switch($data['fieldtype']) {
						case 'N':
							if (isset($data['datavalue']) && !empty($data['datavalue']) && !is_numeric($data['datavalue'])) {
								$message = $data['name'].Yii::t('monthly',' is invalid');
								$this->addError($attribute,$message);
							}
						break;
					}
				}
			}
		}
	}

	public function retrieveData($index) {
		$suffix = Yii::app()->params['envSuffix'];
		$citylist = Yii::app()->user->city_allow();
		$sql = "select a.year_no, a.month_no, b.id, b.hdr_id, b.data_field, b.data_value, c.name, c.upd_type, c.field_type, c.function_name, b.manual_input, a.lcd, 
				a.city, d.name as city_name, workflow$suffix.RequestStatus('OPRPT',a.id,a.lcd) as wfstatus,
				workflow$suffix.RequestStatusDesc('OPRPT',a.id,a.lcd) as wfstatusdesc
				from opr_monthly_hdr a, opr_monthly_dtl b, opr_monthly_field c, security$suffix.sec_city d  
				where a.id=$index and a.city in ($citylist)
				and a.id=b.hdr_id and b.data_field=c.code
				and a.city=d.code 
				and c.status='Y'
			";
		$rows = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($rows) > 0) {
			$hid = 0;
			foreach ($rows as $row) {
				if ($hid!=$row['hdr_id']) {
					$hid = $row['hdr_id'];
					$this->id = $hid;
					$this->year_no = $row['year_no'];
					$this->month_no = $row['month_no'];
					$this->city = $row['city'];
					$this->city_name = $row['city_name'];
					$this->lcd = $row['lcd'];
					$this->wfstatus = $row['wfstatus'];
					$this->wfstatusdesc = $row['wfstatusdesc'];
				}
				$temp = array();
				$temp['id'] = $row['id'];
				$temp['code'] = $row['data_field'];
				$temp['function_name'] = $row['function_name'];
				$temp['name'] = $row['name'];
				$temp['datavalue'] = $row['data_value'];
				$temp['datavalueold'] = $row['data_value'];
				$temp['updtype'] = $row['upd_type'];
				$temp['fieldtype'] = $row['field_type'];
				$temp['manualinput'] = $row['manual_input'];
				$this->record[$row['function_name']] = $temp;
			}
		}
		return true;
	}
	
	public function submit()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->saveRequestData('CITY',$this->city);
				$wf->saveRequestData('CITYNAME',$this->city_name);
				$wf->saveRequestData('REQ_USER',Yii::app()->user->id);
				$wf->saveRequestData('YEAR',$this->year_no);
				$wf->saveRequestData('MONTH',$this->month_no);
				$wf->takeAction('SUBMIT');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function resubmit()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('RESUBMIT');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function accept()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('APPROVE');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function reject()
	{
		$wf = new WorkflowOprpt;
		$connection = $wf->openConnection();
		try {
			$this->saveMonthly($connection);
			if ($wf->startProcess('OPRPT',$this->id,$this->lcd)) {
				$wf->takeAction('DENY');
			}
			$wf->transaction->commit();
		}
		catch(Exception $e) {
			$wf->transaction->rollback();
			throw new CHttpException(404,'Cannot update.'.$e->getMessage());
		}
	}

	public function saveData()
	{
		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$this->saveMonthly($connection);
			$transaction->commit();
		}
		catch(Exception $e) {
			$transaction->rollback();
			throw new CHttpException(404,'Cannot update. ('.$e->getMessage().')');
		}
	}

	protected function saveMonthly(&$connection) {
		$sql = '';
		switch ($this->scenario) {
			case 'edit':
				$sql = "update opr_monthly_dtl set
							data_value = :data_value,
							manual_input = :manual_input,
							luu = :uid 
						where id = :id
					";
				break;
		}
		if (empty($sql)) return false;

		$city = $this->city; //Yii::app()->user->city();
		$uid = Yii::app()->user->id;
		
		$select = "select code from opr_monthly_field 
					where status = 'Y'
					order by code
				";
		$rows = Yii::app()->db->createCommand($select)->queryAll();
		foreach ($rows as $row) {
			$code = $row['code'];
			$command=$connection->createCommand($sql);
			if (strpos($sql,':id')!==false)
				$command->bindParam(':id',$this->record[$code]['id'],PDO::PARAM_INT);
			if (strpos($sql,':data_value')!==false)
				$command->bindParam(':data_value',$this->record[$code]['datavalue'],PDO::PARAM_STR);
			if (strpos($sql,':manual_input')!==false) {
				$input = 'N';
				if ($this->record[$code]['updtype']=='M' || $this->record[$code]['datavalueold']==$this->record[$code]['datavalue']) {
					$input = $this->record[$code]['manualinput'];
				} else {
					if ($this->record[$code]['updtype']!='M') {
						$input = ($this->record[$code]['datavalue'] || $this->record[$code]['datavalue']=='0') ? 'N' : 'Y';
					}
				}
				$command->bindParam(':manual_input',$input,PDO::PARAM_STR);
			}
			if (strpos($sql,':uid')!==false)
				$command->bindParam(':uid',$uid,PDO::PARAM_STR);
			$command->execute();
		}
		return true;
	}
	
	public function isReadOnly() {
		return ($this->scenario=='view'|| strpos('~~PS~','~'.$this->wfstatus.'~')===false);
	}
}
