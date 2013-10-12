/*tooltip*/
$(function(){
	$('[rel=tooltip]').hover(function(){ 
		$('<div class="tooltip" style="display:none; top:'+($(this).offset().top+$(this).height()+5)+'px;left:'+$(this).offset().left+'px;">'+$(this).attr('title')+'<div class="arrow"></div></div>').appendTo('body').fadeIn();
		//$('body').append('<div class="tooltip" style="top:'+($(this).offset().top+$(this).height()+5)+'px;left:'+$(this).offset().left+'px;">'+$(this).attr('title')+'<div class="arrow"></div></div>');						  		
	},
	function(){
		$('.tooltip').fadeOut().remove();	
	})
	
	
	$('.naver .collapse').click(function(){
		$('.naver .module, .naver .search, .naver .sub').toggle();									  
	})
})


/*ѡ�Ч��*/
$(function(){
	$('.taber .head a').hover(function(){
		$('.taber .body').hide();
		$('.taber #'+$(this).attr('lang')).show();	
		
		$('.taber .head a').removeClass('selected');
		$(this).addClass('selected');
	})		   
})


/*ͷ�������˵�*/
$(function(){
	$('.naver-module li').hover(function(){
		$(this).addClass('selected');
	},
	function(){
		$(this).removeClass('selected');
	})
})


/*heading��Ӧʽ�û�����*/
$(function(){
/*	$('.heading').hover(function(){
		$(this).animate({'height':'+=10'},300,function(){
														  
		})							 
	},
	function(){
		$(this).animate({'height':'-=10'},300,function(){
														  
		})		
	})	*/	   
})

$(function(){
	$('a[rel=popup]').click(function(){
		
		$('body').prepend('<div id="mask"></div>').find('#mask').css({opacity:0.5,  cursor:'pointer', background:'black', position:'absolute', zIndex:999, width:'100%',  height:$(document).height()});
		
		//$('body').append('<div class="popup"><del>x</del><div class="head">����-��Դ����css���</div><div class="body">������һ����Դ���� (X)HTML/CSS ��� ,����Ŀ���Ǽ������css����ʱ�䡣���ṩһ���ɿ���css����ȥ���������Ŀ,�ܹ�������վ�Ŀ������,ͨ��������ؽ��������׼��������ÿ����վ�� ֹ����Ŀ�����������Բ��ԡ�����Խ�������һ��ģ�壬��������˴����վ��������Ҫ����Щcss�ࡣ����С��ֻ���ĸ��ļ����ѡ��ܹ�����6KB��</div><div class="foot"><a href="#" class="button blue">�ر�</a></div></div>').find('.popup').fadeIn();
		$($(this).attr('href')).fadeIn().animate({'top':'60%'});
		
	})		   
	
	/*������ֹر�,live�������ڣ�Ϊͨ��js������Ԫ������¼�*/
	$('#mask, .popup del').live('click',function(){
		$('#mask').remove();
		$(this).parent('.popup').fadeOut(); $(this).parent().parent('.popup').fadeOut();
	})
})

/*popover*/
$(function(){
	$('*[rel=popover], .popover').live('mouseover',function(){
		//alert(o) 
		e = $(this)[0];
		
		$('<div class="popover" onMouseOver="'+$('.popover').show()+'" onMouseOut="'+$('.popover').hide()+'"  style="display:none; top:'+($(this).offset().top+$(this).height()+3)+'px;left:'+$(this).offset().left+'px;">'+$(this).attr('title')+'<div class="arrow"></div></img></div>').appendTo('body').show();
							   
	})	
	
	$('*[rel=popover], .popover').live('mouseout',function(){
		$('.popover').hide().remove();						   
	})	
	
	/*$('.popover').live('mouseover',function(){
		$(this).show();										
	})
	$('.popover').live('mouseout',function(){
		$(this).hide();									   
	})*/
})





/*��ͨ����*/
$(function(){
	setTimeout(function(){
			$('.cartoon').fadeIn();				
		},1000)		   
})

