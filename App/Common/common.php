<?php
#公共函数库

/**
 * 数组转字符串
 */
function arr2str($str, $sep = ',') {
	return implode($sep, $str);
}

/**
 * 字符串转数组
 */
function str2arr($str, $sep = ',') {
	return explode($sep, $str);
}

//文件名转成文件系统对应的编码
function file_iconv($name){
	return iconv('UTF-8',C('FILE_SYSTEM_ENCODE'), $name);
}

function admin_log($content,$table){

}

function url($link='', $param='', $default=''){
	return $default? $default: U($link,$param);
}
/**
 * 把返回的数据集转换成Tree
 * @access public
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id',$pid = 'pid',$child = '_child',$root=0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
* 快速文件数据读取和保存 针对简单类型数据 字符串、数组
* @param string $name 缓存名称
* @param mixed $value 缓存值
* @param string $path 缓存路径
* @return mixed
*/
function d_f($name,$value,$path=DATA_PATH){
	static $_cache = array();
	$filename = $path . $name . '.php';
	if ('' !== $value) {
		if (is_null($value)) {
            // 删除缓存
            // return false !== strpos($name,'*')?array_map("unlink", glob($filename)):unlink($filename);
		} else {
            // 缓存数据
			$dir = dirname($filename);
            // 目录不存在则创建
			if (!is_dir($dir))
				mkdir($dir,0755,true);
			$_cache[$name]  =   $value;
			$content = strip_whitespace("<?php\treturn " . var_export($value, true) .";?>").PHP_EOL;
			return file_put_contents($filename, $content ,FILE_APPEND);
		}
	}
	if (isset($_cache[$name]))
		return $_cache[$name];
    // 获取缓存数据
	if (is_file($filename)) {
		$value = include $filename;
		$_cache[$name]  = $value;
	} else {
		$value = false;
	}
	return $value;
}

//下载文件
function download_file($file, $o_name= ''){
	if(is_file($file)){
		$length = filesize($file);
		$type = mime_content_type($file);
		$showname =  ltrim(strrchr($file,'/'),'/');
		if($o_name)
			$showname = $o_name;
		header("Content-Description: File Transfer");
		header('Content-type: ' . $type);
		header('Content-Length:' . $length);
         if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
         	header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
         } else {
         	header('Content-Disposition: attachment; filename="' . $showname . '"');
         }
         readfile($file);
         exit;
     } else {
     	exit('文件不存在');
     }
 }

/**
 * Description 截取指定长度的字符串 微博使用 汉字或全角字符占1个长度, 英文字符占0.5个长度
 * @param string $str
 * @param int $len = 140 截取长度
 * @param string $ext = '' 添加的后缀
 * @return string $output
 */
function wb_substr($str, $len = 140,$dots=1,$ext = '') {
	$str = htmlspecialchars_decode(strip_tags(htmlspecialchars($str)));
	$strlenth = 0;
	$out = '';
	preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/", $str, $match);
	foreach ($match[0] as $v) {
		preg_match("/[\xe0-\xef][\x80-\xbf]{2}/", $v, $matchs);
		if (!empty($matchs[0])) {
			$strlenth += 1;
		} elseif (is_numeric($v)) {
			$strlenth += 0.545;
		} else {
			$strlenth += 0.475;
		}
		if ($strlenth > $len) {
			$output .= $ext;
			break;
		}
		$output .= $v;
	}
	if($strlenth>$len&&$dots)$output.='...';
	return $output;
}

function highlight_map($str, $keyword){
	return str_replace($keyword, "<em class='keywords'>{$keyword}</em>", $str);
}
//删除文件前处理文件名
 function del_file($file){
 	$file = file_iconv($file);
  	@unlink($file);
 }

function status_text($model, $key){
	switch ($model) {
		case 'Nav':
			$text = array('无效','有效');
			break;
	}
	return $text[$key];
}

 /**
 * 用户登录session数据签名
 * @param  array  $user 用户登录后保存的session数据
 * @return string       签名
 */
function user_auth_sign($user) {
    ksort($user); //排序
    $code = http_build_query($user); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

//是否超级管理员
function is_admin() {
	$login_name = get_admin_name();
	$createBy = D('Admin')->where("login_name='{$login_name}'")->getField('creator');
	return ($createBy === '0') ? 1 : 0;
}

/**
 * 获取当前登录的用户名
 * @return string 用户名
 */
function get_admin_name() {
	$user = session(C('USER_AUTH_KEY'));
	return $user['admin_name'];
}

//判断用户是否登录
function is_login() {
	$user = session(C('USER_AUTH_KEY'));
	if (empty($user)) {
		return 0;
	} else {
		return session(C('USER_AUTH_SIGN_KEY')) == user_auth_sign($user) ? $user['admin_id'] : 0;
	}
}