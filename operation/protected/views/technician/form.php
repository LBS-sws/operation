<?php
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
		<strong><?php echo Yii::t('procurement','Technical Operations Form'); ?></strong>
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
            if($model->technician == Yii::app()->user->id){
                //訂單發送則無法修改
                echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Save'), array(
                    'submit'=>Yii::app()->createUrl('technician/save')));
            }
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
                    <table class="table table-bordered table-striped disabled" id="confirm-change">
                        <thead>
                        <tr>
                            <td><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td><?php echo Yii::t("procurement","Type")?></td>
                            <td><?php echo Yii::t("procurement","Unit")?></td>
                            <td><?php echo Yii::t("procurement","Price（RMB）")?></td>
                            <td><?php echo Yii::t("procurement","Goods Number")?></td>
                            <td width="12%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <td><?php echo Yii::t("procurement","Total（RMB）")?></td>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($model->goods_list as $key => $val) {?>
                            <?php
                            $con_num = $key;
                            if(!empty($val['id'])){
                                $con_num = $val['id'];
                            }
                            ?>
                            <tr>
                                <td>
                                <?php
                                    echo '<input type="hidden" name="TechnicianForm[goods_list]['.$con_num.'][name]" value="'.$val["name"].'">';
                                    echo '<input type="hidden" name="TechnicianForm[goods_list]['.$con_num.'][type]" value="'.$val["type"].'">';
                                    echo '<input type="hidden" name="TechnicianForm[goods_list]['.$con_num.'][unit]" value="'.$val["unit"].'">';
                                    echo '<input type="hidden" name="TechnicianForm[goods_list]['.$con_num.'][price]" value="'.$val["price"].'">';
                                    echo '<input type="hidden" name="TechnicianForm[goods_list]['.$con_num.'][goods_num]" value="'.$val["goods_num"].'">';
                                    echo $val['name'];
                                ?>
                                </td>
                                <td><?php echo $val['type']?></td>
                                <td><?php echo $val['unit']?></td>
                                <td class="price"><?php echo $val['price']?></td>
                                <td class="num"><?php echo $val['goods_num']?></td>
                                <td>
                                    <?php
                                        echo '<input type="hidden" name="TechnicianForm[goods_list]['.$con_num.'][id]" value="'.$val["id"].'">';
                                        if(!empty($val['id']) && $model->technician == Yii::app()->user->name && $model->status == "sent"){
                                            echo '<input type="number" min="0" class="form-control confirm_num" name="TechnicianForm[goods_list]['.$con_num.'][confirm_num]" value="'.$val["confirm_num"].'">';
                                        }else{
                                            echo '<input type="number" min="0" class="form-control confirm_num" name="TechnicianForm[goods_list]['.$con_num.'][confirm_num]" readonly value="'.$val["confirm_num"].'">';
                                        }
                                    ?>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        <?php }?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="6"></td>
                            <td class="text-success fa-2x">0</td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'technician',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-3">
                    <?php echo $form->textField($model, 'technician',
                        array('size'=>40,'maxlength'=>250,'readonly'=>true)
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
                    <div class="col-sm-3">
                        <?php echo $form->textField($model, 'order_user',
                            array('size'=>40,'maxlength'=>250,'readonly'=>true)
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
$js = '
$(".confirm_num").on("input",confirmTotalPrice);
confirmTotalPrice();
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


