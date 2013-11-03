<?php
	Class UserModel extends Model{

		protected $_validate = array(
			array('name','require','用户名必须！'), //默认情况下用正则进行验证
			array('email','require','邮箱必须'), //默认情况下用正则进行验证
			array('pwd','require','密码'), //默认情况下用正则进行验证
			array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
		);

		protected $_auto = array (
			array('password','md5',1,'function') , // 对password字段在新增的时候使md5函数处理
			array('ctime','time',2,'function'), // 对create_time字段在更新的时候写入当前时间戳
		);

		protected function _after_find(&$result,$options) {
			$result['time_text'] = date('Y-m-d H:i', $result['ctime']);
		}

		protected function _after_select(&$result,$options){
			foreach($result as &$record){
				$this->_after_find($record,$options);
			}
		}

		protected function _after_insert($data,$options) {

		}

	}