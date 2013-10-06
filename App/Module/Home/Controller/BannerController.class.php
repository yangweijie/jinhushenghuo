<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */

class BannerController extends CommonController {

	public function index(){
		$this->_list(array('source'=>'Banner','order'=>'`order` asc'));
	}

	public function add(){
		session('uploaded', null);
		$this->display();
	}

	//上传附件
	public function attachUpload(){
		parent::ajaxUpload(
			array(
				'savePath'  => './Attach/banner/',
				'saveRule'  => 'uniqid',
				'allowExts' => array('jpg','png','gif','jpeg')
			)
		);
	}

   //排序传值
    public function sort() {
        $array = split("-", $_GET["value"]);
        $id = split(",", $array[1]);
        if (count($id) > 1) {
            $map['id'] = array("in", $id);
            $order = '`order` asc,id asc';
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

	public function after_upload($controller, $uploaded, &$result){
		session('uploaded', $uploaded);
		$uploaded['file'] = str_replace('./', '/', $uploaded['file']);
		$result['content'] = "<img id='preview' width='50%' src='{$uploaded['file']}' style='cursor:pointer'>";
	}

	public function insert(){
		$file = session('uploaded');
		if(!$file)
			$this->error('尚未上传图片');
		$_POST['src'] = str_replace('./', '/', $file['savepath'].$file['savename']);
		parent::insert();
	}
}