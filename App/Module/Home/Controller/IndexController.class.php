<?php

class IndexController extends Action {

    public function _initialize(){
        $nav_tree = D('Nav')->where('status=1')->field("id,c_name,pid,e_name,ctime,link,order")->order('`order` asc')->select();
        $nav_tree = list_to_tree($nav_tree,$pk='id',$pid='pid',$child='_child',$root=1);
        $this->assign('nav',$nav_tree);
    }

    protected function _list($params){
        extract($params);
        if(!isset($params['source'])){
            $this->error('错误的数据');
        }else{
            if(!$listRows)
                $listRows = I('listRows');
            $params = array(
                'source'=>$source,
                'map'=>$map,
                'parameter'=>$parameter,
                'listRows'=>$listRows,
                'listvar'=>$list,
                'order'=>$order,
            );
            $this->page($params);
            $this->display($tpl);
        }
    }

    /**
     * 分页函数 支持数据库查询分页和数组分页 数据库分页直接传数据表名称
     * @access public
     * @param mixed  $source 分页用数据源，可以是数组或数据表
     * @param array  $map数据源为数据表的时候的查询条件
     * @param string $parameter  分页跳转的参数
     * @param string $listvar    赋给模板遍历的变量名 默认list
     * @param int    $listRows  每页显示记录数 默认20
     */
    protected function page($param){
        extract($param);
        import("@.ORG.Util.Page2");
        $flag = !is_array($source);
        $listvar = $listvar ? $listvar : 'list';
        $listRows = $listRows? $listRows : 10;
        //总记录数
        if ($flag){//字符串
            $count = $count? $count : '*';
            $totalRows = D($source)->where($map)->count($count);
        }else{
            $totalRows = ($source) ? count($source) : 1;
        }
        //创建分页对象
        $p = new Page2($totalRows, $listRows, $parameter);
        //抽取数据
        if ($flag) {
            $voList = D($source)->where($map)->bind(true)->group($group)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
            // d_f('sql',$voList);
        } else {
            $voList = array_slice($source, $p->firstRow, $p->listRows);
        }
        $pages = array(
          'theme'=>'<div class="pager unstyled">%upPage% %linkPage% %downPage%</div>',
        );//要ajax分页配置PAGE中必须theme带%ajax%，其他字符串替换统一在配置文件中设置，
        foreach ($pages as $key => $value) {
            $p->setConfig($key, $value); // 'theme'=>'%upPage% %linkPage% %downPage% %ajax%'; 要带 %ajax%
        }
        //分页显示
        $page = $p->totalPages>1? $p->show():'';
        //模板赋值
        d_f('sql',$listvar);
        d_f('sql',$voList);
        $this->assign($listvar, $voList);
        $this->assign("page", $page);
        $this->assign('count',$totalRows);
        $varPage = C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        $this->assign('start_row', ($p->nowPage- 1) * $listRows + 1);
        $this->assign('end_row', ($p->nowPage- 1) * $listRows + count($voList));
        $this->assign('currentPage',$p->nowPage);
        $this->assign('listRows',$listRows);
        return $voList;
    }


    //首页
    public function index(){
        $this->assign('banner', D('Banner')->order('`order` asc')->select());
        #欧意动态
        $dynamic_navid = D('Nav')->where("c_name='欧易动态'")->getField('id');
        $this->assign('dynamic_navid', $dynamic_navid);
        $dynamic = D('Article')->where("nav_id={$dynamic_navid}")->limit(5)->select();
        $this->assign('dynamic', $dynamic);
        #行业资讯
        $dynamic_navid2 = D('Nav')->where("c_name='欧易动态'")->getField('id');
        $this->assign('dynamic_navid2', $dynamic_navid2);
        $dynamic2 = D('Article')->where("nav_id={$dynamic_navid2}")->limit(5)->select();
        $this->assign('dynamic2', $dynamic);
        #资料下载
        $down = D('attach')->limit(5)->select();
        $this->assign('down', $down);
        $this->display();
    }

    //文章详情页
    public function detail($id){
        $record = M('Article')->find($id);
        $this->assign('record', $record);
        $comment = $this->_list(array(
            'source'=>'Message',
            'map'=>"article_id = {$id}",
            'order'=>'id desc',
            'list'=>'comments'
        ));
    }

    //特殊分类页
    public function video($id = 1){
        $data = M('Video')->find($id);
        $this->assign('data', $data);
        $this->display();
    }

    //搜索
    public function search($keyword){
        G('begin');
        $map['title|content'] = array('like',"%{$keyword}%");
        $time_limit = I('time_limit');
        if($time_limit)
            $map['ctime'] = array('egt', strtotime($time_limit));
        G('end');
        $this->assign('cost_time', G('begin', 'end',3));
        $this->_list(array('source'=>'Article','map'=>$map));
    }

