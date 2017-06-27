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
				'access'=>'YA01',
				'url'=>'/goods/index',
			),
            //訂貨管理
            'Order Summary Entry'=>array(
                'access'=>'YA01',
                'url'=>'/order/index',
            ),
            //技術員管理
            'Technician Summary Entry'=>array(
                'access'=>'YA01',
                'url'=>'/technician/index',
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
