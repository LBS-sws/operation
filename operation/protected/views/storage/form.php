<?php
if (empty($model->id)&&$model->scenario == "edit"){
    $this->redirect(Yii::app()->createUrl('storage/index'));
}
$this->pageTitle=Yii::app()->name . ' - storage Info';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'storage-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    #table_storage td{vertical-align: middle;}
</style>
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Warehouse storage form'); ?></strong>
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
				'submit'=>Yii::app()->createUrl('storage/index')));
		?>
<?php if ($model->scenario!='view'&&$model->status_type!=1): ?>
            <?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('procurement','Draft'), array(
                'submit'=>Yii::app()->createUrl('storage/draft')));
            ?>
			<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('procurement','storage'), array(
				'submit'=>Yii::app()->createUrl('storage/storage')));
			?>
<?php endif ?>
	</div>
            <?php if ($model->scenario=='edit'&&$model->status_type!=1): ?>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Delete'), array(
                    'submit'=>Yii::app()->createUrl('storage/delete')));
                ?>
            </div>
            <?php endif ?>
            <?php if ($model->scenario=='edit'&&$model->status_type==1&&Yii::app()->user->validFunction('YN03')): ?>
            <div class="btn-group pull-right" role="group">
                <?php echo TbHtml::button('<span class="fa fa-backward"></span> '.Yii::t('procurement','backward'), array(
                    'submit'=>Yii::app()->createUrl('storage/backward')));
                ?>
            </div>
            <?php endif ?>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'storage_code'); ?>
			<?php echo $form->hiddenField($model, 'storage_name'); ?>

            <?php if ($model->scenario=='edit'): ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'code',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <?php echo $form->textField($model, 'code',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php endif ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'apply_time',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-2">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <?php echo $form->textField($model, 'apply_time',
                            array('class'=>'form-control pull-right','readonly'=>($model->getReadonly()),));
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'goods_list',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-9">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th width="10%"><?php echo Yii::t("procurement","Goods Code");?></th>
                            <th width="30%"><?php echo Yii::t("procurement","Goods Name");?></th>
                            <th width="10%"><?php echo Yii::t("procurement","Unit");?></th>
                            <th width="30%"><?php echo Yii::t("procurement","supplier");?></th>
                            <th width="10%"><?php echo Yii::t("procurement","Now Inventory");?></th>
                            <th width="14%"><?php echo Yii::t("procurement","storage num");?></th>

                                <?php
                                if(!$model->getReadonly()){
                                    echo '<th class="text-center" width="5%">'.TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('procurement','Select Goods'),
                                        array('data-toggle'=>'modal','data-target'=>'#lookupdialog','id'=>'storageSelect')
                                    )."</th>";
                                }
                                ?>
                        </tr>
                        <tbody id="table_storage">
                        <?php
                        echo $model->printTableStorage();
                        ?>
                        </tbody>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'remark',array('class'=>"col-sm-2 control-label")); ?>
                <div class="col-sm-4">
                    <?php echo $form->textArea($model, 'remark',
                        array('rows'=>4,'cols'=>50,'maxlength'=>1000,'readonly'=>($model->getReadonly()))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>


<?php $this->renderPartial('//site/lookup'); ?>
<?php $this->renderPartial('//site/supplier'); ?>
<?php
if (!$model->getReadonly()) {
    $mesg = Yii::t('dialog','No Record Found');
    $link = Yii::app()->createAbsoluteUrl("lookup")."/StorageSearchex";
    $js='
    $("#lookupdialog .modal-title").text($("#storageSelect").text());
    $("#lstlookup").attr("multiple","multiple");
    $("#btnLookup").on("click",function(){
        $.ajax({
            type: "GET",
            url: "'.$link.'",
            data: {search:$("#txtlookup").val()},
            dataType: "json",
            success: function(data) {
                $("#lstlookup").empty();
                var selectList = $("#StorageForm_storage_code").val();
                var option = "";
                selectList =  selectList==""?[]:selectList.split("~");
                $.each(data, function(index, element) {
                    option = "<option value=\'"+element.id+"\' ";
                    option+=" data-code=\'"+element.goods_code+"\' ";
                    option+=" data-name=\'"+element.name+"\' ";
                    option+=" data-unit=\'"+element.unit+"\' ";
                    option+=" data-inventory=\'"+element.inventory+"\' ";
                    if(selectList.indexOf(element.id)>-1){
                        option+=" selected ";
                    }
                    option+=">"+element.goods_code+" -- "+element.name+"</option>";
                    $("#lstlookup").append(option);
                });
                
                var count = $("#lstlookup").children().length;
                if (count<=0) $("#lstlookup").append("<option value=\'-1\'>'.$mesg.'</option>");
            },
            error: function(data) { // if error occured
                alert("Error occured.please try again");
            }
        });
    });
    
    $("#btnLookupSelect").on("click",function(){
        $("#lookupdialog").modal("hide");
        var oldcodeval = $("#StorageForm_storage_code").val();
        oldcodeval =  oldcodeval==""?[]:oldcodeval.split("~");
        $("#lstlookup option:selected").each(function(i, selected) { //添加新增的物品
            if(oldcodeval.indexOf(""+$(selected).val())<0){
                oldcodeval.push(""+$(selected).val());
                setStorageRow(selected);
            }
        });
        $("#StorageForm_storage_code").val(oldcodeval.join("~"));
        if($("#table_storage").children("tr").length>2){
            $("#table_storage").children("tr.none").hide();
        }
    });
    
    function setStorageRow(row){
        var html =$("#table_template").html();
        html=html.replace(/:model/g,"StorageForm[goods_list]");
        html=html.replace(/:id/g,$(row).val());
        html=html.replace(/:goods_code/g,$(row).data("code"));
        html=html.replace(/:name/g,$(row).data("name"));
        html=html.replace(/:unit/g,$(row).data("unit"));
        html=html.replace(/:inventory/g,$(row).data("inventory"));
        $("#table_storage").append("<tr data-num=\'"+$(row).val()+"\'>"+html+"</tr>");
    }
    
    $("#table_storage").delegate(".storageDelete","click",function(){
        var selectList = $("#StorageForm_storage_code").val();
        var $thatTr = $(this).parents("tr:first");
        selectList =  selectList==""?[]:selectList.split("~");
        var index = selectList.indexOf(""+$thatTr.data("num"));
        if(index>-1){
            selectList.splice(index, 1);
            $("#StorageForm_storage_code").val(selectList.join("~"));
            $thatTr.remove();
        }
        if($("#table_storage").children("tr").length<3){
            $("#table_storage").children("tr.none").show();
        }
    });
';
    Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
    $js = Script::genDatePicker(array(
        'StorageForm_apply_time',
    ));
    Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
}
$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


