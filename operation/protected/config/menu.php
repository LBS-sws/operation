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
    //總部管理員採購
	'Activity'=>array(
		'access'=>'YS',
		'items'=>array(
            //訂單活動
			'Order Activity'=>array(
				'access'=>'YS03',
				'url'=>'/activity/index',
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
    //區域管理員下單
	'Order'=>array(
		'access'=>'YD',
		'items'=>array(
            //訂單活動(下訂單)
            'Add Order'=>array(
                'access'=>'YD04',
                'url'=>'/order/activity',
            ),
            //訂貨列表(下訂單)
            'Order List'=>array(
                'access'=>'YD03',
                'url'=>'/order/index',
            ),
            //區域管理員處理技術員的訂單
            'Technician take Goods'=>array(
                'access'=>'YD02',
                'url'=>'/delivery/index',
            ),
            //倉庫管理
            'Warehouse Info'=>array(
                'access'=>'YD01',
                'url'=>'/warehouse/index',
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
