<?php

class ServiceMoneyController extends Controller
{
	public $function_id='TL05';
	
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
				'actions'=>array('new','edit','delete','save'),
				'expression'=>array('ServiceMoneyController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','test'),
				'expression'=>array('ServiceMoneyController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new ServiceMoneyList();
		if (isset($_POST['ServiceMoneyList'])) {
			$model->attributes = $_POST['ServiceMoneyList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['serviceMoney_c01']) && !empty($session['serviceMoney_c01'])) {
				$criteria = $session['serviceMoney_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['ServiceMoneyForm'])) {
			$model = new ServiceMoneyForm($_POST['ServiceMoneyForm']['scenario']);
			$model->attributes = $_POST['ServiceMoneyForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('serviceMoney/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new ServiceMoneyForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new ServiceMoneyForm('new');
		$model->service_month = date("n");
		$model->service_year = date("Y");
		$this->render('form',array('model'=>$model,));
	}

	public function actionTest($year,$month)
	{
		$model = new ServiceMoneyForm('new');
        $arr = $model->curlJobFee($year,$month);
        if($arr["code"]==1){
            echo "success !!!!!<br/>";
        }else{
            echo "error:<br/>";
        }
        var_dump($arr);
        Yii::app()->end();
	}
	
	public function actionEdit($index)
	{
		$model = new ServiceMoneyForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new ServiceMoneyForm('delete');
		if (isset($_POST['ServiceMoneyForm'])) {
			$model->attributes = $_POST['ServiceMoneyForm'];
			if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('serviceMoney/index'));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('TL05');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('TL05');
	}
}
