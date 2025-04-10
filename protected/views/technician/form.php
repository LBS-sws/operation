
<?php
//2024年9月28日09:28:46
?>
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

<style>
    *.readonly{ pointer-events: none;}
    .input-text-span{display: block;width: 100%;padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        border: 1px solid #d2d6de;
        background-color: #eee;
        min-height: 34px;
        min-width: 75px;
    }
    #table-change td{position: relative;}
    .select2-container .select2-selection--single{ height: 34px;}
    .select2.select2-container{ width: 100% !important;}
    .select_num,.select_remark{ min-width: 100px;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','requisition form'); ?></strong>
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
            if($model->status == "pending" || $model->status == "reject"||$model->scenario=='new'){
                //存為草稿
                echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save Draft'), array(
                    'submit'=>Yii::app()->createUrl('technician/save')));
            }

            if($model->status == "pending" || $model->status == "reject"){
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
                    //下載
                    echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('procurement','Down'), array(
                        'submit'=>Yii::app()->createUrl('delivery/downorder',array("index"=>$model->id))));
                } ?>
            </div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'status'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
            <?php if (key_exists("jd_order_code",$model->jd_set)): ?>
                <?php echo TbHtml::hiddenField("jd_order_code", $model->jd_set["jd_order_code"]); ?>
            <?php endif ?>

            <div class="form-group">
                <?php echo Tbhtml::label(Yii::t("procurement","apply type"),'jd_order_type',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
                <div class="col-sm-3">
                    <?php
                    echo $form->dropDownList($model,"jd_set[jd_order_type]",TechnicianList::getApplyTypeList(),
                        array('readonly'=>$model->getInputBool(),"id"=>"jd_order_type")
                    );
                    ?>
                </div>
            </div>

            <div class="form-group" id="jd_company_div" <?php if($model->jd_set["jd_order_type"]!=1){ echo "style='display:none;'";} ?>>
                <?php echo Tbhtml::label(Yii::t("procurement","jd company code"),'jd_company_code',array('class'=>"col-sm-2 control-label",'required'=>true)); ?>
                <div class="col-sm-6">
                    <?php
                    echo $form->dropDownList($model,"jd_set[jd_company_code]",TechnicianList::getCompanyList($model->city,$model->jd_set["jd_company_code"]),
                        array('readonly'=>$model->getInputBool(),"id"=>"jd_company_code","empty"=>"")
                    );
                    ?>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-10 col-sm-offset-2">
                    <div class="text-danger form-control-static"><span class="required">*</span>
                        <?php echo Yii::t("procurement","Mobile phone users please horizontal screen operation");?>
                    </div>
                </div>
            </div>

            <?php if (!empty($model->ject_remark)): ?>
                <div class="form-group has-error">
                    <?php echo $form->labelEx($model,'ject_remark',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-6">
                        <?php echo $form->textArea($model, 'ject_remark',
                            array('readonly'=>true)
                        ); ?>
                    </div>
                </div>
            <?php endif; ?>

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
                <div class="col-sm-10">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped disabled" id="table-change" data-matching="<?php echo Yii::t("procurement","matching")?>" data-matters="<?php echo Yii::t("procurement","matters")?>" data-del="<?php echo Yii::t("misc","Delete")?>">
                        <thead>
                        <tr>
                            <td width="20%"><?php echo Yii::t("procurement","Goods Name")?></td>
                            <td width="8%" ><?php echo Yii::t("procurement","Unit")?></td>
                            <td width="12%"><?php echo Yii::t("procurement","Demand Note")?></td>
                            <?php if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")): ?>
                                <td width="12%"><?php echo Yii::t("procurement","Headquarters Note")?></td>
                            <?php endif ?>
                            <td width="12%"><?php echo Yii::t("procurement","Goods Number")?></td>
                            <?php if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")): ?>
                                <td width="12%"><?php echo Yii::t("procurement","Actual Number")?></td>
                            <?php endif ?>
                            <?php if (!$model->getInputBool()): ?>
                                <td width="1%">&nbsp;</td>
                            <?php endif ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                            if(!empty($model->goods_list)){
                                $spanInput = "";
                                if($model->getInputBool()){
                                    $spanInput = "spanInput";
                                }
                                foreach ($model->goods_list as $key =>$goodsList){
                                    echo "<tr data-classify='".$goodsList["classify_id"]."'>";
                                    echo "<td class='openHindDivToTd'>";
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][goods_id]",$goodsList["goods_id"],array("class"=>"select_id"));
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][name]",$goodsList["name"]);
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][unit]",$goodsList["unit"]);
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][classify_id]",$goodsList["classify_id"]);
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][matching]",isset($goodsList["matching"])?$goodsList["matching"]:"");
                                    echo TbHtml::hiddenField("TechnicianForm[goods_list][$key][matters]",isset($goodsList["matters"])?$goodsList["matters"]:"");
                                    echo $goodsList["name"];
                                    echo "</td>";
                                    echo "<td>".$goodsList["unit"]."</td>";
                                    echo "<td>";
                                    echo TbHtml::textField( "TechnicianForm[goods_list][$key][note]",$goodsList["note"],
                                        array('class'=>"select_remark $spanInput",'readonly'=>($model->getInputBool()))
                                    );
                                    echo "</td>";
                                    if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")){
                                        echo "<td>";
                                        echo TbHtml::textField( "TechnicianForm[goods_list][$key][remark]",$goodsList["remark"],
                                            array('readonly'=>(true),'class'=>'spanInput')
                                        );
                                        echo "</td>";
                                    }
                                    echo "<td>";
                                    echo TbHtml::numberField( "TechnicianForm[goods_list][$key][goods_num]",$goodsList["goods_num"],
                                        array('class'=>"select_num $spanInput",'readonly'=>($model->getInputBool()))
                                    );
                                    echo "</td>";
                                    if ($model->scenario=='edit' && ($model->status == "approve" || $model->status == "finished" || $model->status == "read")){
                                        echo "<td>";
                                        echo TbHtml::textField( "TechnicianForm[goods_list][$key][confirm_num]",$goodsList["confirm_num"],
                                            array('readonly'=>(true),'class'=>'spanInput')
                                        );
                                        echo "</td>";
                                    }
                                    if(!$model->getInputBool()){
                                        echo "<td><a class='btn btn-danger goodsDelete' data-id='".$goodsList["goods_id"]."'>刪除</a></td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                        ?>
                        </tbody>
                        <tfoot>
                        <?php if (!$model->getInputBool()): ?>
                        <tr>
                            <td colspan="4" class="">&nbsp;</td>
                            <td class="">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#selectGoods_div" id="addGoods">
                                    <?php echo Yii::t("misc","Add")?>
                                </button>
                            </td>
                         </tr>
                        <?php endif ?>
                        </tfoot>
                    </table>

                    </div>
                </div>
            </div>

            <!--備註-->
            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-7">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->getInputBool()))
                    ); ?>
                </div>
            </div>
		</div>
	</div>

