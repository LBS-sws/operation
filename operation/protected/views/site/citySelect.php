<style>
    .select2-container{ width:100%!important;}
</style>
<?php
$ftrbtn = array();
$ftrbtn[] = TbHtml::button(Yii::t('dialog','Close'), array('data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_DEFAULT));
$ftrbtn[] = TbHtml::button(Yii::t('misc','Submit'), array('id'=>'citySelectBtn','color'=>TbHtml::BUTTON_COLOR_PRIMARY));
$this->beginWidget('bootstrap.widgets.TbModal', array(
    'id'=>'citySelectDialog',
    'header'=>Yii::t('dialog','Select City'),
    'footer'=>$ftrbtn,
    'show'=>false,
));
?>
<div class="form-horizontal">
    <div class="form-group">
        <?php echo Tbhtml::label(Yii::t("dialog","City"),'',array('class'=>"col-lg-4 control-label")); ?>
        <div class="col-lg-4">
            <?php
            echo Tbhtml::dropDownList("dialog_city", Yii::app()->user->city(),General::getCityListWithCityAllow(Yii::app()->user->city_allow()),
                array('id'=>"dialog_city",'empty'=>'')
            ); ?>
        </div>
    </div>
</div>

<?php
$this->endWidget();
if (!Yii::app()->user->isSingleCity()){
    $openJs = "$('#citySelectDialog').modal('show');";
}else{//没有管辖城市
    $openJs = "window.location.href='{$submitUrl}';";
}
$js="
$('#openCitySelectDialog').click(function(){
    {$openJs}
});

$('#citySelectBtn').click(function(){
    var url ='{$submitUrl}';
    var city = $('#dialog_city').val();
    if(city!=''){
        url+='?city='+city;
    }
    window.location.href=url;
});
$('#citySelectDialog').removeAttr('tabindex');
";
Yii::app()->clientScript->registerScript('citySelect',$js,CClientScript::POS_READY);

switch(Yii::app()->language) {
    case 'zh_cn': $lang = 'zh-CN'; break;
    case 'zh_tw': $lang = 'zh-TW'; break;
    default: $lang = Yii::app()->language;
}
$js="
$('#dialog_city,#aaa').select2({
    multiple: false,
    maximumInputLength: 10,
    language: '$lang',
    disabled: false
});
";
Yii::app()->clientScript->registerScript('searchCitySelectDialog',$js,CClientScript::POS_READY);
?>
