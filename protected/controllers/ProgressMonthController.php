<?php

class ProgressMonthController extends Controller
{
	public $function_id='PL01';
	
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
				'actions'=>array('index'),
				'expression'=>array('ProgressMonthController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new ProgressMonthList();
		if (isset($_POST['ProgressMonthList'])) {
			$model->attributes = $_POST['ProgressMonthList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['progressMonth_c01']) && !empty($session['progressMonth_c01'])) {
				$criteria = $session['progressMonth_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('PL01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('PL01');
	}
}
