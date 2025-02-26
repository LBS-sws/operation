<?php

class DashboardController extends Controller
{
	public $interactive = false;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl - checksession', // perform access control for CRUD operations
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
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('RankOneList','RankAllList','RankOtherList','RankOtherAllList'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionRankOneList() {
	    $oneType=key_exists("oneType",$_GET)?$_GET["oneType"]:0;
        $model = $this->getModelForType($oneType);
        $model->year = date("Y");
        $model->month = date("n");
        $model->allCity=0;
        $model->noOfItem=10;
        $model->retrieveDataByPage(1,false);
        $rtn = $model->attr;
		echo json_encode($rtn);
	}

	public function actionRankAllList() {
        $allType=key_exists("allType",$_GET)?$_GET["allType"]:0;
        $model = $this->getModelForType($allType);
        $model->year = date("Y");
        $model->month = date("n");
        $model->allCity=1;//全部城市
        $model->noOfItem=30;
        $model->retrieveDataByPage(1,false);
        $rtn = $model->attr;
        echo json_encode($rtn);
	}

    public function actionRankOtherList() {
        $rankNameOne = key_exists("rankNameOne",$_GET)?$_GET["rankNameOne"]:"integral_num";
        $yearTypeOne = key_exists("yearTypeOne",$_GET)?$_GET["yearTypeOne"]:0;
        $model = new RankingOtherList();
        $model->year = date("Y");
        $model->month = date("n");
        $model->rank_type=$rankNameOne;
        $model->resetMonth($yearTypeOne);
        $model->allCity=0;
        $model->noOfItem=10;
        $model->retrieveDataByPage(1,false);
        $rtn = $model->attr;
        echo json_encode($rtn);
    }

    public function actionRankOtherAllList() {
        $rankNameAll = key_exists("rankNameAll",$_GET)?$_GET["rankNameAll"]:"integral_num";
        $yearTypeAll = key_exists("yearTypeAll",$_GET)?$_GET["yearTypeAll"]:0;
        $model = new RankingOtherList();
        $model->year = date("Y");
        $model->month = date("n");
        $model->rank_type=$rankNameAll;
        $model->resetMonth($yearTypeAll);
        $model->allCity=1;
        $model->noOfItem=30;
        $model->retrieveDataByPage(1,false);
        $rtn = $model->attr;
        echo json_encode($rtn);
    }

	public function actionShowRankOneList() {
		$this->layout = "main_nm";
		$this->render('//dashboard/rankOneList',array('popup'=>true));
	}

	private function getModelForType($type){
        switch ($type){
            case 0://本月
                $model = new RankingMonthList();
                break;
            case 1://本季度
                $model = new RankingQuarterList();
                break;
            case 2://本半年度
                $model = new RankingHalfList();
                break;
            case 3://本年度
                $model = new RankingYearList();
                break;
            default:
                $model = new RankingMonthList();
        }
        return $model;
    }
}

?>