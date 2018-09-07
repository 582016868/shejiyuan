$(document).ready(function() {
	//获取地址栏中的地址
	var url = window.location.href;
	//转换成字符串
	url = url.toString();
	if(url.indexOf("?") == -1) {
		//	导航栏点击及页面跳转后对应栏目添加选中效果
		$(".navbar-nav>li>a").each(function() {
			if($($(this))[0].href == String(window.location))
				$(this).parent().addClass('active');
		});
	} else {
		var nav = new Array();
		var tab = new Array();
		//	nav[0]为 原始网址，tab[1]为传过来的值
		nav = url.split("?");
		tab = nav[1].split("=")
		$(".navbar-nav>li>a").each(function() {
			if($($(this))[0].href == nav[0])
				$(this).parent().addClass('active');
		});

		if(tab[0] == "tab") {
			$(".simplefilter>li").each(function() {
				if($($(this))[0].className == tab[1]) {
					var that = this;
					$(this).addClass('active');
					
					window.onload=dianji;
					function dianji() {
						$(that).trigger('click');
					}
					
					// setTimeout(function(){
					// 	$(that).parent().hide(500);
					// 	$("h3.w3title1").text($(that).text());
					// },250);
				}
			});	
		} else if(tab[0] == "title") {
			$(".container>div").each(function() {
				if($(this).hasClass(tab[1])) {
					$(this).siblings().hide(500);
				}
			});	
		}
	}

	// 点击 tab 装换
    $(".simplefilter>li").click(function () {
    	if (!$(this).hasClass('all')) {
//          $(this).parent().hide(500);
            $("h3.w3title1").text($(this).text());
            $('span.titile_down').text($(this).data('value'));
		}
    });
});