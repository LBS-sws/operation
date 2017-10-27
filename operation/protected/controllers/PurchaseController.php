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

    public function actionBackward()
    {
        if (isset($_POST['PurchaseForm'])) {
            $model = new PurchaseForm("backward");
            $model->attributes = $_POST['PurchaseForm'];
            if($model->backward()){
                Dialog::message(Yii::t('dialog','Information'), Yii::t('procurement','Backward Done'));
            }else{
                Dialog::message(Yii::t('dialog','Validation Message'), Yii::t('procurement','Backward Error'));
            }
            $model->scenario = "edit";
            $this->redirect(Yii::app()->createUrl('purchase/edit',array('index'=>$model->id)));
        }
    }

    //下載訂單
    public function actionDownorder($index){
        $model = new PurchaseForm('edit');
        if ($model->validateOrderId($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $title = OrderList::getActivityTitleToId($model->activity_id);
            $myExcel = new MyExcelTwo();
            $arr = $model->getRulesText();
            $num = count($arr)+ 2;
            $myExcel->setStartRow($num+5);
            $myExcel->setRulesArr($arr);
            $myExcel->setRowContent("A".$num,"订单编号：".$model->order_code,"F".$num);
            $myExcel->setRowContent("A".($num+1),"下单用户：".$model->lcu,"F".($num+1));
            $myExcel->setRowContent("A".($num+2),"订单采购标题：".$title,"F".($num+2));
            $myExcel->setDataHeard($model->getTableHeard());
            $myExcel->setDataBody($model->resetGoodsList());
            $myExcel->outDownExcel($model->lcu."的订单.xls");
        }
    }

    //下載訂單(統計)
    public function actionDownactive($index){
        $model = new ActivityForm('edit');
        if (!$model->retrieveData($index)) {
            throw new CHttpException(404,'The requested page does not exist.');
        } else {
            $cityList = PurchaseView::getCityClassToActivityId($index);
            $myExcel = new MyExcelTwo();
            foreach ($cityList as $cityCode => $goodsList){
                $myExcel->addNewSheet($cityCode);
                $myExcel->setRowContent("A1","城市編號：".$goodsList["cityCode"],"F1");
                $myExcel->setRowContent("A2","公司名稱：".$goodsList["cityName"],"F2");
                $myExcel->setRowContent("A3","公司電話：".$goodsList["cityTel"],"F3");
                $myExcel->setRowContent("A4","公司地址：".$goodsList["cityAdr"],"F4");
                $myExcel->setRowContent("A5","公司負責人：".$goodsList["cityUser"]["name"]."   ".$goodsList["cityUser"]["email"],"F5");
                $myExcel->setStartRow(9);
                $myExcel->fillDownExcel($goodsList["goodList"],$model->order_class);
                $myExcel->setProtoValue($cityCode,"cityName",$goodsList["cityName"]);
                $myExcel->setProtoValue($cityCode,"city",$goodsList["city"]);
            }
            $myExcel->addNewSheet("分區統計");
            $myExcel->setStartRow(3);
            $myExcel->countOrderToCity($model->order_class);
            $myExcel->outDownExcel($model->activity_title.".xls");
        }
    }
}
