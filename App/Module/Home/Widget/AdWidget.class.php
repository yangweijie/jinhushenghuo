<?php
	class AdWidget extends Action{

		public function index($cate='',$type=0){
			trace($cate);
			trace($type);
			if($type == 1){
				$this->display('Ad/type_1');
			}
			if($type == 2){
				$this->display('Ad/type_2');
			}
			if($type == 3){
				$this->display('Ad/type_3');
			}
			if($type == 4){
				$this->display('Ad/type_4');
			}
		}

	}

?>