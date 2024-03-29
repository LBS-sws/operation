<?php
$this->pageTitle=Yii::app()->name . ' - Report';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'report-form',
'action'=>Yii::app()->createUrl('report/generate'),
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Sales Summary - ID'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button(Yii::t('misc','Submit'), array(
				'submit'=>Yii::app()->createUrl('report/salessummaryid'))); 
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'name'); ?>
			<?php echo $form->hiddenField($model, 'fields'); ?>
			<?php echo $form->hiddenField($model, 'hq'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'year',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php 
						$item = array();
						for ($i=2017;$i<=2027;$i++) {$item[$i] = $i; }
						echo $form->dropDownList($model, 'year', $item); 
					?>
				</div>
			</div>
		
			<div class="form-group">
				<?php echo $form->labelEx($model,'month',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php 
						$item = array();
						for ($i=1;$i<=12;$i++) {$item[$i] = $i; }
						echo $form->dropDownList($model, 'month', $item); 
					?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $this->endWidget(); ?>

</div><!-- form -->

