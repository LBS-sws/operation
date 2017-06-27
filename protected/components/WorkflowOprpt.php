<?php 
class WorkflowOprpt extends WorkflowDMS {

	public function routeToRequestor() {
		$user = $this->getRequestData('REQ_USER');
		$this->assignRespUser($user);
	}

	public function routeToApprover() {
		$users = $this->getUserByControlRight(array('YN01'));
		if (empty($users)) {
			$user = $this->getRequestData('REQ_USER');
			$this->assignRespUser($user);
		} else {
			foreach ($users as $user) {
				$this->assignRespUser($user);
			}
		}
	}

	protected function getUserByControlRight($access=array()) {
		$rtn = array();
		if (!empty($access)) {
			$city = Yii::app()->user->city();
			$citylist = City::model()->getAncestorList($city);
			$citylist = empty($citylist) ? "'$city'" : $citylist.",'$city'";
			$suffix = Yii::app()->params['envSuffix'];
			$clause = '';
			foreach ($access as $value) $clause .= ($clause=='' ? '' : ' or ')."b.a_control like '%$value%'";
			$sql = "select a.username
					from security$suffix.sec_user a, security$suffix.sec_user_access b
					where a.username=b.username and a.city in ($citylist)
					and ($clause) and a.status='A'
				";
			$rows = $this->connection->createCommand($sql)->queryAll();
			foreach ($rows as $row) $rtn[] = $row['username'];
		}
		return $rtn;
	}
}
?>