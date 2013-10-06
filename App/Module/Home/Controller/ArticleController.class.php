<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */

class ArticleController extends CommonController {

	public function index(){
		$title = trim(I('title'));
		if($title)
			$map['title'] = array('like',"%{$title}%");
		$this->_list(array('source'=>'Article','map'=>$map));
	}

	public function add(){
		$nav_tree = D('Nav')->where('status=1')->select();
		$nav_tree = D('Tree')->toFormatTree($nav_tree);
		$this->assign('nav_tree',$nav_tree);
		$this->display();
	}

	public function edit(){
		$_GET['model'] = 'Article';
		$nav_tree = D('Nav')->where('status=1')->select();
		$nav_tree = D('Tree')->toFormatTree($nav_tree);
		$this->assign('nav_tree',$nav_tree);
		$this->_edit();
	}

	public function editbleGetNav(){
		$this->editbleAjaxGet('Nav','id as value,c_name as text');
	}
}