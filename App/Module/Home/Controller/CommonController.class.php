<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie@topthink.net <www.thinkphp.cn>
// +----------------------------------------------------------------------

class CommonController extends Action {

	//初始化方法
    protected function _initialize() {
        is_login() || $this->redirect('System/login');
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

    protected function _edit(){
        $map = array(D($_GET['model'])->getPk()=>urldecode($_GET['id']));
        $record = D($_GET['model'])->where($map)->find();
        if($record){
            $this->assign('record',$record);
            if(IS_AJAX){
                exit($this->fetch());
            }else{
               $this->display();
            }
        }else{
           exit('错误的数据');
        }
    }

    //editble ajax更新方法
    public function ajaxUpdate(){
        $_POST = array(
            'id'=>I('pk'),
            I('name')=>I('value')
        );
        $this->update();
    }

    public function editbleAjaxGet($model,$field){
        $list = D($model)->field($field)->select();
        exit(json_encode($list));
    }

    //kindeditor文件管理
    public function fileManagerJson(){
        vendor('JSON');

        $php_url = dirname($_SERVER['PHP_SELF']) . '/';

        //根目录路径，可以指定绝对路径，比如 /var/www/attached/
        $root_path = './Uploads/';
        //根目录URL，可以指定绝对路径，比如 http://www.yoursite.com/attached/
        $root_url = '/Uploads/';
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
        import("@.ORG.Util.Page");
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
        $p = new Page($totalRows, $listRows, $parameter);
        //抽取数据
        if ($flag) {
            $voList = D($source)->where($map)->bind(true)->group($group)->order($order)->limit($p->firstRow . ',' . $p->listRows)->select();
            // d_f('sql',D($source)->_sql());
        } else {
            $voList = array_slice($source, $p->firstRow, $p->listRows);
        }
        $pages = array(
          'theme'=>'<div class="page">%upPage% %linkPage% %downPage%</div>',
        );//要ajax分页配置PAGE中必须theme带%ajax%，其他字符串替换统一在配置文件中设置，
        foreach ($pages as $key => $value) {
            $p->setConfig($key, $value); // 'theme'=>'%upPage% %linkPage% %downPage% %ajax%'; 要带 %ajax%
        }
        //分页显示
        $page = $p->totalPages>1? $p->show():'';
        //模板赋值
        $this->assign($listvar, $voList);
        $this->assign("page", $page);
        $this->assign('count',$totalRows);
        $varPage      =   C('VAR_PAGE') ? C('VAR_PAGE') : 'p' ;
        $this->assign('currentPage',!empty($_GET[$varPage])?intval($_GET[$varPage]):1);
        $this->assign('listRows',$listRows);
        return $voList;
    }

    /**
     * 公共插入方法,要插入的数据模型为当前模块名，如果提价模型，则为提交的模型
     */
    public function insert() {
        $name = empty($_POST['model']) ? CONTROLLER_NAME : $_POST['model']; //添加页面提交要操作的数据模型
        $model = D($name);
        if($data = $model->create($_POST,MODEL::MODEL_INSERT)){
            //保存当前数据对象
            $list = $model->add($data);
            if ($list !== false) { //保存成功
                $jumpUrl = empty($_POST['jumpUrl']) ? $_SESSION['returnUrl'] : $_POST['jumpUrl'];
                $success = empty($_POST['success_info']) ? '新增成功' : $_POST['success_info'];
                $this->assign('jumpUrl', $jumpUrl);
                $this->success($success, $jumpUrl);
            } else {
                $error = $model->getError();
                $error = empty($error) ? '新增失败' : $model->getError();
                $this->error($error);
            }
        }else{
            $error = $model->getError();
            $this->error($error);
        }
    }

    /**
     * 切换状态
     */
    public function status(){
        $_POST = $_GET;
        C('update_status',true);
        $this->update();
        $this->after_status($_POST['model'],$_POST['statusFlag']);
    }

    /**
     * 公共更新数据模型 默认模型名称为当前模块名，如果提交模型名，则为post接收的模型
     */
    public function update() {
        $name = empty($_POST['model']) ? CONTROLLER_NAME : $_POST['model'];
		// d_f('out',$name);
        $model = D($name);
        if($data = $model->create($_POST)){
            $o_data = $model->find($data[$model->getPk()]);
            $result = $model->save($data);
            //更新之后操作
            if (false !== $result) {
                //成功提示
                $jumpUrl = empty($_POST['jumpUrl']) ? $_SESSION['returnUrl'] : $_POST['jumpUrl'];
                $this->success('更新成功', $jumpUrl);
            } else {
                //错误提示
                $error = $model->getDbError();
                $error=empty($error)?'更新失败':$error;
                $this->error($error);
            }
        }else{
            $error = $model->getDbError();
            $this->error($error);
        }
    }

    /**
     * 公共删除一入口
     * 接受参数value类型为 模型-id值,其中多个id值用,隔开
     */
    public function delete() {
        $name = empty($_POST['model']) ? CONTROLLER_NAME : $_POST['model'];
        $id = I('id');
        $model = D($name); //定义模型
        $pk = $model->getPk();
        $map[$pk] = array("IN", $id);
        $data['title'] = "Id：".$value[1];
        $data['model'] = $name;
        //判断删除之前是否存在检查删除操作
        if (method_exists($this, '_check_delete'))
            $this->_check_delete($id, $name);
        //删除操作
        $model->startTrans();
        if ('recycle' == $action) {
            $result = $model->where($map)->setField('status', -1);
            $info = '放入回收站中';
        } elseif ('restore' == $action) {
            $result = $model->where($map)->setField('status', 1);
            $info = '还原成功';
        } else {
            $data = $model->where($map)->find();

            $result = $model->where($map)->delete();

            //删除之后操作
            $result = $result && $this->after_delete($id, $name, $data);
            $info = '删除成功';
        }
        if ($result) {
            $model->commit();
            $this->success($info);
        } else {
            $model->rollback();
            $this->error('删除失败');
        }
    }

    //清空数据
    public function deleteAll(){
        $model = $_GET['model'];
        if(empty($model)){
            $this->error('操作模型未知');
        }else{
            $map['id']=array('neq',0);
            $result = M($model)->where($map)->delete();
            if($result){
                $this->success('删除成功');
            }else{
                $this->error('删除失败');
            }
        }
    }

    //排序传值
    public function sort() {
        $array = split("-", $_GET["value"]);
        $id = split(",", $array[1]);
        if (count($id) > 1) {
            $map['id'] = array("in", $id);
            $order = '`order` asc,id desc';
        } else {
            $limit = 20;
            $order = 'id desc';
        }
        $model = D($array[0]);
        $list = $model->where($map)->order($order)->limit($limit)->select();
        $this->model = $array[0];
        $this->id = $array[1];
        $this->assign("list", $list);
        $this->display();
    }

    //数据库排序的update
    public function updateSort() {
        $model = D($_POST['model']);
        $id = explode(',', $_POST['id']);
        if ($_POST['clear'] == '清空排序') {
            $map['id'] = array('in', $id);
            $model->where($map)->setField('order', null);
            $this->success('排序已清空!');
        } else {
            foreach ($id as $key => $vo) {
                $map['id'] = $vo;
                $data['order'] = $key + 1;
                $model->where($map)->save($data);
            }
            $this->success('排序成功!');
        }
    }

    public function ajaxUpload($parms = array()){
        // if(!extension_loaded('fileinfo'))
        //     $this->error('请告知服务器管理员开启file_info扩展');
        $result = $this->upload($parms);
        if($result['flag']){
            $file = $result['files'][0];
            $this->success('上传成功','',array('content'=>$result['content']));
        }else{
            $this->error('上传失败：'.$result['error']);
        }
    }

    protected function upload($parms){
        f('ss',var_export($parms,1));
        import('@.ORG.Net.UploadFile');
        //导入上传类
        $upload = new UploadFile($parms);
        $uploaded = $upload->uploadOne($_FILES['Filedata']);
        if (!$uploaded) {
            //捕获上传异常
            $result['flag'] = 0;
            $result['error'] = $upload->getErrorMsg();
            f('rs',var_export($result['error'],1));
        } else {
            $result['files'] = $uploaded;
            $result['flag'] = 1;
            f('rs',var_export($uploaded,1));
            if(method_exists($this, 'after_upload')){
                $uploaded = $uploaded[0];
                $uploaded['file'] = $uploaded['savepath'].$uploaded['savename'];
                $this->after_upload(CONTROLLER_NAME,$uploaded,$result);
            }
        }
        d_f('rs',$result);
        return $result;
    }

    protected function after_delete($id,$name,$data = ''){
        return true;
    }

    public function after_status($name, $flag){
        return true;
    }
}
