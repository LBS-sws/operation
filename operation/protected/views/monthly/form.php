<?php
$this->pageTitle=Yii::app()->name . ' - Sales Summary Form';
?>
<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'monthly-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('monthly','Sales Summary Form'); ?></strong>
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
			$retPath = 'monthly/'.$model->listform;
			echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl($retPath))); 
		?>
<?php if (!$model->isReadOnly() && (empty($model->wfstatus) || $model->wfstatus=='PS') && Yii::app()->user->validRWFunction('YA01')): ?>
		<?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Save'), array(
			'submit'=>Yii::app()->createUrl('monthly/save'),)); 
		?>
<?php endif ?>
<?php if (!$model->isReadOnly() && empty($model->wfstatus) && Yii::app()->user->validRWFunction('YA01')) : ?>
		<?php echo TbHtml::button('<span class="fa fa-upload"></span> '.Yii::t('misc','Submit'), array(
			'submit'=>Yii::app()->createUrl('monthly/submit'))); 
		?>
<?php endif ?>
<?php if (!$model->isReadOnly() && $model->wfstatus=='PS' && Yii::app()->user->validRWFunction('YA01')): ?>
		<?php echo TbHtml::button('<span class="fa fa-save"></span> '.Yii::t('misc','Submit'), array(
			'submit'=>Yii::app()->createUrl('monthly/resubmit'))); 
		?>
<?php endif ?>
<?php if ($model->wfstatus=='PA' && Yii::app()->user->validFunction('YN01')): ?>
		<?php echo TbHtml::button('<span class="fa fa-check"></span> '.Yii::t('misc','Approve'), array(
			'submit'=>Yii::app()->createUrl('monthly/accept')));
		?>
		<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Deny'), array(
			'id'=>'btnDeny'));
		?>
<?php endif ?>
<?php if ($model->wfstatus=='PH' && $model->validUserInCurrentAction()): ?>
		<?php echo TbHtml::button('<span class="fa fa-check"></span> '.Yii::t('misc','Approve'), array(
			'submit'=>Yii::app()->createUrl('monthly/acceptm')));
		?>
		<?php echo TbHtml::button('<span class="fa fa-remove"></span> '.Yii::t('misc','Deny'), array(
			'id'=>'btnDenyM'));
		?>
<?php endif ?>
	</div>
	<div class="btn-group pull-right" role="group">
	<?php 
		$counter = ($model->no_of_attm['oper1'] > 0) ? ' <span id="docoper1" class="label label-info">'.$model->no_of_attm['oper1'].'</span>' : ' <span id="docoper1"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('monthly','System Report').$counter, array(
			'name'=>'btnOper1','id'=>'btnOper1','data-toggle'=>'modal','data-target'=>'#fileuploadoper1',)
		);
	?>
	<?php 
		$counter = ($model->no_of_attm['oper2'] > 0) ? ' <span id="docoper2" class="label label-info">'.$model->no_of_attm['oper2'].'</span>' : ' <span id="docoper2"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('monthly','Puriscent Report').$counter, array(
			'name'=>'btnOper2','id'=>'btnOper2','data-toggle'=>'modal','data-target'=>'#fileuploadoper2',)
		);
	?>
	<?php 
		$counter = ($model->no_of_attm['oper3'] > 0) ? ' <span id="docoper3" class="label label-info">'.$model->no_of_attm['oper3'].'</span>' : ' <span id="docoper3"></span>';
		echo TbHtml::button('<span class="fa  fa-file-text-o"></span> '.Yii::t('monthly','Purification Report').$counter, array(
			'name'=>'btnOper3','id'=>'btnOper3','data-toggle'=>'modal','data-target'=>'#fileuploadoper3',)
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
				array('size'=>40,'maxlength'=>100,'readonly'=>($model->isReadOnly()||$data['updtype']!='M'))
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
													'doctype'=>'OPER1',
													'header'=>Yii::t('monthly','System Report'),
													'ronly'=>($model->scenario=='view' || $model->isReadOnly()),
													)); 
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'OPER2',
													'header'=>Yii::t('monhtly','Puriscent Report'),
													'ronly'=>($model->scenario=='view' || $model->isReadOnly()),
													)); 
?>
<?php $this->renderPartial('//site/fileupload',array('model'=>$model,
													'form'=>$form,
													'doctype'=>'OPER3',
													'header'=>Yii::t('monthly','Purification Report'),
													'ronly'=>($model->scenario=='view' || $model->isReadOnly()),
													)); 
?>
<?php $this->renderPartial('//monthly/reason',array('model'=>$model,'form'=>$form)); ?>

<script>
		
		$('#MonthlyForm_record_1_datavalue, #MonthlyForm_record_2_datavalue, #MonthlyForm_record_3_datavalue, #MonthlyForm_record_4_datavalue, #MonthlyForm_record_5_datavalue, #MonthlyForm_record_6_datavalue').focusout(function() {
			$('#MonthlyForm_record_1_datavalue').val((+$('#MonthlyForm_record_1_datavalue').val() || 0 ));
			$('#MonthlyForm_record_2_datavalue').val((+$('#MonthlyForm_record_2_datavalue').val() || 0 ));
			$('#MonthlyForm_record_3_datavalue').val((+$('#MonthlyForm_record_3_datavalue').val() || 0 ));
			$('#MonthlyForm_record_4_datavalue').val((+$('#MonthlyForm_record_4_datavalue').val() || 0 ));
			$('#MonthlyForm_record_5_datavalue').val((+$('#MonthlyForm_record_5_datavalue').val() || 0 ));
			$('#MonthlyForm_record_6_datavalue').val((+$('#MonthlyForm_record_6_datavalue').val() || 0 ));
			$('#MonthlyForm_record_7_datavalue').val((parseFloat(document.getElementById('MonthlyForm_record_1_datavalue').value) + parseFloat(document.getElementById('MonthlyForm_record_2_datavalue').value) + parseFloat(document.getElementById('MonthlyForm_record_3_datavalue').value) + parseFloat(document.getElementById('MonthlyForm_record_4_datavalue').value) + parseFloat(document.getElementById('MonthlyForm_record_5_datavalue').value)).toFixed(2));
			$('#MonthlyForm_record_8_datavalue').val((parseFloat(document.getElementById('MonthlyForm_record_7_datavalue').value) * 8.5 / 100).toFixed(2));
			$('#MonthlyForm_record_9_datavalue').val((parseFloat(document.getElementById('MonthlyForm_record_6_datavalue').value) * 3.5 / 100).toFixed(2));
			$('#MonthlyForm_record_10_datavalue').val((parseFloat(document.getElementById('MonthlyForm_record_8_datavalue').value) + parseFloat(document.getElementById('MonthlyForm_record_9_datavalue').value)).toFixed(2));
			$('#MonthlyForm_record_11_datavalue').val((parseFloat(document.getElementById('MonthlyForm_record_6_datavalue').value) + parseFloat(document.getElementById('MonthlyForm_record_7_datavalue').value)).toFixed(2));
		});
		
</script>

<?php
Script::genFileUpload($model,$form->id,'OPER1');
Script::genFileUpload($model,$form->id,'OPER2');
Script::genFileUpload($model,$form->id,'OPER3');

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


