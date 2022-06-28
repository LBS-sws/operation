<?php
$this->pageTitle=Yii::app()->name . ' - RankingMonth Form';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'RankingMonth-form',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_HORIZONTAL,
)); ?>
<style>
    select[readonly]{ pointer-events: none;}
</style>

<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('rank','RankingMonth Form'); ?></strong>
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
		<?php echo TbHtml::button('<span class="fa fa-reply"></span> '.Yii::t('misc','Back'), array(
				'submit'=>Yii::app()->createUrl('rankingMonth/index')));
		?>
	</div>
	</div></div>

	<div class="box box-info">
		<div class="box-body">
			<?php echo $form->hiddenField($model, 'scenario'); ?>
			<?php echo $form->hiddenField($model, 'id'); ?>

            <div class="form-group">
                <?php echo $form->labelEx($model,'rank',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'rank',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->labelEx($model,'rank_year',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'rank_year',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'rank_month',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'rank_month',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo $form->labelEx($model,'name',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->textField($model, 'name',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
            <?php
            foreach (RankingMonthForm::$sqlDate as $item=>$rule){
                if($rule["reset"]){
                    echo '<div class="form-group">';
                    echo TbHtml::label(Yii::t("rank",$rule["label"]),"",array('class'=>"col-lg-2 control-label"));
                    echo '<div class="col-lg-3">';
                    echo TbHtml::textField($item,$model->arrList[$item],array('readonly'=>(true)));
                    echo '</div>';
                    echo '<div class="col-lg-3"><p class="form-control-static">';
                    echo TbHtml::link(Yii::t("rank","detail"),'javascript:void(0);',array(
                        'class'=>'btn_detail',
                        'data-title'=>Yii::t("rank",$rule["label"]),
                        'data-value'=>$item,
                        'data-id'=>$model->employee_id,
                        'data-year'=>$model->rank_year,
                        'data-month'=>$model->rank_month,
                    ));
                    echo '</p></div>';
                    echo '</div>';
                }
            }
            ?>
            <div class="form-group">
                <?php echo $form->labelEx($model,'score_sum',array('class'=>"col-lg-2 control-label")); ?>
                <div class="col-lg-3">
                    <?php echo $form->hiddenField($model, 'other_score'); ?>
                    <?php echo $form->textField($model, 'score_sum',
                        array('readonly'=>(true))
                    ); ?>
                </div>
            </div>
		</div>
	</div>
</section>

<div class="modal fade" tabindex="-1" role="dialog" id="detailDialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Modal title</h4>
            </div>
            <div class="modal-body">
                <p>加载中....</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php $this->renderPartial('//site/removedialog'); ?>
<?php $this->renderPartial('//site/rankingNote'); ?>

<?php
$ajaxUrl = Yii::app()->createUrl('rankingMonth/ajaxDetail');
$js = "
$('.btn_detail').on('click',function(){
    $('#detailDialog').find('.modal-title').text($(this).data('title'));
    $('#detailDialog').find('.modal-body').html('<p>加载中....</p>');
    $('#detailDialog').modal('show');
    $.ajax({
        type: 'GET',
        url: '{$ajaxUrl}',
        data: {
            'id':$(this).data('id'),
            'type':1,
            'value':$(this).data('value'),
            'year':$(this).data('year'),
            'month':$(this).data('month')
        },
        dataType: 'json',
        success: function(data) {
            $('#detailDialog').find('.modal-body').html(data['html']);
        },
        error: function(data) { // if error occured
            alert('Error occured.please try again');
        }
    });
});
";
Yii::app()->clientScript->registerScript('calcFunction',$js,CClientScript::POS_READY);

$js = Script::genReadonlyField();
Yii::app()->clientScript->registerScript('readonlyClass',$js,CClientScript::POS_READY);
?>

<?php $this->endWidget(); ?>


