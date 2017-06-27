<?php

class PurchaseController extends Controller
{
	public function actionIndex($pageNum=0) 
	{
		$model = new PurchaseList;
		if (isset($_POST['PurchaseList'])) {
			$model->attributes = $_POST['PurchaseList'];
		} else {
			$session = Yii::app()->session;
			if (isset($session['criteria_ya01']) && !empty($session['fcriteria_ya01'])) {
				$criteria = $session['criteria_ya01'];
				$model->setCriteria($criteria);
			}
		}
		$model->determinePageNum($pageNum);
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['PurchaseForm'])) {
			$model = new PurchaseForm($_POST['PurchaseForm']['scenario']);
			$model->attributes = $_POST['PurchaseForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('purchase/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}
//接受
	public function actionAudit()
	{
		if (isset($_POST['PurchaseForm'])) {
			$model = new PurchaseForm("audit");
			$model->attributes = $_POST['PurchaseForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = "edit";
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('purchase/edit',array('index'=>$model->id)));
			} else {
                $model->scenario = "edit";
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}
//拒絕
    public function actionReject()
    {
        if (isset($_POST['PurchaseForm'])) {
            $model = new PurchaseForm("reject");
            $model->attributes = $_POST['PurchaseForm'];
            if ($model->validate()) {
                $model->saveData();
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('purchase/edit',array('index'=>$model->id)));
            } else {
                $model->scenario = "edit";
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }
	public function actionView($index)
	{
		$model = new PurchaseView('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('view',array('model'=>$model,));
		}
	}

    public function actionDetail($index,$pageNum=1)
    {
        $model = new OrderList;
        if (isset($_POST['OrderList'])) {
            $model->attributes = $_POST['OrderList'];
        } else {
            $session = Yii::app()->session;
            if (isset($session['criteria_ya01']) && !empty($session['fcriteria_ya01'])) {
                $criteria = $session['criteria_ya01'];
                $model->setCriteria($criteria);
            }
        }
        $model->determinePageNum($pageNum);
        if(!is_numeric($index)){
            $index = 0;
        }
        $model->retrieveDataByPage($model->pageNum,$index);
        $this->render('index_detail',array('model'=>$model));
    }

	public function actionEdit($index)
	{
		$model = new PurchaseForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

}
