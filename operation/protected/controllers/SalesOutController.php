<?php

class SalesOutController extends Controller
{
	public $function_id='YC03';

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
                'actions'=>array('new','edit','delete','save','finish','audit','orderGoodsDelete'),
                'expression'=>array('SalesOutController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('SalesOutController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YC03');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YC03');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new TechnicianList;
        $model->jd_order_type=1;
		if (isset($_POST['TechnicianList'])) {
			$model->attributes = $_POST['TechnicianList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['technician_ya01']) && !empty($session['technician_ya01'])) {
				$criteria = $session['technician_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

    public function actionSave()
    {
        if (isset($_POST['TechnicianForm'])) {
            $model = new TechnicianForm($_POST['TechnicianForm']['scenario']);
            $model->attributes = $_POST['TechnicianForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('salesOut/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->statusList = OrderForm::getStatusListToId($model->id);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
    //完成收貨
    public function actionFinish()
    {
        if (isset($_POST['TechnicianForm'])) {
            $model = new TechnicianForm("finish");
            $model->attributes = $_POST['TechnicianForm'];
            $model->saveData();
            $model->scenario = "edit";
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
            $this->redirect(Yii::app()->createUrl('salesOut/edit',array('index'=>$model->id)));
        }
    }

    //提交審核
    public function actionAudit()
    {
        if (isset($_POST['TechnicianForm'])) {
            $scenario =$_POST['TechnicianForm']['scenario'];
            $model = new TechnicianForm("audit");
            $model->attributes = $_POST['TechnicianForm'];
            if ($model->validate()) {
                $model->saveData();
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done and Sent Notification'));
                $this->redirect(Yii::app()->createUrl('salesOut/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->scenario = $scenario;
                $model->statusList = OrderForm::getStatusListToId($model->id);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionView($index)
    {
        $model = new TechnicianForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew($city='')
    {
        $model = new TechnicianForm('new');
        $model->jd_set["jd_order_type"]=1;
		$city = empty($city)?Yii::app()->user->city():$city;
		$model->city=$city;
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new TechnicianForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionDelete()
    {
        $model = new TechnicianForm('delete');
        if (isset($_POST['TechnicianForm'])) {
            $model->attributes = $_POST['TechnicianForm'];
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
        $this->redirect(Yii::app()->createUrl('salesOut/index'));
    }

    public function actionOrderGoodsDelete(){
        if(Yii::app()->request->isAjaxRequest) {//是否ajax请求
            $id = $_POST['id'];
            $rs = OrderGoods::model()->deleteByPk($id);
            echo CJSON::encode(array('status'=>$rs));//Yii 的方法将数组处理成json数据
        }else{
            $this->redirect(Yii::app()->createUrl('order/index'));
        }
    }
}
