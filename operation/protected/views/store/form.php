<!-- -->
<?php
$this->pageTitle=Yii::app()->name . ' - Store Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'Store-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Store Form'); ?></strong>
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
			if ($model->scenario!='new' && $model->scenario!='view') {
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add Another'), array(
					'submit'=>Yii::app()->createUrl('store/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('store/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('store/save')));
			?>
<?php endif ?>
<?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
	<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
			'name'=>'btnDelete','id'=>'btnDelete','data-toggle'=>'modal','data-target'=>'#removedialog',)
		);
	?>
<?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
            <?php echo $form->hiddenField($model, 'city'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'name',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->textField($model, 'name', 
					array('size'=>50,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'jd_store_no',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->textField($model, 'jd_store_no',
					array('size'=>50,'maxlength'=>100,'readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'store_type',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php
                $list = array("1"=>Yii::t('procurement',"store default"),"2"=>Yii::t('procurement',"store normal"));
                echo $form->inlineRadioButtonList($model, 'store_type',$list,
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'z_display',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php
                $list = array("1"=>Yii::t('procurement',"show"),"0"=>Yii::t('procurement',"none"));
                echo $form->inlineRadioButtonList($model, 'z_display',$list,
					array('readonly'=>($model->scenario=='view'))
				); ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
$js = Script::genDeleteData(Yii::app()->createUrl('store/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


