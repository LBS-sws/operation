<?php

//技術員模塊的採購控制器
class DeliveryController extends Controller
{
	public function actionIndex($pageNum=0) 
	{
		$model = new DeliveryList;
		if (isset($_POST['DeliveryList'])) {
			$model->attributes = $_POST['DeliveryList'];
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
		if (isset($_POST['DeliveryForm'])) {
			$model = new DeliveryForm($_POST['DeliveryForm']['scenario']);
			$model->attributes = $_POST['DeliveryForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionAudit()
	{
		if (isset($_POST['DeliveryForm'])) {
			$model = new DeliveryForm("audit");
			$model->attributes = $_POST['DeliveryForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = "edit";
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
			} else {
                $model->scenario = "edit";
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

	public function actionReject()
	{
		if (isset($_POST['DeliveryForm'])) {
			$model = new DeliveryForm("reject");
			$model->attributes = $_POST['DeliveryForm'];
			if ($model->validate()) {
				$model->saveData();
				$model->scenario = "edit";
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
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
        $model = new DeliveryForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

	public function actionEdit($index)
	{
		$model = new DeliveryForm('edit');
		if (!$model->retrieveData($index)) {
			throw new CHttpException(404,'The requested page does not exist.');
		} else {
			$this->render('form',array('model'=>$model,));
		}
	}

    public function actionBackward()
    {
        if (isset($_POST['DeliveryForm'])) {
            $model = new DeliveryForm("backward");
            $model->attributes = $_POST['DeliveryForm'];
            if($model->backward()){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Backward Done'));
            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('procurement','Backward Error'));
            }
            $model->scenario = "edit";
            $this->redirect(Yii::app()->createUrl('delivery/edit',array('index'=>$model->id)));
        }
    }


    //下載領料訂單
    public function actionDownorder($index){
        $model = new DeliveryForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $myExcel = new MyExcelTwo();
            $myExcel->setStartRow(5);
            $myExcel->setRowContent("A1","订单编号：".$model->order_code,"F1");
            $myExcel->setRowContent("A2","下单用户：".$model->lcu,"F2");
            $myExcel->setDataHeard($model->getTableHeard());
            $myExcel->setDataBody($model->resetGoodsList());
            $myExcel->outDownExcel($model->lcu."的订单.xls");
        }
    }
}