    //发布资讯
    public function post(){
        if(IS_POST){
            $article = D('Article');
            $data = $article->create($_POST);
            if($data){
                $result = $article->add($data);
                if($result)
                    $this->success('发布成功',U('/'));
                else
                    $this->error('发布失败');
            }else{
                $this->error($article->getError());
            }
        }else{
            $this->display();
        }
    }

    //发布评论
    public function addComment(){
        if(IS_POST){
            $message = D('Message');
            $data = $message->create($_POST);
            if($data){
                $result = $message->add($data);
                if($result)
                    $this->success('评论成功');
                else
                    $this->error('评论失败');
            }else{
                $this->error($message->getError());
            }
        }else{
            $this->error('错误的请求');
        }
    }

    //kindeditor文件管理
    public function fileManagerJson(){
        vendor('JSON');

        $php_url = dirname($_SERVER['PHP_SELF']) . '/';

        //根目录路径，可以指定绝对路径，比如 /var/www/attached/
        $root_path = './Uploads/article/';
        //根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
        $root_url = '/Uploads/article/';
        //图片扩展名
        $ext_arr = array('gif', 'jpg', 'jpeg', 'png', 'bmp');

        //目录名
        $dir_name = empty($_GET['dir']) ? '' : trim($_GET['dir']);
        if (!in_array($dir_name, array('', 'image', 'flash', 'media', 'file'))) {
            echo "Invalid Directory name.";
            exit;
        }
        if ($dir_name !== '') {
            $root_path .= $dir_name . "/";
            $root_url .= $dir_name . "/";
            if (!file_exists($root_path)) {
                mkdir($root_path);
            }
        }

        //根据path参数，设置各路径和URL
        if (empty($_GET['path'])) {
            $current_path = realpath($root_path) . '/';
            $current_url = $root_url;
            $current_dir_path = '';
            $moveup_dir_path = '';
        } else {
            $current_path = realpath($root_path) . '/' . $_GET['path'];
            $current_url = $root_url . $_GET['path'];
            $current_dir_path = $_GET['path'];
            $moveup_dir_path = preg_replace('/(.*?)[^\/]+\/$/', '$1', $current_dir_path);
        }
        echo realpath($root_path);
        //排序形式，name or size or type
        $order = empty($_GET['order']) ? 'name' : strtolower($_GET['order']);

        //不允许使用..移动到上一级目录
        if (preg_match('/\.\./', $current_path)) {
            echo 'Access is not allowed.';
            exit;
        }
        //最后一个字符不是/
        if (!preg_match('/\/$/', $current_path)) {
            echo 'Parameter is not valid.';
            exit;
        }
        //目录不存在或不是目录
        if (!file_exists($current_path) || !is_dir($current_path)) {
            echo 'Directory does not exist.';
            exit;
        }

        //遍历目录取得文件信息
        $file_list = array();
        if ($handle = opendir($current_path)) {
            $i = 0;
            while (false !== ($filename = readdir($handle))) {
                if ($filename{0} == '.') continue;
                $file = $current_path . $filename;
                if (is_dir($file)) {
                    $file_list[$i]['is_dir'] = true; //是否文件夹
                    $file_list[$i]['has_file'] = (count(scandir($file)) > 2); //文件夹是否包含文件
                    $file_list[$i]['filesize'] = 0; //文件大小
                    $file_list[$i]['is_photo'] = false; //是否图片
                    $file_list[$i]['filetype'] = ''; //文件类别，用扩展名判断
                } else {
                    $file_list[$i]['is_dir'] = false;
                    $file_list[$i]['has_file'] = false;
                    $file_list[$i]['filesize'] = filesize($file);
                    $file_list[$i]['dir_path'] = '';
                    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $file_list[$i]['is_photo'] = in_array($file_ext, $ext_arr);
                    $file_list[$i]['filetype'] = $file_ext;
                }
                $file_list[$i]['filename'] = urlencode($filename); //文件名，包含扩展名
                $file_list[$i]['datetime'] = date('Y-m-d H:i:s', filemtime($file)); //文件最后修改时间
                $i++;
            }
            closedir($handle);
        }

        //排序
        function cmp_func($a, $b) {
            global $order;
            if ($a['is_dir'] && !$b['is_dir']) {
                return -1;
            } else if (!$a['is_dir'] && $b['is_dir']) {
                return 1;
            } else {
                if ($order == 'size') {
                    if ($a['filesize'] > $b['filesize']) {
                        return 1;
                    } else if ($a['filesize'] < $b['filesize']) {
                        return -1;
                    } else {
                        return 0;
                    }
                } else if ($order == 'type') {
                    return strcmp($a['filetype'], $b['filetype']);
                } else {
                    return strcmp($a['filename'], $b['filename']);
                }
            }
        }
        usort($file_list, 'cmp_func');

        $result = array();
        //相对于根目录的上一级目录
        $result['moveup_dir_path'] = $moveup_dir_path;
        //相对于根目录的当前目录
        $result['current_dir_path'] = $current_dir_path;
        //当前目录的URL
        $result['current_url'] = $current_url;
        //文件数
        $result['total_count'] = count($file_list);
        //文件列表数组
        $result['file_list'] = $file_list;

        //输出JSON字符串
        header('Content-type: application/json; charset=UTF-8');
        $json = new Services_JSON();
        echo $json->encode($result);
    }

