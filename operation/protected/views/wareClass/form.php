
<?php
//2024年9月28日09:28:46
?>
<?php
$this->pageTitle=Yii::app()->name . ' - WareClass Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'wareClass-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
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
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('wareClass/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('wareClass/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('wareClass/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'warehouse_id'); ?>

            <div class="form-group">
                <?php
                echo TbHtml::label("物料编号",false,array('class'=>"col-sm-2 control-label",'required'=>true))
                ?>
                <div class="col-sm-4">
                    <?php
                    echo TbHtml::textField("goods_code",$model->warehouseList["goods_code"],array('readonly'=>true));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php
                echo TbHtml::label("物料名称",false,array('class'=>"col-sm-2 control-label",'required'=>true))
                ?>
                <div class="col-sm-4">
                    <?php
                    echo TbHtml::textField("goods_code",$model->warehouseList["name"],array('readonly'=>true));
                    ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'class_str',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'class_str',$model->getClassStr(),
                        array('disabled'=>($model->scenario =='view'),'empty'=>'')
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'class_report',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'class_report',$model->getClassReport(),
                        array('disabled'=>($model->scenario =='view'),'empty'=>'')
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


