<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('warehouse/index'));
}
$this->pageTitle=Yii::app()->name . ' - Warehouse Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'warehouse-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Goods Summary Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('Warehouse/index')));
		?>
<?php if ($model->scenario!='view'): ?>
            <?php if ($model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
                    'submit'=>Yii::app()->createUrl('warehouse/new')));
                ?>
                <?php echo TbHtml::button('<span class="fa fa-copy"></span> '.Yii::t('misc','Copy'), array(
                    'submit'=>Yii::app()->createUrl('warehouse/copy')));
                ?>
            <?php endif ?>
			<?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
				'submit'=>Yii::app()->createUrl('Warehouse/save')));
			?>
        <?php if ($model->scenario=='edit'): ?>
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'submit'=>Yii::app()->createUrl('Warehouse/delete')));
                ?>
        <?php endif ?>
<?php endif ?>
	</div>


            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    //库存记录
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('procurement','Inventory History'), array(
                        'name'=>'btnHistory','id'=>'btnHistory','data-toggle'=>'modal','data-target'=>'#historydialog'));
                } ?>
                <?php if ($model->scenario!='view'){
                    //導入
                    /*
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('procurement','Import File'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#importGoods'));
                    */
                } ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'classify_id'); ?>
			<?php echo $form->hiddenField($model, 'old_good_no'); ?>

            <?php if (!empty($model->old_good_no)): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'old_good_no',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'old_good_no',
                            array('min'=>0,'readonly'=>(true))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'goods_code',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
                <div class="col-sm-5">
                    <p class="form-control-static"><?php echo Yii::t('procurement','Please keep the same with T3 system number');?></p>
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
                <?php echo $form->labelEx($model,'jd_classify_no',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'jd_classify_no',
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'jd_classify_name',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textField($model, 'jd_classify_name',
                        array('readonly'=>($model->scenario=='view'))
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
            <?php if (Yii::app()->user->validFunction('YN02')&&$model->scenario!='new'): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'price',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <div class="input-group">
                        <?php echo $form->numberField($model, 'price',
                            array('min'=>0,'readonly'=>(true))
                        ); ?>

                        <span class="input-group-btn">
                            <?php
                            //歷史價格
                            echo TbHtml::button(Yii::t('procurement','price history'), array(
                                'class'=>'clickPriceBtn','data-id'=>$model->id));
                            ?>
                        </span>
                    </div><!-- /input-group -->
                </div>
            </div>
            <?php endif ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'costing',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'costing',
                        array('min'=>0,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'decimal_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->inlineRadioButtonList($model, 'decimal_num',array("否"=>Yii::t("misc","No"),"是"=>Yii::t("misc","Yes")),
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'inventory',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'inventory',
                        array('min'=>0,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'min_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->numberField($model, 'min_num',
                        array('min'=>0,'readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'matching',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-6">
                    <?php echo $form->textField($model, 'matching',
                        array('readonly'=>($model->scenario=='view'||!Yii::app()->user->validFunction('YN04')))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'matters',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-6">
                    <?php echo $form->textField($model, 'matters',
                        array('readonly'=>($model->scenario=='view'||!Yii::app()->user->validFunction('YN04')))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'display',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-lg-6">
                    <?php
                    echo $form->inlineRadioButtonList($model, 'display',array(Yii::t("misc","No"),Yii::t("misc","Yes")),
                        array('readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <legend><?php echo Yii::t("procurement","JD System Curl");?></legend>
            <?php
            $html = "";
            $className = get_class($model);
            foreach (WarehouseForm::$jd_set_list as $num=>$item){
                $field_value = key_exists($item["field_id"],$model->jd_set)?$model->jd_set[$item["field_id"]]:null;
                if($num%2==0){
                    $html.='<div class="form-group">';
                }
                $html.=TbHtml::label(Yii::t("procurement",$item["field_name"]),'',array('class'=>"col-sm-2 control-label"));
                $html.='<div class="col-lg-3">';
                $html.=TbHtml::textField("{$className}[jd_set][{$item["field_id"]}]",$field_value,array('readonly'=>($model->scenario=='view')));
                $html.="</div>";
                if($num%2==1){
                    $html.='</div>';
                }
            }
            if(count(WarehouseForm::$jd_set_list)%2==0){
                $html.='</div>';
            }
            echo $html;
            ?>
		</div>
	</div>
</section>


<?php
$js = "
    $('#changeStatus').change(function(){
        var type = $(this).val();
        if(type==''||type==undefined){
            $('#tblFlow>tbody>tr').removeClass('hidden');
        }else{
            $('#tblFlow>tbody>tr').each(function(){
                if(type==$(this).data('type')){
                    $(this).removeClass('hidden');
                }else{
                    $(this).addClass('hidden');
                }
            });
        }
    });
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>
<?php
if ($model->scenario!='view')
    $this->renderPartial('//site/importGoods',array(
        'model'=>$model,
        'prevUrl'=>'Warehouse/new',
        'name'=>"UploadExcelForm")
    );
if (Yii::app()->user->validFunction('YN02'))
    $this->renderPartial('//site/priceFlow');
if($model->scenario!='new'){
    $this->renderPartial('//site/history',array("tableHtml"=>WarehouseForm::getHistoryList($model->id)));
}
?>


