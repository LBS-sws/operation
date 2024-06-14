<?php

class StoreComparisonController extends Controller
{
	public $function_id='YD11';
	
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
				'actions'=>array('ajaxSave'),
				'expression'=>array('StoreComparisonController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','downExcel','getJDData'),
				'expression'=>array('StoreComparisonController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
    public function actionDownExcel()
    {
        $model = new StoreComparisonForm('view');
        if (isset($_POST['StoreComparisonForm'])) {
            $model->attributes = $_POST['StoreComparisonForm'];
            $excelData = key_exists("excel",$_POST)?$_POST["excel"]:array();
            $model->downExcel($excelData);
        }else{
            $model->setScenario("index");
            $this->render('index',array('model'=>$model));
        }
    }

    public function actionGetJDData($city="")
    {
        $city=empty($city)?Yii::app()->user->city():$city;
        $city = $city=="none"?"":$city;
        $jdData = CurlForDelivery::getWarehouseGoodsForJD(array("data"=>array("org_number"=>$city)));
        var_dump($jdData);
        die();
    }

	public function actionIndex()
	{
		$model = new StoreComparisonForm('index');
        $session = Yii::app()->session;
        if (isset($session['storeComparison_c01']) && !empty($session['storeComparison_c01'])) {
            $criteria = $session['storeComparison_c01'];
            $model->setCriteria($criteria);
        }else{
            $model->search_city = Yii::app()->user->city();
        }
		$this->render('index',array('model'=>$model));
	}

	public function actionView()
	{
        $model = new StoreComparisonForm('view');
        if (isset($_POST['StoreComparisonForm'])) {
            $model->attributes = $_POST['StoreComparisonForm'];
            if ($model->validate()) {
                set_time_limit(0);
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
		return Yii::app()->user->validRWFunction('YD11');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('YD11');
	}
}
