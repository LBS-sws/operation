<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('technician/index'));
}

$this->pageTitle=Yii::app()->name . ' - Technician Summary Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'technician-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Order Form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('technician/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->status == "pending" || $model->status == "cancelled"||$model->scenario=='new'){
                //存為草稿
                echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save Draft'), array(
                    'submit'=>Yii::app()->createUrl('technician/save')));
            }

            if($model->status == "pending" && $model->scenario=='edit'){
                //提交審核
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('procurement','For audit'), array(
                    'submit'=>Yii::app()->createUrl('technician/audit')));
            }

            if($model->scenario=='edit' && ($model->status == "pending" || $model->status == "cancelled")){
                //刪除
                echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'submit'=>Yii::app()->createUrl('technician/delete')));
            }
            if($model->scenario=='edit' && $model->status == "approve"){
                //完成
                echo TbHtml::button('<span class="fa fa-cube"></span> '.Yii::t('procurement','Finish'), array(
                    'submit'=>Yii::app()->createUrl('technician/finish')));
            }
			?>
<?php endif ?>
	</div>

            <div class="btn-group pull-right" role="group">
                <?php if ($model->scenario!='new'){
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
                <?php echo $form->labelEx($model,'goods_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-9">
                    <table class="table table-bordered table-striped disabled" id="table-change">
                        <thead>
                        <tr>
                            <td width="12%"><?php echo Yii::t("procurement","Goods Code")?></td>
                            <td><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="11%"><?php echo Yii::t("procurement","Type")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Unit")?></td>
                            <td width="10%"><?php echo Yii::t("procurement","Goods Number")?></td>
                            <?php if ($model->scenario=='edit' && $model->status == "approve"): ?>
                            <td width="10%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <?php endif ?>
                            <td width="8%">&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                            foreach ($model->goods_list as $key => $val){
                                $con_num = empty($val['id'])?$key:$val['id'];
                                $tableTr = "<tr datanum='$con_num'>";

                                $tableTr.="<td><input type='text' autocomplete='off' class='form-control testInput' name='TechnicianForm[goods_list][$con_num][goods_code]' value='".$val['goods_code']."'>";
                                $tableTr.="<input type='hidden' name='TechnicianForm[goods_list][$con_num][goods_id]' value='".$val['goods_id']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control name' readonly name='TechnicianForm[goods_list][$con_num][name]' value='".$val['name']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control type' readonly name='TechnicianForm[goods_list][$con_num][type]' value='".$val['type']."'></td>";
                                $tableTr.="<td><input type='text' class='form-control unit' readonly name='TechnicianForm[goods_list][$con_num][unit]' value='".$val['unit']."'></td>";
                                $tableTr.="<td><input type='number' class='form-control numChange goods_num' name='TechnicianForm[goods_list][$con_num][goods_num]' value='".$val['goods_num']."'></td>";
                                if($model->scenario=='edit' && $model->status == "approve"){
                                    $tableTr.="<td><input type='number' class='form-control confirm_num' name='TechnicianForm[goods_list][$con_num][confirm_num]' value='".$val['confirm_num']."'></td>";
                                }
                                $tableTr.="<td><button type='button' class='btn btn-danger delGoods'>".Yii::t("misc","Delete")."</button>";
                                if(!empty($val['id'])){
                                    $tableTr.="<input type='hidden' name='TechnicianForm[goods_list][$con_num][id]' value='".$val['id']."'>";
                                }
                                $tableTr.="</td>";

                                $tableTr.="</tr>";
                                echo $tableTr;
                            }
                        ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <?php
                            if ($model->scenario=='edit' && $model->status == "approve"){
                                echo '<td colspan="6"></td>';
                            }else{
                                echo '<td colspan="5"></td>';
                            }
                            ?>
                            <td class="text-center"><button type="button" class="btn btn-primary" id="addGoods"><?php echo Yii::t("misc","Add")?></button></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!--備註-->
            <?php if ($model->scenario =='new' || $model->status=='approve' || $model->status=='pending'): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <?php endif ?>
		</div>
	</div>
</section>
<?php
if ($model->scenario!='new')
    $this->renderPartial('//site/flowlist',array('model'=>$model));
?>
<?php
$goodList = json_encode(OrderForm::getGoodsList());
$tableBool = 1;//表格內的輸入框能否輸入
if($model->status == "pending" || $model->status == "cancelled"||$model->scenario=='new'){
    $tableBool = 0;
}
$js = '
inputDownList('.$goodList.',tableGoodsChange,false);
$("#addGoods").on("click",{btnStr:"'.Yii::t("misc","Delete").'"},addGoodsTableTwo);
$("body").delegate(".delGoods","click","'.Yii::t("procurement","Are you sure you want to delete this data?").'",delGoodsTable);
disabledTable('.$tableBool.');

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


