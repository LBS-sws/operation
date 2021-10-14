<?php
/* Reimbursement Form */

class ReportY06Form extends CReportForm
{
	public $hq;
	
	protected function rulesEx() {
		return array(
				array('hq','safe'),
			);
	}
	
	protected function queueItemEx() {
		return array(
				'HQ'=>$this->hq,
			);
	}
	
	public function init() {
		$this->id = 'RptSalesSummaryID';
		$this->name = 'ID '.Yii::t('report','Sales Summary Report');
		$this->format = 'EXCEL';
		$this->city = Yii::app()->user->city();
		$this->fields = 'year_no,month_no';
		$this->year = date("Y");
		$this->month = date("m");
		$this->hq = Yii::app()->user->validFunction('YN06') ? 'Y' : 'N';
	}
}
