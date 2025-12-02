
<?php
//2024年9月28日09:28:46
?>
<?php
$this->pageTitle=Yii::app()->name . ' - Warehouse Info';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'warehouse-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>
<style>
    .goodsHistoryMouse{padding: 10px;margin: -10px;}
    .goodsHistoryDiv>.table{background: #fff;}
    .goodsHistoryDiv{position: absolute;width: 400px;margin-left: -205px;margin-top: 10px;box-shadow: 5px 5px 5px #999;background: #eee;z-index: 88;padding: 10px;display: none;}
    .goodsHistoryDiv:after{position: absolute;content: " ";left: 50%;top: -10px;border-bottom: 10px solid #eee;border-left: 5px solid transparent;border-right: 5px solid transparent }
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Warehouse Info'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content visit">
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('YD01'))
                    echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('procurement','Add Goods'), array(
                        'submit'=>Yii::app()->createUrl('warehouse/new'),
                    ));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                //var_dump(Yii::app()->session['rw_func']);
                if (Yii::app()->user->validRWFunction('YD01'))
                    echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('misc','Download'), array(
                        'submit'=>Yii::app()->createUrl('warehouse/DownExcel'),
                    ));

                if (Yii::app()->user->validFunction('YN11')){
                    //導入
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('procurement','Import Price'), array(
                        'data-toggle'=>'modal','data-target'=>'#importPrice'));
                }
                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('procurement','Goods List'),
			'model'=>$model,
				'viewhdr'=>'//warehouse/_listhdr',
				'viewdtl'=>'//warehouse/_listdtl',
				'search'=>array(
							'goods_code',
							'name',
							'unit',
							'jd_classify_name',
							'inventory',
							'display',
						),
		));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>

<?php
if (Yii::app()->user->validFunction('YN02')){
    $this->renderPartial('//site/importPrice',array('name'=>"UploadExcelForm","model"=>$model));
}
?>
<div class="goodsHistoryDiv" id="divHistory">
    <table class="table table-bordered table-striped table-hover" style="margin-bottom: 0px;">
        <thead>
        <tr><th>领货日期</th><th>领货同事</th><th>领货数量</th></tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<?php
if (Yii::app()->user->validFunction('YN02'))
    $this->renderPartial('//site/priceFlow');
?>
<?php
$js='
    $(".goodsHistoryMouse").on("mousemove",function(e){
        var num = $(this).data("id");
        var pageX = (e.pageX + 5);
        var pageY = (e.pageY + 5);
        var divData = $("#divHistory").data("id");
        if(divData!="load"&&divData!=num){
            $("#divHistory").data("id","load");
            $("#divHistory tbody").html("<tr><td colspan=\'3\'>加载中...</td></tr>");
            $.ajax({
                type: "GET",
                url: "'.Yii::app()->createUrl('warehouse/ajaxGoodHistory').'",
                data: {
                    "id":num
                },
                dataType: "json",
                success: function(data) {
                    $("#divHistory").data("id",num);
                    if(data.status == 1){
                        $("#divHistory tbody").html(data.html);
                    }else{
                        alert("數據異常");
                    }
                },
                error: function(data) { // if error occured
                    $("#divHistory").data("id","");
                    alert("Error occured.please try again");
                }
            });
        
        }
        $("#divHistory").show();
        var width = $("#divHistory").outerWidth(true);
        if(width+pageX>$(window).outerWidth(true)){
            pageX = $(window).outerWidth(true)-width-20;
        }
        $("#divHistory").css({"left":pageX+"px","top":pageY+"px"});
    });
    $(".goodsHistoryMouse").on("mouseout",function(){
        var num = $(this).data("id");
        $("#divHistory").hide();
    });
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

