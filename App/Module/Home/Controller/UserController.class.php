<?php

/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */
class UserController extends Action {

	public function register() {
		if (IS_POST) {
			$userModel = D('User');
			$data = $userModel->create($_POST);
			if ($data) {
				if ($userModel->add($data)) {
					$this->success('注册成功', U('User/login'));
				} else {
					$this->error('注册失败');
				}
			} else {
				$this->error($userModel->getError());
			}
		} else {
			$this->display();
		}
	}

	public function login() {
		if (IS_POST) {
			$user = D('User');
            //接收数据
			$loginName = trim(I('post.name'));
			$loginPwd = trim(I('post.pwd'));
			$rememberMe = (int) I('rememberMe');
            //数据验证
			if (empty($loginName)) {
				$this->error('请填写登录名');
			} elseif (empty($loginName)) {
				$this->error('请填写密码');
			}
			if ($login_user = $user->where("name='{$loginName}'")->find()) {
				if (md5($loginPwd) == $login_user['pwd']) {
					$user2 = array(
						'id' => $login_user['id'],
						'name' => $login_user['name'],
						);
                    //设置登录SESSION
					session(C('FRONT_USER_AUTH_KEY'), $user2);
					session(C('FRONT_USER_AUTH_SIGN_KEY'), user_auth_sign($user2));
					$this->success('登录成功', U(''));
				} else {
					$this->error('密码错误');
				}
			} else {
				$this->error($user->_sql());
				$this->error('用户不存在');
			}
		} else {
			$this->display();
		}
	}

	public function info() {
		($id = is_front_login()) || $this->redirect('User/login');
		$user = D('User')->find($id);
		$this->assign('info', $user);
		$this->display();
	}

	function avatar() {
		$do = I('get.do');
		if ($do == 'save') {
            //保存裁切
            $aspectRatio = I('post.aspectRatio');  //裁切比列
            $filename = '.' . I('post.avatar_src');  //头像的临时地址
            $x = I('post.x') * $aspectRatio;
            $y = I('post.y') * $aspectRatio;
            $w = I('post.w') * $aspectRatio;
            $h = I('post.h') * $aspectRatio;
            //一下是入库信息
            $user = session(C('FRONT_USER_AUTH_KEY'));
            $model = M('User');
            $map = array();
            $map['id'] = $user['id'];
            $data['big_avatar'] = $this->jcrop($filename,$x,$y,$w,$h,'big');
            $data['small_avatar'] = $this->jcrop($filename,$x,$y,$w,$h,'small');
            $model->where($map)->save($data);
            $big_avatar = $this->jcrop($filename, $x, $y, $w, $h, 'big');
            $small_avatar = $this->jcrop($filename, $x, $y, $w, $h, 'small');
            cookie('big', $big_avatar);
            cookie('small', $small_avatar);
            unlink($filename); //删除临时图片
            $this->success('头像上传成功', U('Index/index'));
        } else {
            //上传头像
        	import("@.ORG.Net.UploadFile");
            $upload = new UploadFile(); // 实例化上传类
            $upload->maxSize = 2000000; // 设置附件上传大小
            $upload->saveRule = 'uniqid';
            $upload->uploadReplace = true;
            $upload->allowExts = array('jpg', 'gif', 'png', 'jpeg'); // 设置附件上传类型
            $upload->savePath = './Uploads/avatar/tmp/'; // 设置附件上传目录
            if (!$upload->upload()) { // 上传错误提示错误信息
            	$array['status'] = 0;
            	$array['message'] = $upload->getErrorMsg();
            	echo json_encode($array);
            	exit;
            } else {
            	$info = $upload->getUploadFileInfo();
            	$file['savename'] = $info[0]['savepath'] . $info[0]['savename'];
            	$file['avatar'] = trim($info[0]['savepath'], '.') . $info[0]['savename'];
            	$file['status'] = 1;
            	$image = getimagesize($file['savename']);
            	$file['width'] = $image[0];
            	$file['height'] = $image[1];
            	echo json_encode($file);
            	exit;
            }
        }
    }

    //裁切函数
    protected function jcrop($filename, $x, $y, $w, $h, $size) {
    	$pathinfo = pathinfo($filename);
    	if ($size == 'big') {
            $targ_w = 200; //大图比例
            $targ_h = 200; //大图比例
            $src = './Uploads/avatar/big/' . $pathinfo['basename'];
        } elseif ($size == 'small') {
            $targ_w = 60; //小图比例
            $targ_h = 60; //小图比例
            $src = './Uploads/avatar/small/' . $pathinfo['basename'];
        }
        $jpeg_quality = 90;
        list($imagewidth, $imageheight, $imageType) = getimagesize($filename);
        $imageType = image_type_to_mime_type($imageType);
        switch ($imageType) {
        	case "image/gif":
        	$img_r = imagecreatefromgif($filename);
        	break;
        	case "image/pjpeg":
        	case "image/jpeg":
        	case "image/jpg":
        	$img_r = imagecreatefromjpeg($filename);
        	break;
        	case "image/png":
        	case "image/x-png":
        	$img_r = imagecreatefrompng($filename);
        	break;
        }
        $dst_r = ImageCreateTrueColor($targ_w, $targ_h);
        imagecopyresampled($dst_r, $img_r, 0, 0, $x, $y, $targ_w, $targ_h, $w, $h);
        //header('Content-type: image/jpeg');
        imagejpeg($dst_r, $src, $jpeg_quality);
        return trim($src, '.');
        //exit;
    }

}
