
function showImg(obj, imgSrc) {
    if($(obj).next(".fileImgShow").length==1){
        $(obj).next(".fileImgShow").find("img").attr("src",imgSrc);
        $(obj).next(".fileImgShow").show();
    }else{
        var div ='<div class="media fileImgShow"><div class="media-left"><img src="'+imgSrc+'" width="500px"></div>';
        div+='<div class="media-body media-bottom"><a>修改</a></div></div>';
        $(obj).after(div);
    }
    $(obj).hide();
}

$(function ($) {
    $("input[type='file']").each(function () {
        var imgSrc = $(this).prev("input").val();
        if(imgSrc!=''&&imgSrc!=undefined){
            showImg(this,imgSrc);
        }
    });
    $("input[type='file']").change(function (e) {
        var tag = e.target;
        var file = tag.files[0];
        var other=this;
        if (!/image\/\w+/.test(file.type)) {
            alert("请选择图片！");
            $(this).val('');
            return false;
        }
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function() {
            showImg(other,this.result);
        };
    });
    $("body").delegate(".fileImgShow a",'click',function () {
        $(this).parents(".fileImgShow").hide();
        $(this).parents(".fileImgShow").prev('input[type="file"]').show();
    });
});