<?php
	Class ArticleModel extends Model{

		protected function _after_find(&$result,$options) {
			$result['time_text'] = date('Y-m-d H:i', $result['ctime']);
			$result['p_name'] = D('Nav')->getFieldById($result['nav_id'],'name');
			if(!$result['p_name'])
				$result['p_name'] = '顶级分类';
		}

		protected function _after_select(&$result,$options){
			foreach($result as &$record){
				$this->_after_find($record,$options);
			}
		}

		protected function _after_insert($data,$options) {
			// $content = $data['tel'] ? "手机号：{$data['tel']}" : '';
			// $content.= $data['email'] ? "邮箱：{$data['email']}": '';
			// admin_log("新增了黑名单：{$content}");
		}

		protected $_auto = array (
			array('ctime','time',1,'function') , // 对password字段在新增的时候使md5函数处理
		);

		protected $_validate = array(
			array('title','','标题唯一！',0,'unique',Model::MODEL_BOTH), // 在新增的时候验证name字段是否唯一
		);

	}