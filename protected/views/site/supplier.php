<?php
	$hidden1 = TbHtml::hiddenField('clickNum', '',array("id"=>"clickNumSupplier"));
	
	$search = TbHtml::textField('txtSupplier', '', array('class'=>'form-control','maxlength'=>500,
				'append'=>TbHtml::button(Yii::t('misc','Search'),array('name'=>'btnSupplierSearch','id'=>'btnSupplierSearch')),
			)); 
	$list = CHtml::listBox('lstSupplier', '', array(), array('class'=>'form-control','size'=>10,)
			);
			
	$content = "
<div class=\"row\">
	$hidden1
	<div class=\"col-sm-11\">
			$search
	</div>
</div>
<div class=\"row\">
	<div class=\"col-sm-11\" id=\"supplier-list\">
			$list
	</div>
</div>
	";

	$this->widget('bootstrap.widgets.TbModal', array(
					'id'=>'selectSupplierModel',
					'header'=>Yii::t('procurement','select supplier')."<span id='title_add_text'></span>",
					'content'=>$content,
					'footer'=>array(
						TbHtml::button(Yii::t('dialog','Select'), array('id'=>'btnSupplierSelect','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
						TbHtml::button(Yii::t('dialog','Close'), array('id'=>'btnSupplierCancel','data-dismiss'=>'modal','color'=>TbHtml::BUTTON_COLOR_PRIMARY)),
					),
					'show'=>false,
				));
?>

<script>
	$(function () {
		$("#table_storage").delegate(".select_supplier","click",function () {
		    var $thisTr = $(this).parents("tr:first");
		    var text =" （"+$thisTr.find("input[name*='[name]']").val()+"）";
		    $("#title_add_text").text(text);
		    $("#clickNumSupplier").val($thisTr.data("num"));
			$("#selectSupplierModel").modal("show");
        });

        $("#btnSupplierSearch").on("click",function(){
            $.ajax({
                type: "GET",
                url: "<?php echo Yii::app()->createAbsoluteUrl('lookup').'/supplierSearch'; ?>",
                data: {search:$("#txtSupplier").val()},
                dataType: "json",
                success: function(data) {
                    $("#lstSupplier").empty();
                    var num = $("#clickNumSupplier").val();
                    var selectList = $("#table_storage>tr[data-num='"+num+"']").find(".supplier_id:first").val();
                    var option = "";
                    $.each(data, function(index, element) {
                        option = "<option value='"+element.id+"' ";
                        option+=" data-code='"+element.code+"' ";
                        option+=" data-name='"+element.name+"' ";
                        if(element.id == selectList){
                            option+=" selected ";
                        }
                        option+=">"+element.code+" -- "+element.name+"</option>";
                        $("#lstSupplier").append(option);
                    });

                    var count = $("#lstSupplier").children().length;
                    if (count<=0) $("#lstSupplier").append("<option value='-1'><?php echo Yii::t('dialog','No Record Found');?></option>");
                },
                error: function(data) { // if error occured
                    alert("Error occured.please try again");
                }
            });
        });

        $("#btnSupplierSelect").on("click",function () {
            var num = $("#clickNumSupplier").val();
            var select = $("#lstSupplier option:selected").eq(0);
            if(select.length>0){
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_text:first").text(select.data("name"));
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_id:first").val(select.val());
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_code:first").val(select.data("code"));
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_name:first").val(select.data("name"));
			}else{
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_text:first").text(" - ");
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_id:first").val("");
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_code:first").val("");
                $("#table_storage>tr[data-num='"+num+"']").find(".supplier_name:first").val("");
			}
        })
    })
</script>


