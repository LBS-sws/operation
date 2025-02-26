<?php

class RankingQuarterController extends Controller
{
	public $function_id='TL02';
	
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
				'expression'=>array('RankingQuarterController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('RankingQuarterController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new RankingQuarterList();
		if (isset($_POST['RankingQuarterList'])) {
			$model->attributes = $_POST['RankingQuarterList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankingQuarter_c01']) && !empty($session['rankingQuarter_c01'])) {
				$criteria = $session['rankingQuarter_c01'];
				$model->setCriteria($criteria);
			}
		}
        RankingMonthList::setEmployeeToModel($model,$this->function_id);//唯獨權限只能看自己的列表
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionView($index,$rank,$year,$month)
	{
		$model = new RankingQuarterForm('view');
		if (!$model->retrieveData($index,$rank,$year,$month)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index,$rank,$year,$month)
	{
		$model = new RankingQuarterForm('edit');
		if (!$model->retrieveData($index,$rank,$year,$month)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('TL02');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('TL02');
	}
}
