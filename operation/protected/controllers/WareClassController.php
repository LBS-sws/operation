<?php

class WareClassController extends Controller
{
	public $function_id='YD13';

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
                'actions'=>array('new','edit','delete','save','downExcel'),
                'expression'=>array('WareClassController','allowReadWrite'),
            ),
            array('allow',
                'actions'=>array('index','view'),
                'expression'=>array('WareClassController','allowReadOnly'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionDownExcel()
    {
        //$model = new WarehouseForm();
        $warehouseList = WareClassForm::downExcel();
        $myExcel = new MyExcelTwo();
        $myExcel->setDataHeard($warehouseList["head"]);
        $myExcel->setDataBody($warehouseList["body"]);
        $myExcel->outDownExcel("未设置分类的仓库物料.xls");
    }

    public static function allowReadWrite() {
        return Yii::app()->user->validRWFunction('YD13');
    }

    public static function allowReadOnly() {
        return Yii::app()->user->validFunction('YD13');
    }
	public function actionIndex($pageNum=0) 
	{
		$model = new WareClassList;
		if (isset($_POST['WareClassList'])) {
			$model->attributes = $_POST['WareClassList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['wareClass_ya01']) && !empty($session['wareClass_ya01'])) {
				$criteria = $session['wareClass_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['WareClassForm'])) {
			$model = new WareClassForm($_POST['WareClassForm']['scenario']);
			$model->attributes = $_POST['WareClassForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('wareClass/edit',array('index'=>$model->warehouse_id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new WareClassForm('view');
		if (!$model->retrieveDataByWareID($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
            $model->validateWare("id",'');
			$this->render('form',array('model'=>$model,));
		}
	}

	public function actionEdit($index)
	{
		$model = new WareClassForm('edit');
		if (!$model->retrieveDataByWareID($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
            $model->validateWare("id",'');
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new WareClassForm('delete');
        if (isset($_POST['WareClassForm'])) {
            $model->attributes = $_POST['WareClassForm'];
            if($model->validate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('wareClass/index'));
            }else{
                $model->scenario = "edit";
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('wareClass/index'));
        }
    }

}
