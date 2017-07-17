<?php

class GoodsfaController extends Controller
{
    public function actionIndex($pageNum=0)
    {
        $model = new GoodsFaList;
        if (isset($_POST['GoodsFaList'])) {
            $model->attributes = $_POST['GoodsFaList'];
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
        if (isset($_POST['GoodsFaForm'])) {
            $model = new GoodsFaForm($_POST['GoodsFaForm']['scenario']);
            $model->attributes = $_POST['GoodsFaForm'];
            if ($model->validate()) {
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Save Done'));
                $this->redirect(Yii::app()->createUrl('goodsfa/edit',array('index'=>$model->id)));
            } else {
                $message = CHtml::errorSummary($model);
                Dialog::message(Yii::t('dialog','Validation Message'), $message);
                $this->render('form',array('model'=>$model,));
            }
        }
    }

    public function actionView($index)
    {
        $model = new GoodsFaForm('view');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionNew()
    {
        $model = new GoodsFaForm('new');
        $this->render('form',array('model'=>$model,));
    }

    public function actionEdit($index)
    {
        $model = new GoodsFaForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $this->render('form',array('model'=>$model,));
        }
    }

    public function actionDelete()
    {
        $model = new GoodsFaForm('delete');
        if (isset($_POST['GoodsFaForm'])) {
            $model->attributes = $_POST['GoodsFaForm'];
            if($model->deleteValidate()){
                $model->saveData();
                Dialog::message(Yii::t('dialog','Information'), Yii::t('dialog','Record Deleted'));
                $this->redirect(Yii::app()->createUrl('goodsfa/index'));
            }else{
                $model->scenario = "edit";
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t("procurement","This goods has been used and cannot be deleted"));
                $this->render('form',array('model'=>$model,));
            }
        }else{
            $this->redirect(Yii::app()->createUrl('goodsfa/index'));
        }
    }

}
