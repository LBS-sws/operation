<?php
$this->pageTitle=Yii::app()->name . ' - Goods Summary Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'order-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Goods Summary Form')." - ".Yii::t('procurement','Fast'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('goodsfa/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('goodsfa/save')));
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit'): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('goodsfa/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'goods_code',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'name',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'classify_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'classify_id',ClassifyForm::getClassifyList("Fast"),
                        array('disabled'=>($model->scenario =='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'origin',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'origin',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'type',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'type',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'unit',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'unit',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'price',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'price',
                        array('size'=>40,'maxlength'=>250,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-4 radio-change" id="radio-change">
                    <label class="radio-inline">
                        <input type="radio" name="inlineRadioOptions" checked id="inlineRadio1" value="option1"> <?php echo Yii::t("procurement","Independent Rules"); ?>
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> <?php echo Yii::t("procurement","Hybrid Rules"); ?>
                    </label>
                </div>
            </div>
            <div id="row0">
                <div class="form-group">
                    <?php echo $form->labelEx($model,'multiple',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->numberField($model, 'multiple',
                            array('min'=>1,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'big_num',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->numberField($model, 'big_num',
                            array('min'=>1,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'small_num',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-4">
                        <?php echo $form->numberField($model, 'small_num',
                            array('min'=>1,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
            </div>
            <div class="form-group" id="row1">
                <?php echo $form->labelEx($model,'rules_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'rules_id',RulesForm::getRulesList(),
                        array('disabled'=>($model->scenario =='view'))
                    ); ?>
                </div>
            </div>

		</div>
	</div>
</section>


<?php
$rulesType = empty($model->rules_id)?0:1;
$js='
$("#radio-change input").on("change",function(){
    $(this).prop("checked",true);
    var num = $(this).parent("label").index();
    if(num == 0 || num == 1){
        $("#row"+num).show();
        $("#row"+(1-num)).hide();
        if(num == 0){
            $("#GoodsImForm_rules_id").val(0);
        }
        if($("#GoodsImForm_small_num").val() < 1){
            $("#GoodsImForm_small_num").val(1);
        }
        if($("#GoodsImForm_big_num").val() < 1){
            $("#GoodsImForm_big_num").val(1);
        }
        if($("#GoodsImForm_multiple").val() < 1){
            $("#GoodsImForm_multiple").val(1);
        }
    }
});
$("#radio-change input").eq('.$rulesType.').trigger("change");
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>

