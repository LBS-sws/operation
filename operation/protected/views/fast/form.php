<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('fast/index'));
}
$this->pageTitle=Yii::app()->name . ' - Fast Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'fast-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Fast Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('fast/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->status == "sent" || $model->status == "read"){
                //存為草稿
                echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('fast/save')));

                //发货
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('procurement','Shipments'), array(
                    'submit'=>Yii::app()->createUrl('fast/audit')));
            }
			?>
<?php endif ?>
	</div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
                    if($model->status == "sent" || $model->status == "read") {
                        //拒絕
                        echo TbHtml::button('<span class="fa fa-mail-reply-all"></span> ' . Yii::t('procurement', 'Reject'), array(
                            'submit' => Yii::app()->createUrl('fast/reject')));
                    }
                    //流程
                    echo TbHtml::button('<span class="fa fa-file-text-o"></span> '.Yii::t('misc','Flow'), array(
                        'name'=>'btnFlow','id'=>'btnFlow','data-toggle'=>'modal','data-target'=>'#flowinfodialog'));
                } ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
            <?php echo $form->hiddenField($model, 'activity_id'); ?>
            <?php echo $form->hiddenField($model, 'order_class'); ?>


            <?php if ($model->scenario!='new'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'order_code',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'order_code',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'order_class',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->dropDownList($model, 'order_class',OrderGoods::getArrGoodsClass(),
                        array('disabled'=>true)
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-10">
                    <table class="table table-bordered table-striped disabled" id="table-change">
                        <thead>
                        <tr>
                            <td width="12%"><?php echo Yii::t("procurement","Goods Code")?></td>
                            <td><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="11%"><?php echo Yii::t("procurement","Type")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Unit")?></td>
                            <td width="12%"><?php echo Yii::t("procurement","Price（RMB）")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Goods Number")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <td width="12%"><?php echo Yii::t("procurement","Total（RMB）")?></td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                            foreach ($model->goods_list as $key => $val){
                                $con_num = empty($val['id'])?$key:$val['id'];
                                $tableTr = "<tr datanum='$con_num'>";

                                $tableTr.="<td><input type='text' class='form-control testInput' readonly name='FastForm[goods_list][$con_num][goods_code]' value='".$val['goods_code']."'>";
                                $tableTr.="<input type='hidden' name='FastForm[goods_list][$con_num][goods_id]' value='".$val['goods_id']."'>";
                                $tableTr.="<input type='hidden' name='FastForm[goods_list][$con_num][id]' value='".$val['id']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control name' readonly name='FastForm[goods_list][$con_num][name]' value='".$val['name']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control type' readonly name='FastForm[goods_list][$con_num][type]' value='".$val['type']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control unit' readonly name='FastForm[goods_list][$con_num][unit]' value='".$val['unit']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control price' readonly name='FastForm[goods_list][$con_num][price]' value='".sprintf("%.2f", $val['price'])."'></td>";
                                $tableTr.="<td><input type='number' class='form-control' readonly name='FastForm[goods_list][$con_num][goods_num]' value='".$val['goods_num']."'></td>";
                                if($model->status == "sent" || $model->status == "read"){
                                    $tableTr.="<td><input type='number' class='form-control numChange goods_num' name='FastForm[goods_list][$con_num][confirm_num]' value='".$val['confirm_num']."'></td>";
                                }else{
                                    $tableTr.="<td><input type='number' class='form-control numChange goods_num' readonly name='FastForm[goods_list][$con_num][confirm_num]' value='".$val['confirm_num']."'></td>";
                                }
                                $tableTr.="<td><input type='text' class='form-control sum' readonly></td>";

                                $tableTr.="</tr>";
                                echo $tableTr;
                            }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7"></td>
                            <td class="text-success fa-2x">0</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!--備註-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>
<?php
    $this->renderPartial('//site/flowlist',array('model'=>$model));
?>
<?php
$js = '
$("body").delegate(".numChange","input",goodsTotalPrice);
goodsTotalPrice();
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/goodsChange.js", CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/goodsChange.css");
?>

<?php $this->endWidget(); ?>


