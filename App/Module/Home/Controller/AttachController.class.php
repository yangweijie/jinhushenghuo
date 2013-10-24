<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */

class AttachController extends CommonController {

	public function index(){
		$o_name = trim(I('o_name'));
		if($o_name)
			$map['o_name'] = array('like', array('like',"%{$o_name}%"));
		$this->_list(array('source'=>'Attach','map'=>$map));
	}

	public function add(){
		session('uploaded', null);
		$this->display();
	}

	//上传附件
	public function attachUpload(){
		parent::ajaxUpload(
			array(
				'savePath'  => './Uploads/normal/',
				'saveRule'  => 'uniqid',
				// 'allowExts' => array('xls')
			)
		);
	}

	public function after_upload($controller, $uploaded, $result){
		session('uploaded', $uploaded);
	}

	public function insert(){
		$file = session('uploaded');
		if(!$file)
			$this->error('尚未上传文件');
		$_POST = $file;
		parent::insert();
	}

}