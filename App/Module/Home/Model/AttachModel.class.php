<?php
	Class AttachModel extends Model{

		protected function _after_find(&$result,$options) {
			$result['time_text'] = date('Y-m-d H:i', $result['ctime']);
		}

		protected function _after_select(&$result,$options){
			foreach($result as &$record){
				$this->_after_find($record,$options);
			}
		}

		// protected function _after_insert($data,$options) {

		// }

		protected $_auto = array (
			array('ctime','time',3,'function') , // 对password字段在新增的时候使md5函数处理
		);

	 	protected $_map = array(
	        'name' 		=>'o_name',
	        'savepath'  =>'path',
	        'savename' =>'name'
	    );

	}