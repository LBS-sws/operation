<?php

class RankingMonthController extends Controller
{
	public $function_id='TL01';
	
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
				'actions'=>array('test','edit'),
				'expression'=>array('RankingMonthController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view'),
				'expression'=>array('RankingMonthController','allowReadOnly'),
			),
			array('allow',
				'actions'=>array('ajaxDetail'),
				'expression'=>array('RankingMonthController','allowAjax'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new RankingMonthList();
		if (isset($_POST['RankingMonthList'])) {
			$model->attributes = $_POST['RankingMonthList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['rankingMonth_c01']) && !empty($session['rankingMonth_c01'])) {
				$criteria = $session['rankingMonth_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionView($index,$rank)
	{
		$model = new RankingMonthForm('view');
		if (!$model->retrieveData($index,$rank)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new RankingMonthForm('new');
		$this->render('form',array('model'=>$model,));
	}

    //详情列表的異步請求
    public function actionAjaxDetail(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $model = new RankingMonthForm();
            $html =$model->ajaxDetailForHtml();
            echo CJSON::encode(array('status'=>1,'html'=>$html));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('RankingMonth/index'));
        }
    }

	public function actionTest($year=2022,$month=3)
	{
        set_time_limit(0);
		$model = new RankingMonthForm('new');
        $model->insertTechnician($year,$month,true);
        echo "<p>&nbsp;&nbsp;&nbsp;&nbsp;year:{$year}</p>";
        echo "<p>month:{$month}</p>";
        die("success!");
	}
	
	public function actionEdit($index,$rank)
	{
		$model = new RankingMonthForm('edit');
		if (!$model->retrieveData($index,$rank)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('TL01');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('TL01');
	}

	public static function allowAjax() {
		return Yii::app()->user->validFunction('TL01')||
            Yii::app()->user->validFunction('TL02')||
            Yii::app()->user->validFunction('TL03')||
            Yii::app()->user->validFunction('TL04');
	}
}
