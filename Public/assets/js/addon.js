/**
 * ============================================================================
 * 版权所有: 渴切-开源中文css框架
 * 网站地址: http://code.google.com/p/keqie/
 * ----------------------------------------------------------------------------
 * $codex 1. id和class命名采用该版块的英文单词或组合命名，并第一个单词小写，第二个单词首个字母大写，如:thinkCss（最新产品/think+Css)
 * $codex 2. CSS样式表各区块用注释说明
 * $codex 3. 尽量使用英文命名原则
 * $codex 4. 尽量不加中杠和下划线
 * $codex 5. 尽量不缩写，除非一看就明白的单词
 * $Author: tomcat 
 * $mailto: <qietu@keqie.com>
 * $hack :ie 6 _  \ ie7 *+ \  ie6,ie7 *  \  ie7,firefox !important ie9, \9
*/

/* ------------------------------------
	常用搜索效果 
	$ Ahour: aming
	$ mailto: 137992916@qq.com
	$ exaple: searchText(this,'请输入关键字');
------------------------------------------ */
function searchText(obj,textValue){
	if(obj.value == textValue){
		obj.value = '';
	}
	obj.onblur = function(){
		if(obj.value == '')	{
			obj.value = textValue;	
		}
	}
}

/*-------------------------------------------
	 $ 图片预加载
  	 $ comfrom: dreamweaver编辑器
     $example: simplePreload( '01.gif', '02.gif' ); 
---------------------------------*/
function simplePreload()
{ 
  var args = simplePreload.arguments;
  document.imageArray = new Array(args.length);

  for(var i=0; i<args.length; i++)
  {
    document.imageArray[i] = new Image;
    document.imageArray[i].src = args[i];
  }

}

/* ----------------------------------------
	加入收藏
	$comefrom : 互联网
	$example : <a href="javascript:void(0);" target="_self" onClick="javascript:AddFavorite('http://www.webjx.com/','网页教学网')">加入收藏</a>
--------------------------------- */
function AddFavorite(sURL, sTitle)
{
    try { window.external.addFavorite(sURL, sTitle); }
    catch (e)
    {
        try { window.sidebar.addPanel(sTitle, sURL, ""); }
        catch (e) {
            alert("加入收藏失败，请使用Ctrl+D进行添加");
        }
    }
}


/* --------------------------------------
	设为首页
	$example : <a href="javascript:void(0)" onclick="setHomepage()">设为首页</a>
-------------------------------- */
function SetHome(obj,vrl){
	try{ obj.style.behavior='url(#default#homepage)';obj.setHomePage(vrl); }
	catch(e){
		if(window.netscape) {
			try { netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");  }  
			catch (e) { 
				alert("此操作被浏览器拒绝！\n请在浏览器地址栏输入“about:config”并回车\n然后将[signed.applets.codebase_principal_support]设置为'true'");
			}
		var prefs = Components.classes['@mozilla.org/preferences-service;1'].getService(Components.interfaces.nsIPrefBranch);
		prefs.setCharPref('browser.startup.homepage',vrl);
		}
	}
}




//双击鼠标滚动屏幕的代码
var currentpos,timer;
function initialize()
{
timer=setInterval ("scrollwindow ()",30);
}
function clearScroll()
{
clearInterval(timer);
}
function scrollwindow()
{
	currentpos=document.body.scrollTop;
	window.scroll(0,++currentpos);
	if (currentpos !=document.body.scrollTop)
	clearScroll();
}
document.onmousedown=clearScroll;
document.ondblclick=initialize ;




