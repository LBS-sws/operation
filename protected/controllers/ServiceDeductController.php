<?php

class ServiceDeductController extends Controller
{
	public $function_id='TL07';
	
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
				'actions'=>array('new','edit','delete','save','fileupload','fileRemove'),
				'expression'=>array('ServiceDeductController','allowReadWrite'),
			),
			array('allow', 
				'actions'=>array('index','view','fileDownload'),
				'expression'=>array('ServiceDeductController','allowReadOnly'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionIndex($pageNum=0) 
	{
		$model = new ServiceDeductList();
		if (isset($_POST['ServiceDeductList'])) {
			$model->attributes = $_POST['ServiceDeductList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['serviceDeduct_c01']) && !empty($session['serviceDeduct_c01'])) {
				$criteria = $session['serviceDeduct_c01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['ServiceDeductForm'])) {
			$model = new ServiceDeductForm($_POST['ServiceDeductForm']['scenario']);
			$model->attributes = $_POST['ServiceDeductForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = 'edit';
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('serviceDeduct/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new ServiceDeductForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionNew()
	{
		$model = new ServiceDeductForm('new');
		$model->deduct_date = date("Y/m/d");
		$this->render('form',array('model'=>$model,));
	}
	
	public function actionEdit($index)
	{
		$model = new ServiceDeductForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}
	
	public function actionDelete()
	{
		$model = new ServiceDeductForm('delete');
		if (isset($_POST['ServiceDeductForm'])) {
			$model->attributes = $_POST['ServiceDeductForm'];
			if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('serviceDeduct/index'));
			} else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model));
			}
		}
	}

    public function actionFileupload($doctype) {
        $model = new ServiceDeductForm();
        if (isset($_POST['ServiceDeductForm'])) {
            $model->attributes = $_POST['ServiceDeductForm'];

            $id = ($_POST['ServiceDeductForm']['scenario']=='new') ? 0 : $model->id;
            $docman = new DocMan($doctype,$id,get_class($model));
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            if (isset($_FILES[$docman->inputName])) $docman->files = $_FILES[$docman->inputName];
            $docman->fileUpload();
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileRemove($doctype) {
        $model = new ServiceDeductForm();
        if (isset($_POST['ServiceDeductForm'])) {
            $model->attributes = $_POST['ServiceDeductForm'];

            $docman = new DocMan($doctype,$model->id,'ServiceDeductForm');
            $docman->masterId = $model->docMasterId[strtolower($doctype)];
            $docman->fileRemove($model->removeFileId[strtolower($doctype)]);
            echo $docman->genTableFileList(false);
        } else {
            echo "NIL";
        }
    }

    public function actionFileDownload($mastId, $docId, $fileId, $doctype) {
        $sql = "select id from opr_service_deduct where id = $docId";
        $row = Yii::app()->db->createCommand($sql)->queryRow();
        if ($row!==false) {
            $docman = new DocMan($doctype,$docId,'ServiceDeductForm');
            $docman->masterId = $mastId;
            $docman->fileDownload($fileId);
        } else {
            throw new CHttpException(404,'Record not found.');
        }
    }

    public static function allowReadWrite() {
		return Yii::app()->user->validRWFunction('TL07');
	}
	
	public static function allowReadOnly() {
		return Yii::app()->user->validFunction('TL07');
	}
}
