<?php

return array(
    //物品管理
    'Goods'=>array(
        'access'=>'YG',
        'items'=>array(
            //物品管理
            'Import Summary Entry'=>array(
                'access'=>'YG01',
                'url'=>'/goodsim/index',
            ),
            //物品管理
            'Domestic Summary Entry'=>array(
                'access'=>'YG04',
                'url'=>'/goodsdo/index',
            ),
            //物品管理
            'Fast Summary Entry'=>array(
                'access'=>'YG05',
                'url'=>'/goodsfa/index',
            ),
            //標籤管理
            'Stickies Summary Entry'=>array(
                'access'=>'YG02',
                'url'=>'/stickies/index',
            ),
            //分類管理
            'Classify Summary Entry'=>array(
                'access'=>'YG03',
                'url'=>'/classify/index',
            ),
            //分類管理
            'Goods Hybrid Rules'=>array(
                'access'=>'YG06',
                'url'=>'/rules/index',
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
            //快速訂單
			'Head Email'=>array(
				'access'=>'YS05',
				'url'=>'/email/index',
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
			'Technician List'=>array(
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
