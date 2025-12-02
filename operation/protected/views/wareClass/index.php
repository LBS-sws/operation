
<?php
//2024年9月28日09:28:46
?>
<?php
$this->pageTitle=Yii::app()->name . ' - WareClass Info';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'wareClass-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Warehouse Class'); ?></strong>
	</h1>
<!--
	<ol class="breadcrumb">
		<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
		<li><a href="#">Layout</a></li>
		<li class="active">Top Navigation</li>
	</ol>
-->
</section>

<section class="content">
    <div class="box">
        <div class="box-body">
            <div class="btn-group pull-right" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-cloud-download"></span>导出未设置的物料', array(
                    'submit'=>Yii::app()->createUrl('wareClass/DownExcel'),
                ));
                ?>
            </div>
        </div>
    </div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('app','Warehouse Class'),
			'model'=>$model,
				'viewhdr'=>'//wareClass/_listhdr',
				'viewdtl'=>'//wareClass/_listdtl',
				'search'=>array(
							'goods_code',
							'name',
							'class_str',
							'class_report',
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
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

