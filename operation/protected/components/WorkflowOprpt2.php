<?php
class WorkflowOprpt2 extends WorkflowOprpt {
	public function routeToApprover() {
		$users = $this->getUserByControlRight(array('YN07'), true);//權限問題，07還原成06（沈超修改）, Percy再修改, 07才是正確數值
		if (empty($users)) {
			$user = $this->getRequestData('REQ_USER');
			$this->assignRespUser($user);
		} else {
			foreach ($users as $user) {
				$this->assignRespUser($user);
			}
		}
	}

	protected function getUserByControlRight($access=array(), $all=false) {
		$rtn = array();
		if (!empty($access)) {
			$city = $this->getRequestData('CITY');
			$citylist = City::model()->getAncestorList($city);
			$citylist = empty($citylist) ? "'$city'" : $citylist.",'$city'";
			$citycond = $all ? '' : "and a.city in ($citylist)";
			$suffix = Yii::app()->params['envSuffix'];
			$clause = '';
			foreach ($access as $value) $clause .= ($clause=='' ? '' : ' or ')."b.a_control like '%$value%'";
			$sql = "select a.username
					from security$suffix.sec_user a, security$suffix.sec_user_access b
					where a.username=b.username $citycond
					and ($clause) and a.status='A'
				";
			$rows = $this->connection->createCommand($sql)->queryAll();
			foreach ($rows as $row) $rtn[] = $row['username'];
		}
		return $rtn;
	}

	public function emailPA() {
		$docId = $this->getDocId();
		$year = $this->getRequestData('YEAR');
		$month = $this->getRequestData('MONTH');
		$cityname = $this->getRequestData('CITYNAME');
		$url = Yii::app()->createAbsoluteUrl('monthly2/view',array('index'=>$docId,'rtn'=>'indexa'));
		
		$v = array();
		$v['doc_id'] = $docId;
		$v['send'] = 'Y';
		$v['state'] = 'PA';
		$v['subjtype'] = 'action';
		$v['subject'] = Yii::t('workflow','You have 1 request for ID report approval')
			.' ('.$cityname.', '.$year.'/'.$month.')';
		$v['desc'] = Yii::t('workflow','ID Report Approval');
		$msg_url = str_replace('{url}',$url, Yii::t('workflow',"Please click <a href=\"{url}\" onClick=\"return popup(this,'Operation');\">here</a> to carry out your job."));
		$msg = $this->requestDetail();
		$v['message'] = "<p>$msg</p><p>$msg_url</p>";
		
		$rtn = array();
		$rtn[] = $this->emailGeneric($v);
		return $rtn;
	}

	public function emailPH() {
		$docId = $this->getDocId();
		$year = $this->getRequestData('YEAR');
		$month = $this->getRequestData('MONTH');
		$cityname = $this->getRequestData('CITYNAME');
		$url = Yii::app()->createAbsoluteUrl('monthly2/view',array('index'=>$docId,'rtn'=>'indexa'));
		
		$v = array();
		$v['doc_id'] = $docId;
		$v['send'] = 'Y';
		$v['state'] = 'PH';
		$v['subjtype'] = 'action';
		$v['subject'] = Yii::t('workflow','You have 1 request for ID report approval')
			.' ('.$cityname.', '.$year.'/'.$month.')';
		$v['desc'] = Yii::t('workflow','ID Report Approval');
		$msg_url = str_replace('{url}',$url, Yii::t('workflow',"Please click <a href=\"{url}\" onClick=\"return popup(this,'Operation');\">here</a> to carry out your job."));
		$msg = $this->requestDetail();
		$v['message'] = "<p>$msg</p><p>$msg_url</p>";
		
		$rtn = array();
		$rtn[] = $this->emailGeneric($v);
		return $rtn;
	}
	
	public function emailA() {
		$docId = $this->getDocId();
		$year = $this->getRequestData('YEAR');
		$month = $this->getRequestData('MONTH');
		$cityname = $this->getRequestData('CITYNAME');
		$user = $this->getRequestData('REQ_USER');

		$toaddr = array($this->getEmail($user));
		$ccaddr = $this->getLastStateRespEmail();
		$approver = $this->getLastStateActionRespUser('APPROVE');
		$apprname = "";
		if (!empty($approver)) {
			foreach ($approver as $user) {
				$apprname .= (($apprname=="") ? "" : ", ").$this->getDisplayName($user);
			}
		}
		
		$v = array();
		$v['doc_id'] = $docId;
		$v['send'] = 'Y';
		$v['state'] = 'A';
		$v['to_addr'] = $toaddr;
		$v['cc_addr'] = $ccaddr;
		$v['subjtype'] = 'notice';
		$v['subject'] = Yii::t('workflow','Sales report - ID has been approved by HQ').' ('.$cityname.', '.$year.'/'.$month.')';
		$v['desc'] = Yii::t('workflow','ID Report Approval');
		$msg1 = Yii::t('workflow','Sales Report - ID Approved');
		$msg2 = $this->requestDetail();
		$msg2 .= Yii::t('workflow','Approver').': '.$apprname.'<br>';
		$v['message'] = "<p>$msg1</p><p>$msg2</p>";

		$rtn = array();
		$rtn[] = $this->emailGeneric($v);
		return $rtn;
	}

