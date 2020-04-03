<?php

class WarehouseBackController extends Controller
{
	public $function_id='YD09';

    public function filters()
    {
        return array(
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
/*            array('allow',
                'actions'=>array('new','edit'),
                'expression'=>array('WarehouseBackController','allowReadWrite'),
            ),*/
            array('allow',
                'actions'=>array('index'),
                'expression'=>array('WarehouseBackController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD09');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD09');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new WarehouseBackList;
		if (isset($_POST['WarehouseBackList'])) {
			$model->attributes = $_POST['WarehouseBackList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['warehouseBack_op01']) && !empty($session['warehouseBack_op01'])) {
				$criteria = $session['warehouseBack_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

}
