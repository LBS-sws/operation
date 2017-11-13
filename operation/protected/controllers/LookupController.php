<?php

class LookupController extends Controller
{
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'enforceRegisteredStation',
			'enforceSessionExpiration', 
			'enforceNoConcurrentLogin',
			'accessControl', // perform access control for CRUD operations
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
				'actions'=>array('goodex'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all models.
	 */
	
/*
	public function actionStaff($search)
	{
//		$suffix = Yii::app()->params['envSuffix'];
		$suffix = '_w';
		$city = Yii::app()->user->city();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, concat(name, ' (', code, ')') as value from swoper$suffix.swo_staff
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and leave_dt is null or leave_dt=0 or leave_dt > now() ";
		$result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, concat(name, ' (', code, ')',' ".Yii::t('app','(Resign)')."') as value from swoper$suffix.swo_staff
				where (code like '%".$searchx."%' or name like '%".$searchx."%') and city='".$city."'
				and  leave_dt is not null and leave_dt<>0 and leave_dt <= now() ";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
		
		$result = array_merge($result1, $result2);
		$data = TbHtml::listData($result, 'id', 'value');
		echo TbHtml::listBox('lstlookup', '', $data, array('size'=>'15',));
	}
*/

	public function actionGoodEx($search)
	{
		$suffix = Yii::app()->params['envSuffix'];
		$suffix = ($suffix=='dev') ? '_w' : $suffix;
		$city = Yii::app()->user->city();
		$result = array();
		$searchx = str_replace("'","\'",$search);

		$sql = "select id, 'Import' as order_class, goods_code, name from operation$suffix.opr_goods_im
				where (goods_code like '%".$searchx."%' or name like '%".$searchx."%')";
		$result1 = Yii::app()->db->createCommand($sql)->queryAll();

		$sql = "select id, 'Domestic' as order_class, goods_code, name from operation$suffix.opr_goods_do
				where (goods_code like '%".$searchx."%' or name like '%".$searchx."%')";
		$result2 = Yii::app()->db->createCommand($sql)->queryAll();
		
		$sql = "select id, 'Fast' as order_class, goods_code, name from operation$suffix.opr_goods_fa
				where (goods_code like '%".$searchx."%' or name like '%".$searchx."%')";
		$result3 = Yii::app()->db->createCommand($sql)->queryAll();

		$records = array_merge($result1, $result2, $result3);
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['order_class'].':'.$record['id'],
						'value'=>'['.Yii::t('procurement',$record['order_class']).']'.'['.$record['goods_code'].'] '.$record['name'],
					);
			}
		}
		print json_encode($result);
	}

	public function actionTemplate($system) {
		$result = array();
		$suffix = Yii::app()->params['envSuffix'];
		$sql = "select temp_id, temp_name from security$suffix.sec_template
				where system_id='$system'
			";
		$records = Yii::app()->db->createCommand($sql)->queryAll();
		if (count($records) > 0) {
			foreach ($records as $k=>$record) {
				$result[] = array(
						'id'=>$record['temp_id'],
						'name'=>$record['temp_name'],
					);
			}
		}
		print json_encode($result);
	}

//	public function actionSystemDate()
//	{
//		echo CHtml::tag( date('Y-m-d H:i:s'));
//		Yii::app()->end();
//	}
}
