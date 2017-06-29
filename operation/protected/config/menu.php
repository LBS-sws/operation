<?php

return array(
	'Data Entry'=>array(
		'access'=>'YA',
		'items'=>array(
			'Sales Summary Entry'=>array(
				'access'=>'YA01',
				'url'=>'/monthly/index',
			),
            //物品管理
			'Goods Summary Entry'=>array(
				'access'=>'YA02',
				'url'=>'/goods/index',
			),
		),
	),
	'Report'=>array(
		'access'=>'YB',
		'items'=>array(
			'Sales Summary'=>array(
				'access'=>'YB02',
				'url'=>'/report/salessummary',
			),
			'Report Manager'=>array(
				'access'=>'YB01',
				'url'=>'/queue/index',
			),
		),
	),
    //訂貨管理
	'Order'=>array(
		'access'=>'YS',
		'items'=>array(
            //訂單活動
			'Order Activity'=>array(
				'access'=>'YS03',
				'url'=>'/activity/index',
			),
            //訂貨列表(下訂單)
			'Order List'=>array(
				'access'=>'YS02',
				'url'=>'/order/index',
			),
            //訂貨處理(採購)
			'Order Purchase'=>array(
				'access'=>'YS01',
				'url'=>'/purchase/index',
			),
            //快速訂單
			'Fast Order'=>array(
				'access'=>'YS04',
				'url'=>'/fast/index',
			),
		),
	),
    //技術員入口
	'Technician'=>array(
		'access'=>'YC',
		'items'=>array(
            //訂貨列表(下訂單)
			'Order List'=>array(
				'access'=>'YC02',
				'url'=>'/technician/index',
			),
            //訂貨處理(倉庫出貨)
			'Order Purchase'=>array(
				'access'=>'YC01',
				'url'=>'/delivery/index',
			),
		),
	),

//	'System Setting'=>array(
//		'access'=>'YC',
//		'items'=>array(
//			'AAAA'=>array(
//				'access'=>'YC01',
//				'url'=>'/accttype/index',
//				'tag'=>'@',
//			),
//		),
//	),
);
