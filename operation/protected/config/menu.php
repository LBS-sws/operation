<?php

return array(
	'Data Entry'=>array(
		'access'=>'YA',
		'items'=>array(
			'Sales Summary Entry'=>array(
				'access'=>'YA01',
				'url'=>'/monthly/index',
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
