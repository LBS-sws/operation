<?php
$this->pageTitle=Yii::app()->name . ' - RankingQuarter';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'rankingQuarter-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technical Quarter leaderboard'); ?></strong>
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
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[month]",$model->month,RankingQuarterList::getQuarterList(),
        array("class"=>"form-control submitBtn"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('rank','RankingQuarter List'),
        'model'=>$model,
        'viewhdr'=>'//rankingQuarter/_listhdr',
        'viewdtl'=>'//rankingQuarter/_listdtl',
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
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
