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
            if($model->technician == Yii::app()->user->name || $model->status == "pending" || $model->status == "cancelled"||$model->scenario=='new'){
                //訂單發送且技術人員不是當前登錄人則無法保存
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

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_id',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php
                    if($model->status != "pending" && $model->status != "cancelled" && $model->scenario!='new'){
                        //當訂單狀態不是等待或取消則不能修改
                        echo "<input type='hidden' name='OrderForm[goods_id]' value='".$model->goods_id."' id='OrderForm_goods_id'>";
                        echo "<input type='text' class='form-control' readonly value='".$model->getGoodNameToId($model->goods_id)["name"]."'>";
                    }else{
                        echo $form->dropDownList($model, 'goods_id',$model->getGoodsListArr(),
                            array('disabled'=>($model->scenario=='view'))
                        );
                    }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label class = "col-sm-2 control-label"><?php echo Yii::t("procurement","Type")?></label>
                <div class="col-sm-3">
                    <input type="text" readonly disabled id="goodsType" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label"><?php echo Yii::t("procurement","Unit")?></label>
                <div class="col-sm-3">
                    <input type="text" readonly disabled id="goodsUnit" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label"><?php echo Yii::t("procurement","Price（RMB）")?></label>
                <div class="col-sm-3">
                    <input type="text" readonly disabled id="goodsPrice" value="" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'order_num',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->numberField($model, 'order_num',
                        array('size'=>4,'min'=>0,'readonly'=>((($model->status != "pending" && $model->status != "cancelled")||$model->scenario=='view')&&$model->scenario!='new'))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <label class = "col-sm-2 control-label"><?php echo Yii::t("procurement","Total Price（RMB）")?></label>
                <div class="col-sm-3">
                    <input type="text" readonly disabled id="goodsTotalPrice" value="" class="form-control">
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

            <!--當被指定為技術員時-->
            <?php if ($model->technician == Yii::app()->user->name && $model->status !="pending" && $model->status !="cancelled"): ?>
                <legend>&nbsp;</legend>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'order_user',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-7">
                        <?php echo $form->textField($model, 'order_user',
                            array('size'=>40,'maxlength'=>250,'readonly'=>true)
                        ); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'confirm_num',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-3">
                        <?php echo $form->numberField($model, 'confirm_num',
                            array('size'=>4,'min'=>0,'readonly'=>($model->scenario=='view'))
                        ); ?>
                    </div>
                </div>
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
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(!empty($model->statusList)){
                                foreach ($model->statusList as $statusOne){
                                    echo "<tr><td>".Yii::t("procurement",$statusOne["status"])."</td><td>".Yii::t("procurement",$statusOne["lcu"])."</td><td>".Yii::t("procurement",$statusOne["time"])."</td></tr>";
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
$js = '
    $("#OrderForm_goods_id").on("change",function(){
        var goodArr = '.$goodList.';
        var option = $(this).val();
        var goodOne = "";
        for(var i=0;i<goodArr.length;i++){
            if(goodArr[i]["id"] == option){
                goodOne = goodArr[i];
                break;
            }
        }
        if(goodOne != ""){
            $("#goodsType").val(goodOne["type"]);
            $("#goodsUnit").val(goodOne["unit"]);
            $("#goodsPrice").val(goodOne["price"]);
        }else{
            $("#goodsType").val("");
            $("#goodsUnit").val("");
            $("#goodsPrice").val("");
        }
        priceChange();
    }).trigger("change");
    
    $("#OrderForm_confirm_num,#OrderForm_order_num").on("input",priceChange);
    
    function priceChange(){
        var price = 0;
        var num = 0;
        if($("#OrderForm_order_num").val() != "" && !isNaN($("#OrderForm_order_num").val())){
            num = parseFloat($("#OrderForm_order_num").val());
        }
        if($("#OrderForm_confirm_num").val() != "" && !isNaN($("#OrderForm_confirm_num").val())){
            num = parseFloat($("#OrderForm_confirm_num").val());
        }
        if($("#goodsPrice").val() != ""){
            price = parseFloat($("#goodsPrice").val());
        }
        if(!isNaN(price) && !isNaN(num)){
            $("#goodsTotalPrice").val((price*100000)*num/100000);
        }
    }
';
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


