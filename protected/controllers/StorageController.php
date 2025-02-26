<?php

class StorageController extends Controller
{
	public $function_id='YD08';

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
                'actions'=>array('new','edit','delete','storage','draft'),
                'expression'=>array('StorageController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('StorageController','allowReadOnly'),
            ),
            array('allow',
                'actions'=>array('backward'),
                'expression'=>array('StorageController','allowBackward'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public static function allowBackward() {
        return Yii::app()->user->validFunction('YN03');
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD08');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD08');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new StorageList;
		if (isset($_POST['StorageList'])) {
			$model->attributes = $_POST['StorageList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['storage_op01']) && !empty($session['storage_op01'])) {
				$criteria = $session['storage_op01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}

	public function actionDraft()
	{
		if (isset($_POST['StorageForm'])) {
			$model = new StorageForm($_POST['StorageForm']['scenario']);
			$model->attributes = $_POST['StorageForm'];
			if ($model->validate()) {
			    $model->status_type = 0;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('storage/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionStorage()
	{
		if (isset($_POST['StorageForm'])) {
			$model = new StorageForm($_POST['StorageForm']['scenario']);
			$model->attributes = $_POST['StorageForm'];
			if ($model->validate()) {
                $model->status_type = 1;
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('storage/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new StorageForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new StorageForm('new');
        $model->apply_time=date("Y/m/d");
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new StorageForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new StorageForm('delete');
        if (isset($_POST['StorageForm'])) {
            $model->attributes = $_POST['StorageForm'];
            if($model->deleteValidate(0)){
                $model->storage_list = array();
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('storage/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), "已經入庫無法刪除");
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('storage/index'));
        }
    }

    public function actionBackward()
    {
        $model = new StorageForm('edit');
        if (isset($_POST['StorageForm'])) {
            $model->attributes = $_POST['StorageForm'];
            if($model->deleteValidate()){
                $model->backward();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Backward Done'));
            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), "表單異常，請刷新重試");
            }
            $this->redirect(Yii::app()->createUrl('storage/edit',array('index'=>$model->id)));
        }else{
            $this->redirect(Yii::app()->createUrl('storage/index'));
        }
    }

}
