<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */

class SpecialController extends CommonController {

	public function index(){
		$pid = (int)I('pid');
		$name = trim(I('name'));
		if($name)
			$map['name'] = array('like',"%{$name}%");
		$current_parent = D('Nav')->field('pid,name')->find($pid);
		$this->assign('current_parent', $current_parent);
		$this->_list(array('source'=>'Special', 'map'=>$map));
	}

	public function add(){
		$pid = D('Nav')->where("c_name='表达谱芯片服务'")->getField('id');
		$nav_tree = D('Nav')->where("status=1 AND pid={$pid}")->select();
		$this->assign('nav_tree',$nav_tree);
		$type = D('Type')->getField('id,name');
		$this->assign('type', $type);
		$this->display();
	}

	public function edit($id){
		$_GET['model'] = 'Special';
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
		$pid = D('Nav')->where("c_name='表达谱芯片服务'")->getField('id');
		$list = D('Nav')->where("status=1 AND pid={$pid}")->field('id as value,c_name as text')->select();
		exit(json_encode($list));
		// $this->editbleAjaxGet('Nav','id as value,c_name as text');
	}

	public function editbleGetType(){
		$this->editbleAjaxGet('Type','id as value,name as text');
	}
}