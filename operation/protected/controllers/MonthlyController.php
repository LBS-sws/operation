<?php

class MonthlyController extends Controller 
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
				'actions'=>array('edit','save','submit','resubmit'),
				'expression'=>array('MonthlyController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('edit','accept','reject'),
				'expression'=>array('MonthlyController','allowReadWriteC'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('MonthlyController','allowReadOnly'),
			),
			array('allow', 
				'actions'=>array('indexc','view'),
				'expression'=>array('MonthlyController','allowReadOnlyC'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new MonthlyList;
		if (isset($_POST['MonthlyList'])) {
			$model->attributes = $_POST['MonthlyList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ya01']) && !empty($session['criteria_ya01'])) {
				$criteria = $session['criteria_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionIndexc($pageNum=0) 
	{
		$model = new MonthlyConfList;
		if (isset($_POST['MonthlyConfList'])) {
			$model->attributes = $_POST['MonthlyConfList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ya02']) && !empty($session['criteria_ya02'])) {
				$criteria = $session['criteria_ya02'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('indexc',array('model'=>$model));
	}

	public function actionAccept()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->accept();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Acceptance Done'));
				$this->redirect(Yii::app()->createUrl('monthly/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,'rtn'=>$model->listform));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->reject();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Rejection Done'));
				$this->redirect(Yii::app()->createUrl('monthly/view',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionResubmit()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->resubmit();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Submission Done'));
				$this->redirect(Yii::app()->createUrl('monthly/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionSubmit()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->submit();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Submission Done'));
				$this->redirect(Yii::app()->createUrl('monthly/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionSave()
	{
		if (isset($_POST['MonthlyForm'])) {
			$model = new MonthlyForm($_POST['MonthlyForm']['scenario']);
			$model->attributes = $_POST['MonthlyForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('monthly/edit',array('index'=>$model->id,'rtn'=>$model->listform)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index, $rtn='index')
	{
		$model = new MonthlyForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->listform = $rtn;
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionEdit($index, $rtn='index')
	{
		$model = new MonthlyForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$model->listform = $rtn;
			$this->render('form',array('model'=>$model,));
		}
	}
	
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='monthly-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('YA01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('YA01');
	}

	public static function allowReadWriteC() {
		return Yii::app()->user->validRWFunction('YA03');
	}
	
	public static function allowReadOnlyC() {
		return Yii::app()->user->validFunction('YA03');
	}

}
