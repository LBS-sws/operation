<?php
$this->pageTitle=Yii::app()->name . ' - Order Summary Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'order-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Order Summary Form'); ?></strong>
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
        echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('procurement','Add Order'), array(
            'submit'=>Yii::app()->createUrl('order/new'),
        ));
		?>
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('order/index')));
		?>
<?php if ($model->scenario!='view'): ?>
			<?php
            if($model->status == "pending" || $model->status == "cancelled"||$model->scenario=='new'){
                //訂單發送則無法修改
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('order/save')));
            }
			?>
<?php endif ?>
        <?php if ($model->scenario=='edit' && ($model->status == "pending" || $model->status == "cancelled")): ?>
            <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                'submit'=>Yii::app()->createUrl('order/delete')));
            ?>
        <?php endif ?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>


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
                            <td><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td><?php echo Yii::t("procurement","Type")?></td>
                            <td><?php echo Yii::t("procurement","Unit")?></td>
                            <td><?php echo Yii::t("procurement","Price（RMB）")?></td>
                            <td><?php echo Yii::t("procurement","Goods Number")?></td>
                            <td><?php echo Yii::t("procurement","Total（RMB）")?></td>
                            <td>&nbsp;</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($model->goods_list)): ?>
                            <tr datanum="0">
                                <td>
                                    <input type="text" class="form-control testInput" name="OrderForm[goods_list][0][name]">
                                    <input type="hidden" name="OrderForm[goods_list][0][goods_id]" value="">
                                </td>
                                <td><input type="text" class="form-control" name="OrderForm[goods_list][0][type]" readonly></td>
                                <td><input type="text" class="form-control" name="OrderForm[goods_list][0][unit]" readonly></td>
                                <td><input type="text" class="form-control" name="OrderForm[goods_list][0][price]" readonly></td>
                                <td><input type="number" min="0" class="form-control numChange" name="OrderForm[goods_list][0][goods_num]"></td>
                                <td><input type="text" class="form-control" readonly></td>
                                <td>
                                    <button type="button" class="btn btn-danger delGoods"><?php echo Yii::t("misc","Delete")?></button>
                                </td>
                            </tr>
                       <?php else: ?>

                        <?php foreach ($model->goods_list as $key => $val) {?>
                                <?php
                                $con_num = $key;
                                if(!empty($val['id'])){
                                    $con_num = $val['id'];
                                }
                                ?>
                                <tr datanum="<?php echo $con_num;?>">
                                    <td>
                                        <input type="text" class="form-control testInput" name="OrderForm[goods_list][<?php echo $con_num;?>][name]" value="<?php echo $val['name']?>">
                                        <input type="hidden" name="OrderForm[goods_list][<?php echo $con_num;?>][goods_id]" value="<?php echo $val['goods_id']?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly  name="OrderForm[goods_list][<?php echo $con_num;?>][type]" value="<?php echo $val['type']?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly  name="OrderForm[goods_list][<?php echo $con_num;?>][unit]" value="<?php echo $val['unit']?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly  name="OrderForm[goods_list][<?php echo $con_num;?>][price]" value="<?php echo $val['price']?>">
                                    </td>
                                    <td>
                                        <input type="number" min="0" class="form-control numChange" name="OrderForm[goods_list][<?php echo $con_num;?>][goods_num]" value="<?php echo $val['goods_num']?>">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" readonly>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger delGoods"><?php echo Yii::t("misc","Delete")?></button>
                                        <?php if (!empty($val['id'])): ?>
                                            <input type="hidden" name="OrderForm[goods_list][<?php echo $con_num;?>][id]" value="<?php if (!empty($val['id'])){ echo $val['id'];}?>">
                                        <?php endif;?>
                                    </td>
                                </tr>
                        <?php }?>

                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="5"></td>
                            <td class="text-success fa-2x">0</td>
                            <td class="text-center"><button type="button" class="btn btn-primary" id="addGoods"><?php echo Yii::t("misc","Add")?></button></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'technician',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php
                    $bool = false;
                    if($model->scenario!='new'){
                        $bool = $model->scenario=='view'|| ($model->status != "pending" && $model->status != "cancelled");
                    }
                    if($bool){
                        echo '<input type="hidden" name="OrderForm[technician]" value="'.$model->technician.'">';
                    }
                    echo $form->dropDownList($model, 'technician',$model->getUserListArr(),
                        array('disabled'=>$bool)
                    ); ?>
                </div>
            </div>
            <!--狀態顯示-->
            <?php if ($model->scenario!='new' && $model->scenario!='view'): ?>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'status',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->dropDownList($model, 'status',$model->getNowStatusList(),
                            array('disabled'=>($model->scenario=='view'||($model->status == "sent" && $model->technician != Yii::app()->user->name)))
                        ); ?>
                    </div>
                </div>
            <?php endif ?>

            <?php if ($model->status!='sent' && $model->status!='approve' && $model->status!='reject'): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->scenario=='view'))
                    ); ?>
                </div>
            </div>
            <?php endif ?>
            <!--訂單狀態一覽-->
            <?php if ($model->scenario!='new'): ?>
                <legend>&nbsp;</legend>
                <div class="form-group">
                    <label class = "col-sm-2 control-label"><?php echo Yii::t("procurement","Order Status List")?></label>
                    <div class="col-sm-7">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo Yii::t("procurement","Order Status"); ?></th>
                                    <th><?php echo Yii::t("procurement","Operator User"); ?></th>
                                    <th><?php echo Yii::t("procurement","Operator Time"); ?></th>
                                    <th><?php echo Yii::t("procurement","Remark"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($model->statusList)){
                                foreach ($model->statusList as $statusOne){
                                    echo "<tr><td>".Yii::t("procurement",$statusOne["status"])."</td><td>".$statusOne["lcu"]."</td><td>".$statusOne["time"]."</td><td>".$statusOne["r_remark"]."</td></tr>";
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif ?>
		</div>
	</div>
</section>
<?php
$goodList = json_encode($model->getGoodsList());
$tableBool = 1;//表格內的輸入框能否輸入
if($model->status == "pending" || $model->status == "cancelled"||$model->scenario=='new'){
    $tableBool = 0;
}
$js = '
inputDownList('.$goodList.',tableGoodsChange);
$("#addGoods").on("click",{btnStr:"'.Yii::t("misc","Delete").'"},addGoodsTable);
$("body").delegate(".delGoods","click","'.Yii::t("procurement","Are you sure you want to delete this data?").'",delGoodsTable);
$("body").delegate(".numChange","input",goodsTotalPrice);
goodsTotalPrice();
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


