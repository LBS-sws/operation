
<?php
//2024年9月28日09:28:46
?>
<?php
$this->pageTitle=Yii::app()->name . ' - CurlNotes';
?>

<?php $form=$this->beginWidget('TbActiveForm', array(
'id'=>'curlNotes-list',
'enableClientValidation'=>true,
'clientOptions'=>array('validateOnSubmit'=>true,),
'layout'=>TbHtml::FORM_LAYOUT_INLINE,
)); ?>
<style>
    td{word-break: break-all;}
</style>
<!-- -->
<section class="content-header">
	<h1>
		<strong><?php echo Yii::t('app','LBS To JD'); ?></strong>
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
            <div class="pull-left">
                <p style="margin: 7px 0px;">未进行：P，完成：C，错误：E。</p>
            </div>
            <div class="pull-right text-danger">
                <p style="margin: 7px 0px;">重新发送：把数据原文发送，需要金蝶处理是否会造成重复添加</p>
            </div>
        </div>
    </div>
	<?php
    $search_add_html="";
    $modelName = get_class($model);
    $typeList=CurlNotesList::getInfoTypeList();
    if(!empty($typeList)){
        $typeList = array_merge(array(""=>"-- 全部 --"),$typeList);
        $search_add_html .= TbHtml::dropDownList($modelName.'[info_type]',$model->info_type,$typeList,
            array("class"=>"form-control submitBtn"));
    }

    $this->widget('ext.layout.ListPageWidget', array(
        'title'=>Yii::t('curl','CurlNotes List'),
        'model'=>$model,
        'viewhdr'=>'//curlNotes/_listhdr',
        'viewdtl'=>'//curlNotes/_listdtl',
        'gridsize'=>'24',
        'height'=>'600',
        'search_add_html'=>$search_add_html,
        'search'=>array(
            'status_type',
            'info_type',
            'info_url',
            'data_content',
            'out_content',
            'message',
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
<div class="modal fade" tabindex="-1" role="dialog" id="textModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">内容详情</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <textarea class="form-control" rows="7" id="textInput"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
    function unicode2Ch(t) {
        if (t) {
            for (var e = 1, n = "", i = 0; i < t.length; i += e) {
                e = 1;
                var o = t.charAt(i);
                if ("\\" == o)
                    if ("u" == t.charAt(i + 1)) {
                        var r = t.substr(i + 2, 4);
                        n += String.fromCharCode(parseInt(r, 16).toString(10)),
                            e = 6
                    } else
                        n += o;
                else
                    n += o
            }
            return n
        }
    }
</script>
<?php
	$js = "
	    $('.text-break').click(function(){
	        var text = $(this).children('pre').text();
	        if(typeof text=='object'){
	            text = JSON.stringify(text);
	        }
	        text = unicode2Ch(text);
	        $('#textInput').val(text);
	        $('#textModal').modal('show');
	    });
	    
$('.submitBtn').change(function(){
    $('form:first').submit();
});
	";
	Yii::app()->clientScript->registerScript('rowClick',$js,CClientScript::POS_READY);
?>
