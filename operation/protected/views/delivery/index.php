<?php
$this->pageTitle=Yii::app()->name . ' - Delivery List';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'delivery-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('procurement','Delivery List'); ?></strong>
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
    <div class="box">
        <div class="box-body">
            <div class="btn-group" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-paper-plane"></span> '.Yii::t('procurement','All shipments'), array(
                    'submit'=>Yii::app()->createUrl('delivery/allApproved'),
                ));
                ?>
            </div>
            <div class="btn-group pull-right" role="group">
                <?php
                echo TbHtml::button('<span class="fa fa-cloud-download"></span> '.Yii::t('misc','Download'), array(
                    'submit'=>Yii::app()->createUrl('delivery/allDownload'),
                ));
                ?>
            </div>
        </div>
    </div>
	<?php
    $search_add_html="";
    if(!Yii::app()->user->isSingleCity()){
        $search_add_html .= TbHtml::dropDownList('DeliveryList[city]',$model->city,$model->getCityAllList(),
            array("class"=>"form-control","id"=>"change_city"))."<span style='display:inline-block;width:20px;'>&nbsp;</span>";
    }
    $search_add_html .= TbHtml::textField('DeliveryList[searchTimeStart]',$model->searchTimeStart,
        array('size'=>15,'placeholder'=>Yii::t('misc','Start Date'),"class"=>"form-control","id"=>"start_time"));
    $search_add_html.="<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>";
    $search_add_html .= TbHtml::textField('DeliveryList[searchTimeEnd]',$model->searchTimeEnd,
        array('size'=>15,'placeholder'=>Yii::t('misc','End Date'),"class"=>"form-control","id"=>"end_time"));
    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('procurement','Order List'),
        'model'=>$model,
        'viewhdr'=>'//delivery/_listhdr',
        'viewdtl'=>'//delivery/_listdtl',
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