/*ͷ����ʾ��*/
$(function(){
	$('.spring del').click(function(){
		$('.spring').slideUp();								
	})		   
})

/*ͷ������������ �û�����*/
$(function(){
	$('.naver input[type=text]').focus(function(){
		//$(this).animate({'width':'+=10px'},'fast')									 
	})			
})


/*�������̶�*/
$(document).ready(function(){
		
	$(window).bind('scroll',function() {
		if(Math.abs($(window).scrollTop())>300)
			{
				$('.naver.js').hide().addClass('fixed').fadeIn('slow');
			}
			else
			{
				$('.naver.js').removeClass('fixed');
			}
	});
	
});

/*�ص�����*/
$(document).ready(function(){
	
	if($.browser.msie&&($.browser.version == "6.0")&&!$.support.style){
		
	}
	else{
		$(window).bind('scroll',function() {
			if(Math.abs($(window).scrollTop())>600)
				{
					//$('.scrolltotop').fadeIn();
					if($('.scrolltotop').length <= 0){
						$('<a class="fixed scrolltotop" href="#">^</a>').appendTo('body').animate({'bottom':100},'fast');
					}
					
				}
				else
				{
					$('.scrolltotop').animate({'bottom':-100},'fast',function(){
						$(this).remove();														
					})
				}
		});	
	}
	
});


/*�õ�Ƭ*/
$(function(){
		setTimeout(function(){
			$('.slider .item:first').fadeIn(); $('.slider').css('background-image','none');
		},600);
		
		$.extend({
			autoSlider:function(){
				
				/*if($('.slider .item.selected').next().size()==0){
					$('.slider .item.selected').removeClass('selected').parent().find('.item:first').addClass('selected');
				}
				else{
					$('.slider .item.selected').removeClass('selected').next().addClass('selected');
				}*/
				$('.slider .item:first').animate({'opacity':0},200,function(){
						$(this).css('opacity',1).hide().appendTo($(this).parent());
						$('.slider .item:first').fadeIn();
				})
			}
		})
		// �����ظ����ã�����jQuery�ķ���һ��Ҫ�������д�����壬�������ﲻ����Ч
		setInterval("$.autoSlider()",10000);

     $('.slider .prev').click(function(){
		
			/*if($('.slider .item.selected').next().size()==0){
					$('.slider .item.selected').removeClass('selected').parent().find('.item:first').addClass('selected');
				}
				else{
					$('.slider .item.selected').removeClass('selected').next().addClass('selected');
				}*/
				$('.slider .item:first').animate({'opacity':0},200,function(){
						$(this).css('opacity',1).hide();
						$('.slider .item:last').prependTo($(this).parent()).fadeIn();
				})
		},
		function(){});
		
		$('.slider .next').click(function(){
		
			$('.slider .item:first').animate({'opacity':0},200,function(){
						$(this).css('opacity',1).hide().appendTo($(this).parent());
						$('.slider .item:first').fadeIn();
				})
		},
		function(){})
	})


/*���й��� singlerolling */
$(function(){
		
		$.extend({
			singlerolling:function(){
				$('.singlerolling ul').animate({'marginTop':-22},function(){
					$(this).css('marginTop',0).find('li:first').appendTo($(this));
				});
			}
		})
		// �����ظ����ã�����jQuery�ķ���һ��Ҫ�������д�����壬�������ﲻ����Ч
		setInterval("$.singlerolling()",3000);
	})


// ����prettify��ɫ���

// Load the stylesheet that we're demoing.
/*var script = document.createElement('script');
script.src = 'assets/js/prettify.js';
document.getElementsByTagName('head')[0].appendChild(script);

var link = document.createElement('link');
link.rel = 'stylesheet';
link.type = 'text/css';
link.href = 'assets/css/prettify.css';
document.getElementsByTagName('head')[0].appendChild(link);
  

$(function(){
  // ����prettify��ɫ���
  $('pre').addClass('prettyprint linenums');
  prettyPrint();
})*/