</section>
<!--選擇物品彈框-->
<?php
 $goodsListToClassify = TechnicianForm::getWarehouseGoodsListToCity($model->city);
 $classifyList = array();
 echo "<ul class='hide' id='goodsListToClassify'>";
 foreach ($goodsListToClassify as $classify){
     $classifyList[$classify["id"]] = $classify["name"];
     echo "<li><ul class='list-inline'>";
     echo "<li>".$classify["id"]."</li>";
     echo "<li>".$classify["name"]."</li>";
     echo "<li>";
     foreach ($classify["list"] as $goods){
         echo "<ul class='list-inline'>";
         echo "<li data-str='goods_code'>".$goods["goods_code"]."</li>";
         echo "<li data-str='name'>".$goods["name"]."</li>";
         echo "<li data-str='unit'>".$goods["unit"]."</li>";
         echo "<li data-str='id'>".$goods["id"]."</li>";
         echo "<li data-str='matching'>".$goods["matching"]."</li>";
         echo "<li data-str='matters'>".$goods["matters"]."</li>";
         echo "</ul>";
     }
     echo "</li>";
     echo "</ul></li>";
 }
echo "</ul>";
?>
<div id="selectGoods_div" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button class="close" data-dismiss="modal" type="button">×</button>
                <h4 class="modal-title"><?php echo Yii::t("procurement","Select Goods");?></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-xs-6 col-lg-4">
                        <?php echo TbHtml::dropDownList('selectGoods_select',0,$classifyList,
                            array('disabled'=>(false))
                        ); ?>
                    </div>
                    <div class="col-xs-6 col-lg-4">
                        <input class="form-control" id="selectGoods_search" type="text" placeholder='<?php echo Yii::t("procurement","Goods Name");?>'>
                    </div>
                </div>
                <div class="box" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-bordered table-striped" id="selectGoods_table">
                        <thead>
                        <tr>
                            <th width="5%">&nbsp;</th>
                            <th width="25%"><?php echo Yii::t("procurement","Goods Code");?></th>
                            <th width="32%"><?php echo Yii::t("procurement","Goods Name");?></th>
                            <th width="25%"><?php echo Yii::t("procurement","Goods Class");?></th>
                            <th width="13%"><?php echo Yii::t("procurement","Unit");?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="checkbox"></td>
                            <td>sss</td>
                            <td>ddd</td>
                            <td>ddd</td>
                            <td>ddd</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary pull-right" type="button"><?php echo Yii::t("dialog","OK");?></button>
            </div>
        </div>
    </div>
</div>

<?php
if ($model->scenario!='new')
    $this->renderPartial('//site/flowlist',array('model'=>$model));
?>
<?php
$js = "
$('.spanInput').each(function(){
    var text = $(this).val();
    $(this).hide();
    $(this).parent('td').append('<span class=\'input-text-span\'>'+text+'</span>');
});

$('#jd_order_type').change(function(){
    if($(this).val()==1){
        $('#jd_company_div').slideDown(100);
    }else{
        $('#jd_company_code').val('');
        $('#jd_company_div').slideUp(100);
    }
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$disabled = $model->getInputBool() ? 'true' : 'false';
$js="
$('#jd_company_code').select2({
    multiple: false,
    maximumInputLength: 10,
    language: '$lang',
    disabled: $disabled
});
function formatState(state) {
	var rtn = $('<span style=\"color:black\">'+state.text+'</span>');
	return rtn;
}
";
Yii::app()->clientScript->registerScript('selectSearchFunction',$js,CClientScript::POS_READY);
?>

<?php
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/goodsChange.js?4", CClientScript::POS_END);
Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/goodsChange.css");
?>

<?php $this->endWidget(); ?>


