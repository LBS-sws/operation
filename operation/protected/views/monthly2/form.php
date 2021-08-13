<?php
$this->pageTitle=Yii::app()->name . ' - Sales Summary Form - ID';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Sales Summary Form - ID'); ?></strong>
	</h1>
</section>

<section class="content">
	<div class="box"><div class="box-body">
	<div class="btn-group" role="group">
		<?php 
			$retPath = 'monthly2/'.$model->listform;
			echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl($retPath))); 
		?>
<?php if (!$model->isReadOnly() && (empty($model->wfstatus) || $model->wfstatus=='PS') && Yii::app()->user->validRWFunction('YE01')): ?>
		<?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
			'submit'=>Yii::app()->createUrl('monthly2/save'),)); 
		?>
<?php endif ?>
<?php if (!$model->isReadOnly() && empty($model->wfstatus) && Yii::app()->user->validRWFunction('YE01')) : ?>
		<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Submit'), array(
			'submit'=>Yii::app()->createUrl('monthly2/submit'))); 
		?>
<?php endif ?>
<?php if (!$model->isReadOnly() && $model->wfstatus=='PS' && Yii::app()->user->validRWFunction('YE01')): ?>
		<?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Submit'), array(
			'submit'=>Yii::app()->createUrl('monthly2/resubmit'))); 
		?>
<?php endif ?>
<?php if ($model->wfstatus=='PA' && Yii::app()->user->validFunction('YN01')): ?>
		<?php echo TbHtml::button('<span class="fa fa-check"></span> '.Yii::t('misc','Approve'), array(
			'submit'=>Yii::app()->createUrl('monthly2/accept')));
		?>
		<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Deny'), array(
			'id'=>'btnDeny'));
		?>
<?php endif ?>
<?php if ($model->wfstatus=='PH' && $model->validUserInCurrentAction()): ?>
		<?php echo TbHtml::button('<span class="fa fa-check"></span> '.Yii::t('misc','Approve'), array(
			'submit'=>Yii::app()->createUrl('monthly2/acceptm')));
		?>
		<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Deny'), array(
			'id'=>'btnDenyM'));
		?>
<?php endif ?>
		<?php echo TbHtml::button('<span class="fa fa-file-o"></span> '.Yii::t('monthly','Export'), array(
			'id'=>'btnExcel'));
		?>
	</div>
	<div class="btn-group pull-right" role="group">
	<?php 
		$counter = ($model->no_of_attm['operb1'] > 0) ? ' <span id="docoperb1" class="label label-info">'.$model->no_of_attm['operb1'].'</span>' : ' <span id="docoperb1"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('monthly','Air Service Report').$counter, array(
			'name'=>'btnOperb1','id'=>'btnOperb1','data-toggle'=>'modal','data-target'=>'#fileuploadoperb1',)
		);
	?>
	<?php 
		$counter = ($model->no_of_attm['operb2'] > 0) ? ' <span id="docoperb2" class="label label-info">'.$model->no_of_attm['operb2'].'</span>' : ' <span id="docoperb2"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('monthly','Machine Selling Report').$counter, array(
			'name'=>'btnOperb2','id'=>'btnOperb2','data-toggle'=>'modal','data-target'=>'#fileuploadoperb2',)
		);
	?>
	<?php 
		$counter = ($model->no_of_attm['operb3'] > 0) ? ' <span id="docoperb3" class="label label-info">'.$model->no_of_attm['operb3'].'</span>' : ' <span id="docoperb3"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('monthly','Maintenance Service Report').$counter, array(
			'name'=>'btnOperb3','id'=>'btnOperb3','data-toggle'=>'modal','data-target'=>'#fileuploadoperb3',)
		);
	?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>
			<?php echo $form->hiddenField($model, 'lcd'); ?>
			<?php echo $form->hiddenField($model, 'city'); ?>
			<?php echo $form->hiddenField($model, 'wfstatus'); ?>
			<?php echo $form->hiddenField($model, 'listform'); ?>

			<div class="form-group">
				<?php echo $form->labelEx($model,'year_no',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'year_no', 
						array('size'=>10,'readonly'=>true)
					); ?>
				</div>
				<?php echo $form->labelEx($model,'month_no',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'month_no', 
						array('size'=>10,'readonly'=>true)
					); ?>
				</div>
			</div>
	
			<div class="form-group">
				<?php echo $form->labelEx($model,'city_name',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-2">
					<?php echo $form->textField($model, 'city_name', 
						array('size'=>10,'readonly'=>true)
					); ?>
				</div>
				<?php echo $form->labelEx($model,'wfstatusdesc',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-3">
					<?php 
						echo $form->textField($model, 'wfstatusdesc', array('readonly'=>true)); 
					?>
				</div>
			</div>

<?php if (!empty($model->reason)) : ?>
			<div class="form-group">
				<?php echo $form->labelEx($model,'reason',array('class'=>"col-sm-2 control-label")); ?>
				<div class="col-sm-7">
					<?php echo $form->textArea($model, 'reason', 
					array('rows'=>2,'cols'=>60,'readonly'=>true)
					); ?>
				</div>
			</div>
<?php endif ?>
			<legend>&nbsp;</legend>
	
<?php
	$modelName = get_class($model);
	$cnt=0;
	foreach ($model->record as $key=>$data) {
		$cnt++;
		$id_prefix = $modelName.'_record_'.$key;
		$name_prefix = $modelName.'[record]['.$key.']';
		echo '<div class="form-group">';
		echo '<div class="col-sm-4">';
		echo  TbHtml::label($cnt.'. '.$data['name'].($data['updtype']!='M' ? ' *' : ''),$id_prefix.'_datavalue');
		echo '</div>';
		echo '<div class="col-sm-3">';
		echo TbHtml::textField($name_prefix.'[datavalue]',$data['datavalue'],
				array('maxlength'=>100,'readonly'=>($model->isReadOnly()||$data['updtype']!='M'))
			);		
		echo TbHtml::hiddenField($name_prefix.'[id]',$data['id']);
		echo TbHtml::hiddenField($name_prefix.'[code]',$data['code']);
		echo TbHtml::hiddenField($name_prefix.'[name]',$data['name']);
		echo TbHtml::hiddenField($name_prefix.'[datavalueold]',$data['datavalueold']);
		echo TbHtml::hiddenField($name_prefix.'[updtype]',$data['updtype']);
		echo TbHtml::hiddenField($name_prefix.'[fieldtype]',$data['fieldtype']);
		echo TbHtml::hiddenField($name_prefix.'[manualinput]',$data['manualinput']);
		echo '</div>';
		echo '</div>';
	}
?>
		</div>
	</div>
</section>

<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'OPERB1',
													'header'=>Yii::t('monthly','Air Service Report'),
													'ronly'=>($model->scenario=='view' || $model->isReadOnly()),
													)); 
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'OPERB2',
													'header'=>Yii::t('monthly','Machine Selling Report'),
													'ronly'=>($model->scenario=='view' || $model->isReadOnly()),
													)); 
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'OPERB3',
													'header'=>Yii::t('monthly','Maintenance Service Report'),
													'ronly'=>($model->scenario=='view' || $model->isReadOnly()),
													)); 
