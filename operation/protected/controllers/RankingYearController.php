<?php

class RankingYearController extends Controller
{
	public $function_id='TL04';
	
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
				'actions'=>array('edit'),
				'expression'=>array('RankingYearController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('RankingYearController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new RankingYearList();
		if (isset($_POST['RankingYearList'])) {
			$model->attributes = $_POST['RankingYearList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankingYear_c01']) && !empty($session['rankingYear_c01'])) {
				$criteria = $session['rankingYear_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionView($index,$rank,$year,$month)
	{
		$model = new RankingYearForm('view');
		if (!$model->retrieveData($index,$rank,$year,$month)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index,$rank,$year,$month)
	{
		$model = new RankingYearForm('edit');
		if (!$model->retrieveData($index,$rank,$year,$month)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('TL04');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('TL04');
	}
}
