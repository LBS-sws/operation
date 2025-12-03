<?php

class WareGoodController extends Controller
{
	public $function_id='YD14';
	
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
				'actions'=>array(''),
				'expression'=>array('WareGoodController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel'),
				'expression'=>array('WareGoodController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionDownExcel()
    {
        $model = new WareGoodForm('view');
        if (isset($_POST['WareGoodForm'])) {
            $model->attributes = $_POST['WareGoodForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

	public function actionIndex()
	{
		$model = new WareGoodForm('index');
        $session = Yii::app()->session;
        if (isset($session['wareGood_c01']) && !empty($session['wareGood_c01'])) {
            $criteria = $session['wareGood_c01'];
            $model->setCriteria($criteria);
        }else{
            $thisweek_start = date("Y/m/d",mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y")));
            $thisweek_end = date("Y/m/d",mktime(23,59,59,date("m"),date("d")-date("w")+7,date("Y")));
            $model->start_date = $thisweek_start;
            $model->end_date = $thisweek_end;
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
	    set_time_limit(0);
        $model = new WareGoodForm('view');
        if (isset($_POST['WareGoodForm'])) {
            $model->attributes = $_POST['WareGoodForm'];
            if ($model->validate()) {
                $model->retrieveData();
                $this->render('form',array('model'=>$model));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('index',array('model'=>$model));
            }
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('YD14');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('YD14');
	}
}
