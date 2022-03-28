<?php
$this->pageTitle=Yii::app()->name . ' - ServiceMoney Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ServiceMoney-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    select[readonly]{ pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('rank','ServiceMoney Form'); ?></strong>
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
					'submit'=>Yii::app()->createUrl('serviceMoney/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('serviceMoney/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('serviceMoney/save')));
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

            <?php if (!empty($model->service_code)): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'service_code',array('class'=>"col-lg-2 control-label")); ?>
                    <div class="col-lg-3">
                        <?php echo $form->textField($model, 'service_code',
                            array('readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'employee_id',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-5">
				<?php echo $form->dropDownList($model, 'employee_id',ServiceMoneyForm::getEmployeeList($model->employee_id),
					array('id'=>'name','readonly'=>($model->scenario!='new'))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'service_year',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php echo $form->dropDownList($model, 'service_year',ServiceMoneyList::getYearList(),
					array('readonly'=>($model->scenario!='new'))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'service_month',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php echo $form->dropDownList($model, 'service_month',ServiceMoneyList::getMonthList(false),
					array('readonly'=>($model->scenario!='new'))
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'service_money',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php
                echo $form->numberField($model, 'service_money',
					array('readonly'=>($model->scenario=='view'),'min'=>0)
				); ?>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'score_num',array('class'=>"col-lg-2 control-label")); ?>
				<div class="col-lg-3">
				<?php
                echo $form->numberField($model, 'score_num',
					array('readonly'=>(true))
				); ?>
                </div>
                <div class="col-lg-5">
                    <p class="form-control-static">保存后刷新得分</p>
                </div>
			</div>

            <?php if (!empty($model->remark)): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>'4','readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php endif ?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/removedialog'); ?>

<?php
/*
$disabled = $model->scenario=='view'?"true":"false";
$clientData = ServiceMoneyForm::getEmployeeJson($model->employee_id);
$js = "
$('#client_id').select2({
    placeholder: '',
    data:{$clientData},
    disabled: $disabled,
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
*/
$js = Script::genDeleteData(Yii::app()->createUrl('serviceMoney/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


