<div class="box box-primary" >
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app','Technical Overall leaderboard');?> - <?php echo Yii::t("rank","local")?></h3>

        <div class="pull-right">
            <?php
            $oneType = key_exists("oneType",$_GET)?$_GET["oneType"]:0;
            $rankAction = RankingMonthList::getIndexAction($oneType);
            echo TbHtml::dropDownList("oneType",$oneType,RankingMonthList::getIndexTypeList(),array("id"=>"oneType"));
            ?>
        </div>
    </div>
    <!-- /.box-header -->

    <div class="box-body">
        <div id='rankOneList' class="direct-chat-messages" style="height: 250px;">
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





<?php
$link = Yii::app()->createAbsoluteUrl("dashboard/rankOneList",array("oneType"=>$oneType));
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
				    var a_link = data[i].url+'&rank='+rank;
					line += '<td '+style+'>'+rank+'</td>';
					line += '<td '+style+'>'+data[i].name+'</td>';
					line += '<td '+style+'>'+data[i].city_name+'</td>';
					line += '<td '+style+'>'+data[i].rank_year+'</td>';
					line += '<td '+style+'>'+data[i].rank_month+'</td>';
					line +='<td '+style+'><a href="'+a_link+'" target="_blank">'+data[i].score_sum+'</a></td>';
					line += '</tr>';
				}	
				
				line += '</table>';
				$('#rankOneList').html(line);
			}
		},
		error: function(xhr, status, error) { // if error occured
			var err = eval("(" + xhr.responseText + ")");
			console.log(err.Message);
		},
		dataType:'json'
	});
EOF;
Yii::app()->clientScript->registerScript('rankOneListDisplay',$js,CClientScript::POS_READY);

?>