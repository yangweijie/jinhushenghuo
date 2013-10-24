<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-10-22 09:30:32
 * @version $Id$
 */

class VideoController extends CommonController {

	public function index(){
		$title = trim(I('title'));
		if($title)
			$map['title'] = array('like', array('like',"%{$o_name}%"));
		$this->_list(array('source'=>'Video','map'=>$map));
	}

	public function add(){
		session('uploaded', null);
		$this->display();
	}

	//上传视频文件
	public function attachUpload(){
		parent::ajaxUpload(
			array(
				'savePath'  => './Uploads/video/',
				'saveRule'  => 'uniqid',
				'allowExts' => array('mp4')
			)
		);
	}

	//上传封面
	public function coverUpload(){
		parent::ajaxUpload(
			array(
				'savePath'  => './Uploads/cover/',
				'saveRule'  => 'uniqid',
				'allowExts' => array('jpg','jpeg','png','gif')
			)
		);
	}

	public function after_upload($controller, $uploaded, &$result){
		switch ($controller) {
			case 'coverUpload':
				$result['content'] = str_replace('./', '/', $uploaded['file']);
				break;
			case 'attachUpload':
				$result['content'] = str_replace('./', '/', $uploaded['file']);
				break;
			default:
				# code...
				break;
		}
	}

	// public function insert(){
	// 	$file = session('uploaded');
	// 	if(!$file)
	// 		$this->error('尚未上传文件');
	// 	$_POST = $file;
	// 	parent::insert();
	// }

}