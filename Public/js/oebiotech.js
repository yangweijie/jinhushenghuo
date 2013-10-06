/**
 * 欧易生物前台页面效果
 */

$(function(){
	/* 主导航鼠标悬浮出现下拉子菜单 */
	var mainNav = $(".main-nav").find(".nav");
	mainNav.children("li").each(function(){
		$(this).hover(function(){
				var subNav = $(this).find(".subnav");
			subNav.removeClass("hidden");
		},function(){
			var subNav = $(this).find(".subnav");
			subNav.addClass("hidden");
		})
	});


	var initTop;
	var mainNavTop = $(".main-nav").offset().top;
	$(window).scroll(function(){
		/* 主导航固定定位 */
		var scrTop = $(window).scrollTop();
		if( scrTop > mainNavTop) {
			initTop = scrTop;
			$(".main-nav").css({
				"position" : "fixed",
				"top" : 0,
				"z-index" : 1000,
				"width" : "100%"
			});
		} else if( scrTop < initTop ){
			initTop = scrTop;
			if( scrTop < mainNavTop ) {
				$(".main-nav").css("position", "static");
			}
		}

		//页面滚动距离小于100px是隐藏“返回顶部”
		// if( scrTop > 100 ) {
		// 	$("#gototop").fadeIn();
		// } else {
		// 	$("#gototop").fadeOut();
		// }
	});

	/* 返回顶部 */
	$("#gototop").click(function(){
		$("html,body").animate({scrollTop: 0}, 200);
	});

	/* 特殊页列表 */
	$(".procon-content").find("li").each(function(){
		$(this).click(function(){
			$(this).addClass("active").siblings().removeClass("active");
		});
	})
})

var slide_index = 0;
/* banner幻灯片 */
$(function(){

	var timeout;
	var picContainer = $(".slide-container");
	var picBox = picContainer.children(".slide-pic-wrap");
	var picItem = picBox.find("li");

	//动态添加数字列表
	var numlist = "<ul class=\"slide-numlist nav\">";
	for (var i = 1; i <= picItem.length; i++) {
		numlist += "<li><a href=\"javascript:;\">" + i + "</a></li>";
	}
	numlist += "</ul>"
	picContainer.append(numlist);
	var numItem = $(".slide-numlist").children("li");
	showPic(0);
	var slide = setInterval(function(){
		slide_index++;
		if(slide_index == picItem.length){
			slide_index = 0
		}
		showPic(slide_index)
	},2000);

	//自动播放
	picItem.hover(function(){
		clearInterval(slide);
	},function(){
		slide = setInterval(function(){
			// console.log(index);
			slide_index++;
			if(slide_index == picItem.length)
				slide_index = 0
			showPic(slide_index)
		},1000);
	})

	function showPic(index) {
		numItem.removeClass("current");
		numItem.eq(index).addClass("current");
		picItem.eq(index).stop(true,false).
			animate({"opacity" : 1, "z-index" : 1},200).
			siblings().animate({"opacity" : 0, "z-index" : 0},200);
	}

	//添加数字列表鼠标悬浮事件
	numItem.mouseover(function(){
		clearInterval(slide);
		var index = $(this).index();
		showPic(index);
		slide_index = index;
	})
})
