<?php
$this->pageTitle=Yii::app()->name . ' - ServiceDeduct';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'serviceDeduct-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technical Deduct Marks'); ?></strong>
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
			if (Yii::app()->user->validRWFunction('TL07'))
				echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('misc','Add'), array(
					'submit'=>Yii::app()->createUrl('serviceDeduct/new'),
				)); 
		?>
	</div>
	</div></div>
	<?php
    $modelClass=get_class($model);
    $search_add_html="";
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[year]",$model->year,ServiceMoneyList::getYearList(),
        array("class"=>"form-control submitBtn"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::dropDownList("{$modelClass}[month]",$model->month,ServiceMoneyList::getMonthList(),
        array("class"=>"form-control submitBtn"));

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('rank','ServiceDeduct List'),
        'model'=>$model,
        'viewhdr'=>'//serviceDeduct/_listhdr',
        'viewdtl'=>'//serviceDeduct/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'service_code',
            'code',
            'name',
            'city_name',
        ),
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
