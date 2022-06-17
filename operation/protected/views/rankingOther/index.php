<?php
$this->pageTitle=Yii::app()->name . ' - RankingYear';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'rankingYear-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technical Other leaderboard'); ?></strong>
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
	<?php
    $modelClass=get_class($model);
    $search_add_html="";
    if(Yii::app()->user->validFunction('YN06')) {
        $search_add_html .= TbHtml::dropDownList("{$modelClass}[allCity]", $model->allCity, RankingMonthList::getAllCityTypeList(),
            array("class" => "form-control submitBtn"));
        $search_add_html .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    }
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[rank_type]", $model->rank_type, RankingOtherList::getRankTypeList(),
        array("class" => "form-control submitBtn"));
    $search_add_html .= "<span>&nbsp;&nbsp;&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[year]",$model->year,ServiceMoneyList::getYearList(),
        array("class"=>"form-control submitBtn"));
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[year_type]",$model->year_type,RankingOtherList::getYearTypeList(),
        array("class"=>"form-control","id"=>"btnYearType","data-month"=>$model->month));
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[month]",$model->month,RankingOtherList::getAllMonthList(),
        array("class"=>"form-control submitBtn","id"=>"btnMonth"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('app','Technical Other leaderboard'),
        'model'=>$model,
        'viewhdr'=>'//rankingOther/_listhdr',
        'viewdtl'=>'//rankingOther/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(),
    ));
	?>
</section>
<?php
	echo $form->hiddenField($model,'pageNum');
	echo $form->hiddenField($model,'totalRow');
	echo $form->hiddenField($model,'orderField');
	echo $form->hiddenField($model,'orderType');
?>
<?php $this->endWidget(); ?>
<?php
$js = "
$('#btnYearType').change(function(){
    var type = $(this).val();
    var month = $(this).data('month');
    type = parseInt(type,10);
    $('#btnMonth>option').hide();
    $('#btnMonth').val('');
    switch (type){
        case 1:
            $('#btnMonth>option').slice(1,13).show();
            break;
        case 2:
            $('#btnMonth>option').slice(13,17).show();
            break;
        case 3:
            $('#btnMonth>option').slice(17,19).show();
            break;
        default:
            $('#btnMonth>option').slice(19).show();
    }
    if(month!=0){
        $('#btnMonth').val(month);
        $(this).data('month',0);
    }
}).trigger('change');
$('.submitBtn').change(function(){
    $('form:first').submit();
});
$('select[id$=\"List_searchField\"]').hide();
$('input[id$=\"List_searchValue\"]').attr('placeholder','仅限页内搜索');
$('#yt0').hide();
$('input[id$=\"List_searchValue\"]').keyup(function(){
    var search = $(this).val();
    var username,city;
    if(search!=''){
        $('#tblData>tbody>tr').each(function(){
            username = $(this).find('td').eq(2).text();
            city = $(this).find('td').eq(3).text();
            if(username.indexOf(search)>=0||city.indexOf(search)>=0){
                $(this).show();
            }else{
                $(this).hide();
            }
        });
    }else{
        $('#tblData>tbody>tr').show();
    }
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
?>
