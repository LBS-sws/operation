
function waitingMessageHeader(url,str) {
    console.log(str);
    var $message = $("#navbar-collapse a[href='"+str+"/index.php/purchase/index']");
    if($message.length == 1){
        $.ajax({
            type: "post",
            url: url,
            data: {id:""},
            dataType: "json",
            success: function(data){
                console.log(data);
                if(data["fast_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/fast/index']").append("<span class='badge'>"+data["fast_num"]+"</span>");
                }
                if(data["imDo_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/purchase/index']").append("<span class='badge'>"+data["imDo_num"]+"</span>");
                }
                if(data["take_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/order/index']").append("<span class='badge'>"+data["take_num"]+"</span>");
                }
                if(data["deli_num"] != 0){
                    $("#navbar-collapse a[href='"+str+"/index.php/delivery/index']").append("<span class='badge'>"+data["deli_num"]+"</span>");
                }
                if(data["goods_num"] != 0){
                    $message = $("#navbar-collapse a[href='"+str+"/index.php/technician/index']");
                    $message.append("<span class='badge'>"+data["goods_num"]+"</span>");
                    $message.parents("li.dropdown").find("a:first>span:first").before("<span class='badge'>"+data["goods_num"]+"</span>");
                }
                if(data["fast_num"] != 0 || data["imDo_num"] != 0){
                    $message = $("#navbar-collapse a[href='"+str+"/index.php/purchase/index']");
                    $message.parents("li.dropdown").find("a:first>span:first").before("<span class='badge'>"+(parseInt(data["fast_num"],10)+parseInt(data["imDo_num"],10))+"</span>");
                }
                if(data["take_num"] != 0 || data["deli_num"] != 0){
                    $message = $("#navbar-collapse a[href='"+str+"/index.php/order/index']");
                    $message.parents("li.dropdown").find("a:first>span:first").before("<span class='badge'>"+(parseInt(data["take_num"],10)+parseInt(data["deli_num"],10))+"</span>");
                }
            }
        });
    }
}
//<kbd>cd</kbd>