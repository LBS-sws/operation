<div class="box box-primary" >
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app','Technical Overall leaderboard');?> - <?php echo Yii::t("rank","all city")?></h3>

        <div class="pull-right">
            <?php
            $allType = key_exists("allType",$_GET)?$_GET["allType"]:0;
            $rankAction = RankingMonthList::getIndexAction($allType);
            echo TbHtml::dropDownList("allType",$allType,RankingMonthList::getIndexTypeList(),array("id"=>"allType"));
            ?>
        </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div id='rankAllList' class="direct-chat-messages" style="height: 250px;">
            <div class="overlay">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </div>
    </div>
    <!-- /.box-body -->

    <div class="box-footer">
        <small><?php echo Yii::t('rank','Refresh every day at 6');?></small>
        <?php echo TbHtml::link(Yii::t("rank","detail"),Yii::app()->createUrl($rankAction.'/index'),array("class"=>"pull-right"))?>
    </div>
    <!-- /.box-footer -->
</div>
<!-- /.box -->
<script>
    $(function () {
        $('#oneType,#allType,#rankNameOne,#yearTypeOne,#rankNameAll,#yearTypeAll').change(function(){
            var oneType = $('#oneType').val();
            var allType = $('#allType').val();
            var rankNameOne = $('#rankNameOne').val();
            var yearTypeOne = $('#yearTypeOne').val();
            var rankNameAll = $('#rankNameAll').val();
            var yearTypeAll = $('#yearTypeAll').val();
            window.location.href='<?php echo Yii::app()->createUrl('site/index');?>?oneType='+oneType+'&allType='+allType+'&rankNameOne='+rankNameOne+'&rankNameAll='+rankNameAll+'&yearTypeOne='+yearTypeOne+'&yearTypeAll='+yearTypeAll;
        });
    })
</script>





<?php
$link = Yii::app()->createAbsoluteUrl("dashboard/rankAllList",array("allType"=>$allType));
$paiming= Yii::t('rank','rank');
$staff= Yii::t('rank','employee name');
$city= Yii::t('rank','city');
$month= Yii::t('rank','month');
$year= Yii::t('rank','year');
$f73= Yii::t('rank','Score Sum');
$js = <<<EOF

	$.ajax({
		type: 'GET',
		url: '$link',
		success: function(data) {
			if (data !== undefined) {
				var line = '<table class="table table-bordered small">';
                line += '<tr><td><b>$paiming</b></td><td><b>$staff</b></td><td><b>$city</b></td><td><b>$year</b></td><td><b>$month</b></td><td><b>$f73</b></td></tr>';
				
				for (var i=0; i < data.length; i++) {
					line += '<tr>';
					style = '';
					switch(i) {
						case 0: style = 'style="color:#FF0000"'; break;
						case 1: style = 'style="color:#871F78"'; break;
						case 2: style = 'style="color:#0000FF"'; break;
					}
					rank = i+1;
					line += '<td '+style+'>'+rank+'</td><td '+style+'>'+data[i].name+'</td><td '+style+'>'+data[i].city_name+'</td><td '+style+'>'+data[i].rank_year+'</td><td '+style+'>'+data[i].rank_month+'</td><td '+style+'>'+data[i].score_sum+'</td>';
					line += '</tr>';
				}	
				
				line += '</table>';
				$('#rankAllList').html(line);
			}
		},
		error: function(xhr, status, error) { // if error occured
			var err = eval("(" + xhr.responseText + ")");
			console.log(err.Message);
		},
		dataType:'json'
	});
EOF;
Yii::app()->clientScript->registerScript('rankAllListDisplay',$js,CClientScript::POS_READY);

?>