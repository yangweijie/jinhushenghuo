<?php
	Class MessageModel extends Model{
		protected $_auto = array (
			array('ctime','time',3,'function') , // 对password字段在新增的时候使md5函数处理
		);

		protected $_validate = array(
			array('content','require','评论内容必须！'), //默认情况下用正则进行验证
			array('uid','require','必须登录后才可评论！'), // 在新增的时候验证name字段是否唯一
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