<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-10-26 09:30:32
 * @version $Id$
 */

class AdController extends CommonController {

	public function index(){
		$title = trim(I('title'));
		if($title)
			$map['title'] = array('like', array('like',"%{$title}%"));
		$this->_list(array('source'=>'Ad','map'=>$map));
	}

	public function _types(){
		$option = array(
			1=>'内容',
			2=>'顶部',
			3=>'对联',
			4=>'悬浮',
		);
		$this->assign('types', $option);
	}

	public function add(){
		session('uploaded', null);
		$this->_types();
		$this->display('update');
	}

	//上传附件
	public function adUpload(){
		parent::ajaxUpload(
			array(
				'savePath'  => './Uploads/ad/',
				'saveRule'  => 'uniqid',
				// 'allowExts' => array('xls')
			)
		);
	}

	public function after_upload($controller, $uploaded, &$result){
		$src = str_replace('./', '/', $uploaded['file']);
		$result['content'] = <<<pic
<li>
	<img src="{$src}" width=100 />
	<a href="javascript:;" title="{$src}" class="del">删除</a>
	<input type="hidden" name="files[]" value="{$src} />
</li>
pic;
	}

	public function insert(){
		$file = I('post.files');
		if(!count($file))
			$this->error('尚未上传图片');
		$_POST = $file;
		parent::insert();
	}

}