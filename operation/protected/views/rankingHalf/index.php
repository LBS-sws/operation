<?php
$this->pageTitle=Yii::app()->name . ' - RankingHalf';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'rankingHalf-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technical Half leaderboard'); ?></strong>
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
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[year]",$model->year,ServiceMoneyList::getYearList(),
        array("class"=>"form-control submitBtn"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[month]",$model->month,RankingHalfList::getHalfList(),
        array("class"=>"form-control submitBtn"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('rank','RankingHalf List'),
        'model'=>$model,
        'viewhdr'=>'//rankingHalf/_listhdr',
        'viewdtl'=>'//rankingHalf/_listdtl',
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
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
