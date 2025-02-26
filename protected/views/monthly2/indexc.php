<?php
$this->pageTitle=Yii::app()->name . ' - Sales Summary ID Enquiry';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Sales Summary Enquiry - ID'); ?></strong>
	</h1>
</section>

<section class="content">
	<?php 
		$search = Yii::app()->user->isSingleCity()
				? array('year_no', 'month_no', 'wfstatusdesc', )
				: array('city_name','year_no', 'month_no', 'wfstatusdesc', )
				;
		$this->widget('ext.layout.ListPageWidget', array(
			'title'=>Yii::t('monthly','Sales Summary List'),
			'model'=>$model,
				'viewhdr'=>'//monthly2/_listhdrc',
				'viewdtl'=>'//monthly2/_listdtlc',
				'search'=>$search,
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

