<?php
//2024年9月28日09:28:46

class CurlReceiveController extends Controller
{
	public $function_id='ZC02';
	
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations 1
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
				'actions'=>array('send','testPayment','testWarehouseFull','testIp','getGoods','getSupplier','testSupplier','System'),
				'expression'=>array('CurlReceiveController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','getAjaxStr'),
				'expression'=>array('CurlReceiveController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

    public function actionGetAjaxStr()
    {
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new CurlReceiveList();
            $id = key_exists("id",$_POST)?$_POST["id"]:0;
            $type = key_exists("type",$_POST)?$_POST["type"]:0;
            $content = $model->getCurlTextForID($id,$type);
            echo CJSON::encode(array("content"=>$content));
        }else{
            $this->redirect(Yii::app()->createUrl('curlBsNotes/index'));
        }
    }

	public function actionIndex($pageNum=0) 
	{
		$model = new CurlReceiveList();
		if (isset($_POST['CurlReceiveList'])) {
			$model->attributes = $_POST['CurlReceiveList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['curlReceive_c01']) && !empty($session['curlReceive_c01'])) {
				$criteria = $session['curlReceive_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionSend($index)
	{
        $model = new CurlReceiveList();
        if($model->sendID($index)){
            Dialog::message(Yii::t('dialog','Information'), "已重新发送");
        }else{
            Dialog::message(Yii::t('dialog','Validation Message'), "数据异常");
        }
        $this->redirect(Yii::app()->createUrl('curlReceive/index'));
	}

    public function actionTestWarehouseFull($index=10)
    {
        $model = new CurlReceiveList();
        $model->testWarehouseFull($index);
        die();
    }

	public function actionTestIp()
	{
        $model = new CurlReceiveList();
        $model->testIp();
        die();
	}

	public function actionGetGoods($city="",$goods="")
	{
        $model = new CurlReceiveList();
        $city_arr = !empty($city)?explode(",",$city):array();
        $goods_arr = !empty($goods)?explode(",",$goods):array();
        $model->getGoods($city_arr,$goods_arr);
        die();
	}

	public function actionGetSupplier($city="")
	{
        $model = new CurlReceiveList();
        $city_arr = !empty($city)?explode(",",$city):array();
        $model->getSupplier($city_arr);
        die();
	}

	public function actionTestPayment($index="")
	{
        $model = new CurlReceiveList();
        $model->testPayment($index);
        die();
	}

    public function actionTestSupplier()
    {
        $model = new CurlReceiveList();
        $model->testSupplier();
        die();
    }

	public function actionSystem($type)
	{
	    set_time_limit(0);
        $model = new CurlReceiveList();
        $model->systemU($type);
        die();
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('ZC02');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('ZC02');
	}
}
