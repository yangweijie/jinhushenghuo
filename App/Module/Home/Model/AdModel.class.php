<?php
	Class AdModel extends Model{
		protected $_auto = array (
			// array('status','1'),  // 新增的时候把status字段设置为1
			array('files','json_encode',3,'function') ,
			array('startTime','strtotime',3,'function') ,
			array('endTime','strtotime',3,'function') ,
		);
	}