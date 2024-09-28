
<?php
//2024年9月28日09:28:46
?>
<?php
$this->pageTitle=Yii::app()->name . ' - Store';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'store-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>
<!-- -->
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Store Info'); ?></strong>
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
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			if (Yii::app()->user->validRWFunction('YD10'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
					'id'=>"openCitySelectDialog",
				)); 
		?>
	</div>
	</div></div>
	<?php $this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('procurement','Store List'),
			'model'=>$model,
				'viewhdr'=>'//store/_listhdr',
				'viewdtl'=>'//store/_listdtl',
				'gridsize'=>'24',
				'height'=>'600',
				'search'=>array(
							'name',
							'jd_store_no',
							'city_name',
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
<?php $this->renderPartial('//site/citySelect',array("submitUrl"=>Yii::app()->createUrl('store/new'))); ?>

<?php
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
