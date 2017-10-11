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
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('procurement','Goods List'),
			'model'=>$model,
				'viewhdr'=>'//warehouse/_listhdr',
				'viewdtl'=>'//warehouse/_listdtl',
				'search'=>array(
							'name',
							'unit',
							'inventory',
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
$this->renderPartial('//site/goodsHistory',array(
        'model'=>$model)
);
?>
<?php
$js='
    $(".goodsHistoryMouse").on("mousemove",function(e){
        var num = $(this).data("id");
        console.log(num);
        $(".divHistory"+num).show().css({"left":e.pageX+"px","top":e.pageY+"px"});
    });
    $(".goodsHistoryMouse").on("mouseout",function(){
        var num = $(this).data("id");
        console.log(num);
        $(".divHistory"+num).hide();
    });
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

