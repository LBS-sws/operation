<?php

return array(
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
		'access'=>'YS',
		'items'=>array(
            //訂單活動(下訂單)
            'Add Order'=>array(
                'access'=>'YS05',
                'url'=>'/order/activity',
            ),
            //訂貨列表(下訂單)
            'Order List'=>array(
                'access'=>'YS02',
                'url'=>'/order/index',
            ),
            //區域管理員處理技術員的訂單
            'Technician take Goods'=>array(
                'access'=>'YC01',
                'url'=>'/delivery/index',
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

	'Misc'=>array(
		'access'=>'YA',
		'items'=>array(
			'Sales Summary Entry'=>array(
				'access'=>'YA01',
				'url'=>'/monthly/index',
			),
			'Sales Summary Enquiry'=>array(
				'access'=>'YA03',
				'url'=>'/monthly/indexc',
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
);
