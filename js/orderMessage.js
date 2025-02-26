$(function () {
    $("#navbar-collapse>ul>li").each(function () {
        var num = 0;
        $(this).find("ul>li>a>.badge").each(function () {
            var text = $(this).text();
            if(!isNaN(text)){
                num+=parseInt(text,10);
            }
        });
        if(num != 0){
            $(this).find("a.dropdown-toggle>.caret:first").before('<span class="badge">'+num+'</span>');
        }
    });
});