	public function emailAH() {
		$docId = $this->getDocId();
		$year = $this->getRequestData('YEAR');
		$month = $this->getRequestData('MONTH');
		$cityname = $this->getRequestData('CITYNAME');
		$user = $this->getRequestData('REQ_USER');

		$toaddr = array($this->getEmail($user));
		$ccaddr = $this->getLastStateRespEmail();
		$approver = $this->getLastStateActionRespUser('HDAPPROVE');
		$apprname = "";
		if (!empty($approver)) {
			foreach ($approver as $user) {
				$apprname .= (($apprname=="") ? "" : ", ").$this->getDisplayName($user);
			}
		}
		
		$v = array();
		$v['doc_id'] = $docId;
		$v['send'] = 'Y';
		$v['state'] = 'AH';
		$v['to_addr'] = $toaddr;
		$v['cc_addr'] = $ccaddr;
		$v['subjtype'] = 'notice';
		$v['subject'] = Yii::t('workflow','Sales report - ID has been approved by Manager').' ('.$cityname.', '.$year.'/'.$month.')';
		$v['desc'] = Yii::t('workflow','ID Report Approval');
		$msg1 = Yii::t('workflow','Sales Report - ID Approved');
		$msg2 = $this->requestDetail();
		$msg2 .= Yii::t('workflow','Approver').': '.$apprname.'<br>';
		$v['message'] = "<p>$msg1</p><p>$msg2</p>";

		$rtn = array();
		$rtn[] = $this->emailGeneric($v);
		return $rtn;
	}
	
	public function emailD() {
		$docId = $this->getDocId();
		$year = $this->getRequestData('YEAR');
		$month = $this->getRequestData('MONTH');
		$cityname = $this->getRequestData('CITYNAME');
		$user = $this->getRequestData('REQ_USER');
		$toaddr[] = $this->getEmail($user);
		$ccaddr = $this->getLastStateRespEmail();
		$approver = $this->getLastStateActionRespUser('DENY');
		$apprname = "";
		$reason = "";
		if (!empty($approver)) {
			foreach ($approver as $user) {
				$apprname .= (($apprname=="") ? "" : ", ").$this->getDisplayName($user);
				$reason .= (($reason=="") ? "" : "<br>").$this->getCurrentStateRemarks($user);
			}
		}

		$v = array();
		$v['doc_id'] = $docId;
		$v['send'] = 'Y';
		$v['state'] = 'D';
		$v['to_addr'] = $toaddr;
		$v['cc_addr'] = $ccaddr;
		$v['subjtype'] = 'notice';
		$v['subject'] = Yii::t('workflow','Sales report - ID has been denied by HQ').' ('.$cityname.', '.$year.'/'.$month.')';
		$v['desc'] = Yii::t('workflow','ID Report Approval');
		$msg1 = Yii::t('workflow','Sales Report - ID Denied');
		$msg2 = $this->requestDetail();
		$msg2 .= Yii::t('workflow','Reason').': '.$reason.'<br>';
		$msg2 .= Yii::t('workflow','Approver').': '.$apprname.'<br>';
		$v['message'] = "<p>$msg1</p><p>$msg2</p>";

		$rtn = array();
		$rtn[] = $this->emailGeneric($v);
		return $rtn;
	}
	
	public function emailDH() {
		$docId = $this->getDocId();
		$year = $this->getRequestData('YEAR');
		$month = $this->getRequestData('MONTH');
		$cityname = $this->getRequestData('CITYNAME');
		$user = $this->getRequestData('REQ_USER');
		$toaddr[] = $this->getEmail($user);
		$ccaddr = $this->getLastStateRespEmail();
		$approver = $this->getLastStateActionRespUser('HDDENY');
		$apprname = "";
		$reason = "";
		if (!empty($approver)) {
			foreach ($approver as $user) {
				$apprname .= (($apprname=="") ? "" : ", ").$this->getDisplayName($user);
				$reason .= (($reason=="") ? "" : "<br>").$this->getCurrentStateRemarks($user);
			}
		}

		$v = array();
		$v['doc_id'] = $docId;
		$v['send'] = 'Y';
		$v['state'] = 'DH';
		$v['to_addr'] = $toaddr;
		$v['cc_addr'] = $ccaddr;
		$v['subjtype'] = 'notice';
		$v['subject'] = Yii::t('workflow','Sales report -ID has been denied by Manager').' ('.$cityname.', '.$year.'/'.$month.')';
		$v['desc'] = Yii::t('workflow','ID Report Approval');
		$msg1 = Yii::t('workflow','Sales Report - ID Denied');
		$msg2 = $this->requestDetail();
		$msg2 .= Yii::t('workflow','Reason').': '.$reason.'<br>';
		$msg2 .= Yii::t('workflow','Approver').': '.$apprname.'<br>';
		$v['message'] = "<p>$msg1</p><p>$msg2</p>";

		$rtn = array();
		$rtn[] = $this->emailGeneric($v);
		return $rtn;
	}
	
	public function clearFlow() {
		$suffix = Yii::app()->params['envSuffix'];
		$reqId = $this->request_id;
		$this->connection->createCommand("delete from workflow$suffix.wf_request_data where request_id=$reqId")->execute();
		$this->connection->createCommand("delete from workflow$suffix.wf_request_resp_user where request_id=$reqId")->execute();
		$this->connection->createCommand("delete from workflow$suffix.wf_request_transit_log where request_id=$reqId")->execute();
		$this->connection->createCommand("delete from workflow$suffix.wf_request where id=$reqId")->execute();
	}
}
?>