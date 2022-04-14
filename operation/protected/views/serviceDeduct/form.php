<?php
$this->pageTitle=Yii::app()->name . ' - ServiceDeduct Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'ServiceDeduct-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    select[readonly]{ pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('rank','ServiceDeduct Form'); ?></strong>
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
					'submit'=>Yii::app()->createUrl('serviceDeduct/new')));
			}
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('serviceDeduct/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('serviceDeduct/save')));
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
                <?php echo $form->labelEx($model,'deduct_date',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'deduct_date',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'deduct_type',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->dropDownList($model, 'deduct_type',ServiceDeductList::getDeductType(),
                        array('id'=>'deduct_type','readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <?php echo TbHtml::label(Yii::t("rank","deduct score"),"deduct_type",array('class'=>"col-lg-2 control-label"));?>
                <div class="col-lg-2">
                    <?php
                    echo TbHtml::textField("deduct_num","",array('id'=>'deduct_num','readonly'=>(true)))
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-5">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>'4','readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>
<?php $this->renderPartial('//site/removedialog'); ?>
<?php

$js = "
$('#deduct_type').change(function(){
    var deduct_type = $(this).val()*1;
    switch(deduct_type){
        case 1:
            $('#deduct_num').val('-1000/封');
            break;
        case 2:
            $('#deduct_num').val('-500/次');
            break;
        case 3:
            $('#deduct_num').val('-300/封');
            break;
        default:
            $('#deduct_num').val('');
    }
});
$('#deduct_type').trigger('change');
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'ServiceDeductForm_deduct_date'
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genDeleteData(Yii::app()->createUrl('serviceDeduct/delete'));
Yii::app()->clientScript->registerScript('deleteRecord',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