?>
<?php $this->renderPartial('//monthly2/reason',array('model'=>$model,'form'=>$form)); ?>

<script>
function roundNumber(num, scale) {
  if(!("" + num).includes("e")) {
    return +(Math.round(num + "e+" + scale)  + "e-" + scale);
  } else {
    var arr = ("" + num).split("e");
    var sig = ""
    if(+arr[1] + scale > 0) {
      sig = "+";
    }
    return +(Math.round(+arr[0] + "e" + sig + (+arr[1] + scale)) + "e-" + scale);
  }
}		

$('#Monthly2Form_record_1_datavalue, #Monthly2Form_record_2_datavalue, #Monthly2Form_record_3_datavalue').focusout(function() {
	$('#Monthly2Form_record_1_datavalue').val(parseFloat(+$('#Monthly2Form_record_1_datavalue').val() || 0 ).toFixed(2));
	$('#Monthly2Form_record_2_datavalue').val(parseFloat(+$('#Monthly2Form_record_2_datavalue').val() || 0 ).toFixed(2));
	$('#Monthly2Form_record_3_datavalue').val(parseFloat(+$('#Monthly2Form_record_3_datavalue').val() || 0 ).toFixed(2));
	var total = parseFloat(document.getElementById('Monthly2Form_record_1_datavalue').value) + parseFloat(document.getElementById('Monthly2Form_record_2_datavalue').value) + parseFloat(document.getElementById('Monthly2Form_record_3_datavalue').value);
	$('#Monthly2Form_record_4_datavalue').val(total);
});
</script>

<script>
	function numberWithCommas(x) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
	
	$('#btnExcel').on('click',function(){
		var output = '<table><tr><td>史伟莎ID营业报告</td><td>地区:'+$('#Monthly2Form_city_name').val()+'</td></tr>';
		output += '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
		output += '<tr><td>日期：'+$('#MonthlyForm_year_no').val()+'年'+$('#Monthly2Form_month_no').val()+'月份</td><td>人民币（元）</td></tr>';
		$("[id^='Monthly2Form_record_'][id$='_datavalue']").each(function(){
			var id = $(this).attr('id');
			output += '<tr>';
			output += '<td>'+$("label[for='"+id+"']").text()+'</td>';
			output += '<td>'+numberWithCommas($(this).val())+'</td>';
			output += '</tr>';
		});
		output += '</table>';
		window.open('data:application/vnd.ms-excel,'+output);
	});
</script>

<?php
Script::genFileUpload($model,$form->id,'OPERB1');
Script::genFileUpload($model,$form->id,'OPERB2');
Script::genFileUpload($model,$form->id,'OPERB3');

$js=<<<EOF
$('#btnDeny').on('click',function(){
	$('#btnRmkOk').show();
	$('#btnRmkOkMgr').hide();
	$('#rmkdialog').modal('show');
});
$('#btnDenyM').on('click',function(){
	$('#btnRmkOk').hide();
	$('#btnRmkOkMgr').show();
	$('#rmkdialog').modal('show');
});
EOF;
Yii::app()->clientScript->registerScript('denyPopup',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


