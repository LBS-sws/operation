<?php

class Monthly2Controller extends Controller 
{
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('edit','save','submit','resubmit','fileupload','fileremove'),
				'expression'=>array('Monthly2Controller','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('edit','filedownload','fileupload','fileremove'),
				'expression'=>array('Monthly2Controller','allowReadWriteC'),
			),
			array('allow', 
				'actions'=>array('index','view','filedownload'),
				'expression'=>array('Monthly2Controller','allowReadOnly'),
			),
			array('allow', 
				'actions'=>array('indexc','view','accept','reject','acceptm','rejectm','filedownload'),
				'expression'=>array('Monthly2Controller','allowReadOnlyC'),
			),
			array('allow', 
				'actions'=>array('indexa','view','accept','reject','acceptm','rejectm','filedownload','fileupload','fileremove'),
				'expression'=>array('Monthly2Controller','allowReadOnlyA'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	// To override the checking in components/Controller.php
	public function beforeAction($action) {
		$index_id = $action->getId();
		if (strpos('index/indexa/indexc/edit/view/', $index_id.'/')!==false) {
			if (strpos($index_id,'index')===false) {
				$params = $this->getActionParams();
				if (isset($params['rtn'])) $index_id = $params['rtn'];
			}
			$this->function_id = $index_id=='indexa' ? 'YE03' : ($index_id=='indexc' ? 'YE02' : 'YE01');
			return parent::beforeAction($action);
		} else {
			return true;
		}
	}

	public function actionIndex($pageNum=0) 
	{
		$this->function_id = 'YE01';
		Yii::app()->session['active_func'] = $this->function_id;
		
		$model = new Monthly2List;
		if (isset($_POST['Monthly2List'])) {
			$model->attributes = $_POST['Monthly2List'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ye01']) && !empty($session['criteria_ye01'])) {
				$criteria = $session['criteria_ye01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionIndexc($pageNum=0) 
	{
		$this->function_id = 'YE02';
		Yii::app()->session['active_func'] = $this->function_id;
		
		$model = new Monthly2ConfList;
		if (isset($_POST['Monthly2ConfList'])) {
			$model->attributes = $_POST['Monthly2ConfList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ye02']) && !empty($session['criteria_ye02'])) {
				$criteria = $session['criteria_ye02'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('indexc',array('model'=>$model));
	}

	public function actionIndexa($pageNum=0) 
	{
		$this->function_id = 'YE03';
		Yii::app()->session['active_func'] = $this->function_id;
		
		$model = new Monthly2ApprList;
		if (isset($_POST['Monthly2ApprList'])) {
			$model->attributes = $_POST['Monthly2ApprList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ye03']) && !empty($session['criteria_ye03'])) {
				$criteria = $session['criteria_ye03'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('indexa',array('model'=>$model));
	}

	public function actionAccept()
	{
		if (isset($_POST['Monthly2Form'])) {
			$model = new Monthly2Form($_POST['Monthly2Form']['scenario']);
			$model->attributes = $_POST['Monthly2Form'];
			$model->scenario = 'accept';
			if ($model->validate()) {
				$model->accept();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Acceptance Done'));
				$this->redirect(Yii::app()->createUrl('monthly2/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,'rtn'=>$model->listform));
			}
		}
	}

	public function actionAcceptm()
	{
		if (isset($_POST['Monthly2Form'])) {
			$model = new Monthly2Form($_POST['Monthly2Form']['scenario']);
			$model->attributes = $_POST['Monthly2Form'];
			if ($model->validate()) {
				$model->acceptm();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Acceptance Done'));
				$this->redirect(Yii::app()->createUrl('monthly2/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,'rtn'=>$model->listform));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['Monthly2Form'])) {
			$model = new Monthly2Form($_POST['Monthly2Form']['scenario']);
			$model->attributes = $_POST['Monthly2Form'];
			if ($model->validate()) {
				$model->reject();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Rejection Done'));
				$this->redirect(Yii::app()->createUrl('monthly2/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionRejectm()
	{
		if (isset($_POST['Monthly2Form'])) {
			$model = new Monthly2Form($_POST['Monthly2Form']['scenario']);
			$model->attributes = $_POST['Monthly2Form'];
			if ($model->validate()) {
				$model->rejectm();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Rejection Done'));
				$this->redirect(Yii::app()->createUrl('monthly2/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionResubmit()
	{
		if (isset($_POST['Monthly2Form'])) {
			$model = new Monthly2Form($_POST['Monthly2Form']['scenario']);
			$model->attributes = $_POST['Monthly2Form'];
			$model->scenario = 'resubmit';
			if ($model->validate()) {
				$model->resubmit();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Submission Done'));
				$this->redirect(Yii::app()->createUrl('monthly2/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionSubmit()
	{
		if (isset($_POST['Monthly2Form'])) {
			$model = new Monthly2Form($_POST['Monthly2Form']['scenario']);
			$model->attributes = $_POST['Monthly2Form'];
			$model->scenario = 'submit';
			if ($model->validate()) {
				$model->submit();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Submission Done'));
				$this->redirect(Yii::app()->createUrl('monthly2/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionSave()
	{
		if (isset($_POST['Monthly2Form'])) {
			$model = new Monthly2Form($_POST['Monthly2Form']['scenario']);
			$model->attributes = $_POST['Monthly2Form'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('monthly2/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index, $rtn='index')
	{
		$this->function_id = $rtn=='indexa' ? 'YE03' : ($rtn=='indexc' ? 'YE02' : 'YE01') ;
		Yii::app()->session['active_func'] = $this->function_id;

		$model = new Monthly2Form('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->listform = $rtn;
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index, $rtn='index')
	{
		$this->function_id = $rtn=='indexa' ? 'YE03' : ($rtn=='indexc' ? 'YE02' : 'YE01') ;
		Yii::app()->session['active_func'] = $this->function_id;

		$model = new Monthly2Form('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->listform = $rtn;
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionFileupload($doctype) {
		$model = new Monthly2Form();
		if (isset($_POST['Monthly2Form'])) {
			$model->attributes = $_POST['Monthly2Form'];
			
			$id = $model->id;
			$docman = new DocMan($doctype,$id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
			$docman->fileUpload();
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
	public function actionFileRemove($doctype) {
		$model = new Monthly2Form();
		if (isset($_POST['Monthly2Form'])) {
			$model->attributes = $_POST['Monthly2Form'];
			$docman = new DocMan($doctype,$model->id,get_class($model));
			$docman->masterId = $model->docMasterId[strtolower($doctype)];
			$docman->fileRemove($model->removeFileId[strtolower($doctype)]);
			echo $docman->genTableFileList(false);
		} else {
			echo "NIL";
		}
	}
	
	public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
		$sql = "select city from opr_monthly_hdr where id = $docId";
		$row = Yii::app()->db->createCommand($sql)->queryRow();
		if ($row!==false) {
			$citylist = Yii::app()->user->city_allow();
			if (Yii::app()->user->validFunction('YN07') || strpos($citylist, $row['city']) !== false) {
				$docman = new DocMan($doctype,$docId,'Monthly2Form');
				$docman->masterId = $mastId;
				$docman->fileDownload($fileId);
			} else {
				throw new CHttpException(404,'Access right not match.');
			}
		} else {
				throw new CHttpException(404,'Record not found.');
		}
	}

//	public function actionExportexcel() {
//		if (isset($_POST['MonthlyForm'])) {
//			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
//			$model->attributes = $_POST['MonthlyForm'];
//			$model->exportExcel();
//		}
//	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('YE01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('YE01');
	}

	public static function allowReadWriteC() {
		return Yii::app()->user->validRWFunction('YE02');
	}
	
	public static function allowReadOnlyC() {
		return Yii::app()->user->validFunction('YE02');
	}

	public static function allowReadWriteA() {
		return Yii::app()->user->validRWFunction('YE03');
	}
	
	public static function allowReadOnlyA() {
		return Yii::app()->user->validFunction('YE03');
	}

}
