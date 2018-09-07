//首页Banner
jQuery(document).ready(function () {
    $("#focusBar li").css("width", $(window).width());

    //设置宽度
    $(window).bind('scroll resize', function (e) {
        var _scrolltop = $(window).scrollTop();
        $(window).bind('resize', function (e) {
//          $("#focusBar li").css("width", $(window).width());
        });
//      $("#focusBar li").css("width", $(window).width());
    });
});

//切换图片
var splitSec = 10000;
var imgIndex = 0;
function changeimg() {
    var left = imgIndex * 100;
    //$(".mypng").css({left:"-"+left+"%"});
    $(".mypng li").css({"z-index":"1"});
    $(".focusL").stop().animate({ left: "-100%", opacity: "0" }, 2200);
    $(".focusR").stop().animate({ right: "-100%", opacity: "0" }, 2200);
    $(".BanTab span").removeAttr("class");
    $("#focusIndex" + imgIndex).css({"z-index":"2"});
    $("#focusIndex" + imgIndex + " .focusL").stop().animate({ left: "0px",opacity:"1" }, 2000);
    $("#focusIndex" + imgIndex + " .focusR").stop().animate({ right: "0px", opacity: "1" }, 2000);
    $(".BanTab span").eq(imgIndex).addClass("aon");
    if (imgIndex >= 4)
        imgIndex = 0;
    else
        imgIndex++;
}

//自动切换
var time = setInterval("changeimg()", splitSec);


$(".BanTab span").click(function () {
    $(this).addClass("aon").siblings("span").removeClass("aon");
    imgIndex = $(this).index();
    var left = imgIndex * 100;
    $(".mypng").stop().animate({
        left: "-" + left + "%"
    }, 2000);
    $(".focusL").stop().animate({ left: "-100%" }, 2000);
    $(".focusR").stop().animate({ right: "-100%" }, 2450);
    $(".BanTab span").removeAttr("class");
    $("#focusIndex" + imgIndex + " .focusL").stop().animate({ left: "0px" }, 2000);
    $("#focusIndex" + imgIndex + " .focusR").stop().animate({ right: "0px" }, 2450);
    $(".BanTab span").eq(imgIndex).addClass("aon");
    clearInterval(time);
    time=setInterval("changeimg()", splitSec);
})
