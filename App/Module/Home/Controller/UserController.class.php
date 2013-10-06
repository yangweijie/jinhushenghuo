<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */

class UserController extends CommonController {

	public function index(){
		$this->_list(array('source'=>'User'));
	}

	public function add(){
		$this->display();
	}

}