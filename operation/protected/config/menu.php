<?php
//2024年9月28日09:28:46
return array(
    //總部管理員採購
	'Activity'=>array(
		'access'=>'YS',
		'icon'=>'fa-calendar',
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
		'icon'=>'fa-pencil-square-o',
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
            //销售出库审核
            'Sales outbound audit'=>array(
                'access'=>'YD12',
                'url'=>'/salesOutAudit/index',
            ),
            //外勤領貨成本總覽
            'Technician cargo cost'=>array(
                'access'=>'YD07',
                'url'=>'/cargoCost/index',
            ),
            //仓库信息
            'Warehouse Info'=>array(
                'access'=>'YD01',
                'url'=>'/warehouse/index',
            ),
            //仓库信息(金蝶)
            'Store Info'=>array(
                'access'=>'YD10',
                'url'=>'/store/index',
            ),
            //对比金蝶仓库物料
            'Store Comparison'=>array(
                'access'=>'YD11',
                'url'=>'/storeComparison/index',
            ),
            //入库管理
            'Warehouse storage Info'=>array(
                'access'=>'YD08',
                'url'=>'/storage/index',
            ),
            //物品退回列表
            'Warehouse Backward List'=>array(
                'access'=>'YD09',
                'url'=>'/warehouseBack/index',
            ),
            //多訂單限制
            'OrderAcc Info'=>array(
                'access'=>'YD05',
                'url'=>'/orderAcc/form',
				'text'=>'每次额外发送订单给总部也需要此功能授权',
            ),
            //地區審核
            'Area Audit'=>array(
                'access'=>'YD06',
                'url'=>'/areaAudit/index',
            ),
		),
	),
    //技術員入口
	'Technician'=>array(
		'access'=>'YC',
		'icon'=>'fa-truck',
		'items'=>array(
            //訂貨列表(下訂單)
			'Technician List'=>array(
				'access'=>'YC02',
				'url'=>'/technician/index',
			),
            //销售出库(下訂單)
			'Sales outbound'=>array(
				'access'=>'YC03',
				'url'=>'/salesOut/index',
			),
		),
	),

    'Sales Summary'=>array(
        'access'=>'YA',
		'icon'=>'fa-bar-chart',
        'items'=>array(
            'Sales Summary Entry'=>array(
                'access'=>'YA01',
                'url'=>'/monthly/index',
            ),
             'Sales Summary Approval'=>array(
                'access'=>'YA03',
                'url'=>'/monthly/indexa',
           ),
           'Sales Summary Enquiry'=>array(
                'access'=>'YA02',
                'url'=>'/monthly/indexc',
            ),
        ),
    ),

    'Sales Summary - ID'=>array(
        'access'=>'YE',
		'icon'=>'fa-bar-chart',
        'items'=>array(
            'Sales Summary Entry'=>array(
                'access'=>'YE01',
                'url'=>'/monthly2/index',
            ),
             'Sales Summary Approval'=>array(
                'access'=>'YE03',
                'url'=>'/monthly2/indexa',
           ),
           'Sales Summary Enquiry'=>array(
                'access'=>'YE02',
                'url'=>'/monthly2/indexc',
            ),
        ),
    ),

    'Report'=>array(
        'access'=>'YB',
		'icon'=>'fa-file-text-o',
        'items'=>array(
            'Sales Summary'=>array(
                'access'=>'YB02',
                'url'=>'/report/salessummary',
            ),
            'Sales Summary - ID'=>array(
                'access'=>'YB08',
                'url'=>'/report/salessummaryid',
            ),
            'Business Report'=>array(
                'access'=>'YB05',
                'url'=>'/report/business',
            ),
            'Order Records'=>array( //订货记录报表
                'access'=>'YB03',
                'url'=>'/report/orderlist',
            ),
            'Picking List'=>array( //领料记录报表
                'access'=>'YB04',
                'url'=>'/report/pickinglist',
            ),
            'Backward Warehouse'=>array(//仓库退回报表
                'access'=>'YB06',
                'url'=>'/report/backward',
            ),
            'storage Report'=>array( //仓库入库报表
                'access'=>'YB07',
                'url'=>'/report/storage',
            ),
            'Report Manager'=>array(
                'access'=>'YB01',
                'url'=>'/queue/index',
            ),
        ),
    ),

    //物品管理
    'Goods'=>array(
        'access'=>'YG',
		'icon'=>'fa-cubes',
        'items'=>array(
            //进口货物品管理
            'Import Summary Entry'=>array(
                'access'=>'YG01',
                'url'=>'/goodsim/index',
            ),
            //国内货物品管理
            'Domestic Summary Entry'=>array(
                'access'=>'YG04',
                'url'=>'/goodsdo/index',
            ),
            //快速货物品管理
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
            //混合规则管理
            'Goods Hybrid Rules'=>array(
                'access'=>'YG06',
                'url'=>'/rules/index',
            ),
            //城市分配价格
            'Price To City'=>array(
                'access'=>'YG07',
                'url'=>'/PriceCity/index',
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

    'Technical Overall leaderboard'=>array( //技术部综合排行榜
        'access'=>'TL',
        'icon'=>'fa-beer',
        'items'=>array(
            'Technical Month leaderboard'=>array(//技术部综合排行榜(月度)
                'access'=>'TL01',
                'url'=>'/rankingMonth/index',
            ),
            'Technical Quarter leaderboard'=>array(//技术部综合排行榜(季度)
                'access'=>'TL02',
                'url'=>'/rankingQuarter/index',
            ),
            'Technical Half leaderboard'=>array(//技术部综合排行榜(半年度)
                'access'=>'TL03',
                'url'=>'/rankingHalf/index',
            ),
            'Technical Year leaderboard'=>array(//技术部综合排行榜(年度)
                'access'=>'TL04',
                'url'=>'/rankingYear/index',
            ),
            'Technical Other leaderboard'=>array(//分类项目排行榜
                'access'=>'TL08',
                'url'=>'/rankingOther/index',
            ),
            'U system synchronization'=>array(//技术部服務金額（2022/12/20改为U系统同步汇总）
                'access'=>'TL05',
                'url'=>'/serviceMoney/index',
            ),
            'Technical Service new'=>array(//介绍新生意
                'access'=>'TL06',
                'url'=>'/serviceNew/index',
            ),
            'Technical Deduct Marks'=>array(//技术部扣分
                'access'=>'TL07',
                'url'=>'/serviceDeduct/index',
            ),
        ),
    ),

    'Progress leaderboard'=>array( //最佳进步排行榜
        'access'=>'PL',
        'icon'=>'fa-coffee',
        'items'=>array(
            'Progress Month leaderboard'=>array(//最佳进步排行榜(月度)
                'access'=>'PL01',
                'url'=>'/progressMonth/index',
            ),
            'Progress Quarter leaderboard'=>array(//最佳进步排行榜(季度)
                'access'=>'PL02',
                'url'=>'/progressQuarter/index',
            ),
            'Progress Year leaderboard'=>array(//最佳进步排行榜(年度)
                'access'=>'PL03',
                'url'=>'/progressYear/index',
            ),
        ),
    ),
    'JD Curl Notes'=>array(//金蝶同步记录
        'access'=>'ZC',
        'icon'=>'fa-exchange',
        'items'=>array(
            'LBS To JD'=>array(//LBS发送给金蝶
                'access'=>'ZC01',
                'url'=>'/curlNotes/index',
            ),
            'JD To LBS'=>array(//金蝶发送给LBS
                'access'=>'ZC02',
                'url'=>'/curlReceive/index',
            ),
        ),
    ),
);
