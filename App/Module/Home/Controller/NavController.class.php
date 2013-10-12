<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */

class NavController extends CommonController {

	public function index(){
		$pid = (int)I('pid');
		$name = trim(I('name'));
		if($pid)
			$map['pid'] = $pid;
		else if(!$name)
			$map['pid'] = 1;
		if($name)
			$map['name'] = array('like',"%{$name}%");
		$current_parent = D('Nav')->field('pid,name')->find($pid);
		$this->assign('current_parent', $current_parent);
		$this->_list(array('source'=>'Nav', 'map'=>$map,'order'=>'sort asc'));
	}

	public function add(){
		$nav_tree = D('Nav')->where('status=1')->select();
		$nav_tree = D('Tree')->toFormatTree($nav_tree);
		$this->assign('nav_tree',$nav_tree);
		$type = D('Type')->getField('id,name');
		$this->assign('type', $type);
		$this->display();
	}

	public function edit($id){
		$_GET['model'] = 'Nav';
		$this->_edit();
	}

	//editble ajax更新方法
    public function ajaxUpdateType(){
        $_POST = array(
            'id'=>I('pk'),
            I('name')=>I('value'),
            'model'=>'Type'
        );
        $this->update();
    }

	public function editbleGetNav(){
		$nav_tree = D('Nav')->where('status=1')->select();
		$nav_tree = D('Tree')->toFormatTree($nav_tree);
		$list = array();
		foreach ($nav_tree as $key => $value) {
			// $title_show = htmlentities($value['title_show']);
			$title_show = str_replace('&nbsp;', ' ', $value['title_show']);
			$list[] = array('value'=>$value['id'],'text'=>"{$title_show}{$value['c_name']}");
		}
		// dump($list);
		exit(json_encode($list));
		// $this->editbleAjaxGet('Nav','id as value,c_name as text');
	}

	public function type(){
		$name = trim(I('name'));
		if($name)
			$map['name'] = array('like', "%{$name}%");
		$this->_list(array('source'=>'Type', 'map'=>$map));
	}

	public function editbleGetType(){
		$this->editbleAjaxGet('Type','id as value,name as text');
	}
}