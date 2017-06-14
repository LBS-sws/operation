<?php

class OrderController extends Controller
{
	public function actionIndex($pageNum=0) 
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
		$model->retrieveDataByPage($model->pageNum);
		$this->render('index',array('model'=>$model));
	}


	public function actionSave()
	{
		if (isset($_POST['OrderForm'])) {
			$model = new OrderForm($_POST['OrderForm']['scenario']);
			$model->attributes = $_POST['OrderForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('order/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $model->statusList = $model->getStatusListToId($model->id);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionView($index)
	{
		$model = new OrderForm('view');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionNew()
    {
        $model = new OrderForm('new');
        $this->render('form',array('model'=>$model,));
    }

	public function actionEdit($index)
	{
		$model = new OrderForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionDelete()
    {
        $model = new OrderForm('delete');
        if (isset($_POST['OrderForm'])) {
            $model->attributes = $_POST['OrderForm'];
            $model->saveData();
            Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
        }
        $this->redirect(Yii::app()->createUrl('order/index'));
    }

}
