<?php
$this->pageTitle=Yii::app()->name . ' - Report';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'report-form',
'action'=>Yii::app()->createUrl('report/generate'),
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    .datepicker{z-index: 2000 !important;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('report',$model->name); ?></strong>
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
        if($model->id == "RptBackward"){
            echo TbHtml::button(Yii::t('misc','Submit'), array(
                'submit'=>Yii::app()->createUrl('report/backward')));
        }else{
            echo TbHtml::button(Yii::t('misc','Submit'), array(
                'submit'=>Yii::app()->createUrl('report/pickinglist')));
        }
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'name'); ?>
			<?php echo $form->hiddenField($model, 'fields'); ?>
			<?php echo $form->hiddenField($model, 'user_ids'); ?>


            <?php if ($model->showField('city') && !Yii::app()->user->isSingleCity()): ?>
                <div class="form-group">
                    <?php
                    echo TbHtml::label("快捷操作","",array('class'=>"col-sm-2 control-label"));
                    ?>
                    <div class="col-sm-10">
                        <?php
                        echo TbHtml::checkBox("0",false,array('label'=>"全部","class"=>"fastChange",'data-city'=>"",'labelOptions'=>array("class"=>"checkbox-inline")));
                        $fastCityList = General::getCityListForArea();
                        foreach ($fastCityList as $row){
                            echo TbHtml::checkBox($row["code"],false,array('label'=>$row["name"],"class"=>"fastChange",'data-city'=>$row["city"],'labelOptions'=>array("class"=>"checkbox-inline")));
                        }
                        ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model,'city',array('class'=>"col-sm-2 control-label")); ?>
                    <div class="col-sm-10" id="report_look_city">
                        <?php
                        $item = General::getCityListWithCityAllow(Yii::app()->user->city_allow());
                        if (empty($model->city)) {
                            $model->city = array();
                            foreach ($item as $key=>$value) {$model->city[] = $key;}
                        }
                        echo $form->inlineCheckBoxList($model,'city', $item,
                            array('id'=>'look_city'));
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <?php echo $form->hiddenField($model, 'city'); ?>
            <?php endif ?>
		
			<div class="form-group">
				<?php echo $form->labelEx($model,'start_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'start_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
				<?php echo $form->labelEx($model,'end_dt',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<div class="input-group date">
						<div class="input-group-addon">
							<i class="fa fa-calendar"></i>
						</div>
						<?php echo $form->textField($model, 'end_dt', 
							array('class'=>'form-control pull-right','readonly'=>($model->scenario=='view'),)); 
						?>
					</div>
				</div>
			</div>

			<div class="form-group">
					<?php echo $form->labelEx($model,'user_ids',array('class'=>"col-sm-2 control-label")); ?>
					<div class="col-sm-6">
						<?php 
							echo $form->textArea($model, 'user_names',
								array('rows'=>4,'cols'=>80,'maxlength'=>1000,'readonly'=>true,)
							); 
						?>
					</div>
					<div class="col-sm-2">
						<?php
							echo TbHtml::button('<span class="fa fa-search"></span> '.Yii::t('report','Order Person'),
										array('name'=>'btnUsers','id'=>'btnUsers',)
								);
						?>
					</div>
			</div>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/lookup'); ?>

<?php
$js = Script::genLookupSearchEx();
Yii::app()->clientScript->registerScript('lookupSearch',$js,CClientScript::POS_READY);

$js = Script::genLookupButtonEx('btnUsers', 'yc02user', 'user_ids', 'user_names', 
		array(),
		true
	);
Yii::app()->clientScript->registerScript('lookupUsers',$js,CClientScript::POS_READY);

$js = Script::genLookupSelect();
Yii::app()->clientScript->registerScript('lookupSelect',$js,CClientScript::POS_READY);

$js = Script::genDatePicker(array(
			'ReportY03Form_start_dt',
			'ReportY03Form_end_dt',
		));
Yii::app()->clientScript->registerScript('datePick',$js,CClientScript::POS_READY);
?>

<?php
$js="
$('.fastChange').change(function(){
    var cityStr = ','+$(this).data('city')+',';
    console.log(cityStr);
    var checkBool = $(this).is(':checked')?true:false;
    $('#report_look_city').find('input[type=\"checkbox\"]').each(function(){
        var city = ','+$(this).val()+',';
        if(cityStr==',,'||cityStr.indexOf(city)>-1){
            $(this).prop('checked',checkBool);
        }
    });
});
";
Yii::app()->clientScript->registerScript('fastChange',$js,CClientScript::POS_READY);
?>
<?php $this->endWidget(); ?>

</div><!-- form -->

