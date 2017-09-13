<?php

class OrderAccController extends Controller
{

    public function actionForm()
    {
        $model = new OrderAccForm();
        $this->render('form',array('model'=>$model,));
    }
	public function actionSave()
	{
		if (isset($_POST['OrderAccForm'])) {
			$model = new OrderAccForm();
			$model->attributes = $_POST['OrderAccForm'];
			if ($model->validate()) {
				$model->saveData();
				Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
				$this->redirect(Yii::app()->createUrl('orderAcc/form'));
			} else {
				$message = CHtml::errorSummary($model);
				Dialog::message(Yii::t('dialog','Validation Message'), $message);
				$this->render('form',array('model'=>$model,));
			}
		}
	}

}
