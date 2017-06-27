<?php
$this->pageTitle=Yii::app()->name . ' - Order Activity Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'activity-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Order Activity Form'); ?></strong>
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
        echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('procurement','Add Activity'), array(
            'submit'=>Yii::app()->createUrl('activity/new'),
        ));
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('activity/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('activity/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('activity/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'activity_code',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'activity_title',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'activity_title',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'start_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'start_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'end_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'end_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label required">
                    <?php echo Yii::t("procurement","Order Access")?>
                    <span class="required">*</span>
                </label>
                <div class="col-sm-8">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <td width="15%"><?php echo Yii::t("procurement","Order Class");?></td>
                            <td width="30%"><?php echo Yii::t("procurement","Start Time");?></td>
                            <td width="30%"><?php echo Yii::t("procurement","End Time");?></td>
                            <td width="20%"><?php echo Yii::t("procurement","Access Number");?></td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><?php echo Yii::t("procurement","Import");?></td>
                            <td>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo $form->textField($model, 'import_start_time',
                                        array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo $form->textField($model, 'import_end_time',
                                        array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->numberField($model, 'import_num',
                                    array('min'=>0,'readonly'=>($model->scenario=='view'))
                                ); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo Yii::t("procurement","Domestic");?></td>
                            <td>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo $form->textField($model, 'domestic_start_time',
                                        array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                                    ?>
                                </div>
                            </td>
                            <td>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <?php echo $form->textField($model, 'domestic_end_time',
                                        array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),));
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php echo $form->numberField($model, 'domestic_num',
                                    array('min'=>0,'readonly'=>($model->scenario=='view'))
                                ); ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
		</div>
	</div>
</section>


<?php

if ($model->scenario!='view') {
    $js = Script::genDatePicker(array(
        'ActivityForm_start_time',
        'ActivityForm_end_time',
        'ActivityForm_import_start_time',
        'ActivityForm_import_end_time',
        'ActivityForm_domestic_start_time',
        'ActivityForm_domestic_end_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


