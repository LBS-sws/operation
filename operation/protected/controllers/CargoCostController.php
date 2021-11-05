<?php

class CargoCostController extends Controller
{
	public $function_id='YD07';

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
            array('allow',
                'actions'=>array('index','view','test'),
                'expression'=>array('CargoCostController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD07');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD07');
    }
	public function actionTest($year=0,$month=0,$city="")
	{
        set_time_limit(0);
	    echo "start<br/>";
		$model = new CargoCostList;
        $model->resetGoodsPrice($year,$month,$city);
        $minLcd="2020/04/09";
        Yii::app()->db->createCommand("update opr_order a set a.total_price=(SELECT ifnull(sum(b.total_price),0) FROM opr_order_goods b WHERE b.order_id=a.id AND date_format(b.lcd,'%Y/%m/%d')>='$minLcd') WHERE date_format(a.lcd,'%Y/%m/%d')>='$minLcd'")->execute();
        echo "end";
        Yii::app()->end();
	}

	public function actionIndex($pageNum=0)
	{
		$model = new CargoCostList;
		if (isset($_POST['CargoCostList'])) {
			$model->attributes = $_POST['CargoCostList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['cargoCost_ya01']) && !empty($session['cargoCost_ya01'])) {
				$criteria = $session['cargoCost_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

    public function actionView($index)
    {
        $model = new CargoCostForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

}
