<?php
$ftrbtn = array();
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnWFClose','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'storageFlow',
    'header'=>Yii::t('procurement','storage history'),
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>

<div class="box" id="flow-storage-list" style="max-height: 300px; overflow-y: auto;">
    <table id="tblStorageFlow" class="table table-bordered table-striped table-hover">
        <thead>
        <tr>
            <th><?php echo Yii::t("procurement","storage code"); ?></th>
            <th><?php echo Yii::t("procurement","storage time"); ?></th>
            <th><?php echo Yii::t("procurement","Goods Code"); ?></th>
            <th><?php echo Yii::t("procurement","Goods Name"); ?></th>
            <th><?php echo Yii::t("procurement","storage num"); ?></th>
            <th><?php echo Yii::t("procurement","Operator User"); ?></th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
<?php
$this->endWidget();
?>

<script>
    function printStorageTable(id) {
        $('#storageFlow').modal('show');
        $("#tblStorageFlow>tbody").html('<span>加载中....</span>');
        if(id==undefined||id==null){
            id = 0;
        }
        $.ajax({
            type: 'GET',
            url: '<?php echo Yii::app()->createUrl('warehouse/ajaxStorageHistory');?>',
            data: {
                'id':id
            },
            dataType: 'json',
            success: function(data) {
                if(data.status == 1){
                    $("#tblStorageFlow>tbody").html(data.html);
                }else{
                    alert('數據異常');
                }
            },
            error: function(data) { // if error occured
                alert('Error occured.please try again');
            }
        });
    }
    $(function () {
        $(".clickStorageBtn").on("click",function (e) {
            var id = $(this).data("id");
            printStorageTable(id);
            e.stopPropagation();
        })
    })
</script>