    //kindeditor文件上传
    public function uploadJson(){
        require_once 'JSON.php';
        $php_path = dirname(__FILE__) . '/';
        $php_url = dirname($_SERVER['PHP_SELF']) . '/';

        //文件保存目录路径
        $save_path = './Uploads/article/';
        //文件保存目录URL
        $save_url = './Uploads/article/';
        //定义允许上传的文件扩展名
        $ext_arr = array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2'),
        );
        //最大文件大小
        $max_size = 1000000;

        $save_path = realpath($save_path) . '/';

        //PHP上传失败
        if (!empty($_FILES['imgFile']['error'])) {
            switch($_FILES['imgFile']['error']){
                case '1':
                    $error = '超过php.ini允许的大小。';
                    break;
                case '2':
                    $error = '超过表单允许的大小。';
                    break;
                case '3':
                    $error = '图片只有部分被上传。';
                    break;
                case '4':
                    $error = '请选择图片。';
                    break;
                case '6':
                    $error = '找不到临时目录。';
                    break;
                case '7':
                    $error = '写文件到硬盘出错。';
                    break;
                case '8':
                    $error = 'File upload stopped by extension。';
                    break;
                case '999':
                default:
                    $error = '未知错误。';
            }
            alert($error);
        }

        //有上传文件时
        if (empty($_FILES) === false) {
            //原文件名
            $file_name = $_FILES['imgFile']['name'];
            //服务器上临时文件名
            $tmp_name = $_FILES['imgFile']['tmp_name'];
            //文件大小
            $file_size = $_FILES['imgFile']['size'];
            //检查文件名
            if (!$file_name) {
                alert("请选择文件。");
            }
            //检查目录
            if (@is_dir($save_path) === false) {
                alert("上传目录不存在。");
            }
            //检查目录写权限
            if (@is_writable($save_path) === false) {
                alert("上传目录没有写权限。");
            }
            //检查是否已上传
            if (@is_uploaded_file($tmp_name) === false) {
                alert("上传失败。");
            }
            //检查文件大小
            if ($file_size > $max_size) {
                alert("上传文件大小超过限制。");
            }
            //检查目录名
            $dir_name = empty($_GET['dir']) ? 'image' : trim($_GET['dir']);
            if (empty($ext_arr[$dir_name])) {
                alert("目录名不正确。");
            }
            //获得文件扩展名
            $temp_arr = explode(".", $file_name);
            $file_ext = array_pop($temp_arr);
            $file_ext = trim($file_ext);
            $file_ext = strtolower($file_ext);
            //检查扩展名
            if (in_array($file_ext, $ext_arr[$dir_name]) === false) {
                alert("上传文件扩展名是不允许的扩展名。\n只允许" . implode(",", $ext_arr[$dir_name]) . "格式。");
            }
            //创建文件夹
            if ($dir_name !== '') {
                $save_path .= $dir_name . "/";
                $save_url .= $dir_name . "/";
                if (!file_exists($save_path)) {
                    mkdir($save_path);
                }
            }
            $ymd = date("Ymd");
            $save_path .= $ymd . "/";
            $save_url .= $ymd . "/";
            if (!file_exists($save_path)) {
                mkdir($save_path);
            }
            //新文件名
            $new_file_name = date("YmdHis") . '_' . rand(10000, 99999) . '.' . $file_ext;
            //移动文件
            $file_path = $save_path . $new_file_name;
            if (move_uploaded_file($tmp_name, $file_path) === false) {
                alert("上传文件失败。");
            }
            @chmod($file_path, 0644);
            $file_url = $save_url . $new_file_name;
            $file_url = str_replace('./', '/', $file_url);

            header('Content-type: text/html; charset=UTF-8');
            $json = new Services_JSON();
            echo $json->encode(array('error' => 0, 'url' => $file_url));
            exit;
        }

        function alert($msg) {
            header('Content-type: text/html; charset=UTF-8');
            $json = new Services_JSON();
            echo $json->encode(array('error' => 1, 'message' => $msg));
            exit;
        }
    }


    public function xhr(){
        exit(json_encode(array('1'=>'我是跨域的请求')));
    }
}