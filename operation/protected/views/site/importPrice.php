
<?php
 echo '<form action="" target="_blank" method="post" enctype="multipart/form-data" class="form-horizontal" name="'.$name.'">';
?>

<?php
	$ftrbtn = array();
    $ftrbtn[] = TbHtml::button("导入价格", array('id'=>"importUpPrice",'data-dismiss'=>'modal','submit'=>Yii::app()->createUrl('warehouse/importPrice'),'color'=>TbHtml::BUTTON_COLOR_PRIMARY));
    $ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>"btnWFClose",'data-dismiss'=>'modal'));
    $ftrbtn[] = TbHtml::link("下载导入模板",Yii::app()->createUrl('warehouse/downPriceExcel'), array("target"=>"_blank",'class'=>'btn btn-primary pull-left'));
	$this->beginWidget('bootstrap.widgets.TbModal', array(
					'id'=>'importPrice',
					'header'=>Yii::t('procurement','Import Price'),
					'footer'=>$ftrbtn,
					'show'=>false,
				));
?>

<div class="form-group">
    <label class="col-sm-2 control-label"><?php echo Yii::t("procurement","file");?></label>
    <div class="col-sm-6">
        <?php echo TbHtml::fileField($name.'[file]',"",array("class"=>"form-control")); ?>
    </div>
    <div class="col-sm-12">
        <p class="form-control-static text-danger" style="margin-bottom: 0px;">
            <span>1、请先下载导入模板，然后直接修改物品单价。</span><br/>
            <span>2、文档内的“年”“月”为生效日期的年月</span><br/>
            <span>3、如果不想修改某个物料的单价，可以把导入文档的物料删除，只保留需要修改的物料</span><br/>
        </p>
    </div>
</div>

<?php
	$this->endWidget(); 
?>
<?php
echo '</form>';
?>
<script>
    $(function () {
        $("#importUpPrice").on("click",function () {
            setTimeout(function () {
                $("#importUpPrice").parents("form").eq(0).get(0).reset();
                $("#importUpPrice").css('pointer-events','auto');;
            },1000);
        })
    })
</script>
