<?php
$this->pageTitle=Yii::app()->name . ' - Cargo Cost List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'cargoCost-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','Technician cargo cost'); ?></strong>
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
    $search_add_html="";
    if(!Yii::app()->user->isSingleCity()){
        $search_add_html .= TbHtml::dropDownList('CargoCostList[city]',$model->city,$model->getCityAllList(),
            array("class"=>"form-control","id"=>"change_city"))."<span style='display:inline-block;width:20px;'>&nbsp;</span>";
    }
    $search_add_html .= TbHtml::textField('CargoCostList[searchTimeStart]',$model->searchTimeStart,
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time","style"=>"width:100px;"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::textField('CargoCostList[searchTimeEnd]',$model->searchTimeEnd,
        array('size'=>15,'placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time","style"=>"width:100px;"));
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('procurement','Order List'),
        'model'=>$model,
        'viewhdr'=>'//cargoCost/_listhdr',
        'viewdtl'=>'//cargoCost/_listdtl',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'order_code',
            'lcu',
            'goods_name',
            'status',
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
$('#start_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('#end_time').datepicker({autoclose: true, format: 'yyyy/mm/dd',language: 'zh_cn'});
$('.checkBoxDown').on('click',function(e){
    e.stopPropagation();
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);
	$js = Script::genTableRowClick();
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>

