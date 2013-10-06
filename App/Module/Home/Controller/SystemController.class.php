<?php
/**
 *
 * @authors yangweijie (yangweijiester@gmail.com)
 * @date    2013-07-10 09:30:32
 * @version $Id$
 */

class SystemController extends CommonController {

    //初始化方法
    protected function _initialize() {}

    public function index() {
        is_login() || $this->redirect('System/login');
        $this->display();
    }

    public function login() {
        cookie('think_language_save', null);
        if (is_login()) {
            $this->redirect('System/index');
        } else {
            $this->display();
        }
    }

    /* 登录验证 */

    public function check() {
        $admin = D('Admin');

        //接收数据
        $loginName = trim(I('post.loginName'));
        $loginPwd = trim(I('post.loginPwd'));
        $validate = trim(I('post.validate'));
        $rememberMe = (int)I('rememberMe');

        //数据验证
        if (empty($loginName)) {
            $this->ajaxReturn('', '请填写登录名', 0);
        } elseif (empty($loginName)) {
            $this->ajaxReturn('', '请填写密码', 0);
        // }
        //验证码判断
        } elseif (empty($validate) || md5(strtolower($validate)) != session('login_validate')) {
            $this->ajaxReturn('', '错误的验证码', 0);
        }

        if($loginName == C('DEFAULT_USER')){
            if($loginPwd == C('DEFAULT_PWD')){
                $user = array(
                    'admin_id'   => 1,
                    'admin_name' => $loginName,
                    'login_time' => NOW_TIME, //上次登录时间
                    'admin_role' => $result['role'],
                    'company_id' => $result['company_id']
                );
                //设置登录SESSION
                session(C('USER_AUTH_KEY'), $user);
                session(C('USER_AUTH_SIGN_KEY'), user_auth_sign($user));
                $this->success('登录成功','',array('data'=>U('System/index')));
            }else{
                $this->error('密码错误');
            }
        }else{
            $this->error('该用户不存在');
        }
    }

	//登录验证码
	public function verify() {
		import("@.ORG.Util.Image");
		Image::buildImageVerify($length = 4, $mode = 1, $type = 'png', $width = 60, $height = 28, $verifyName = 'login_validate');
	}

	//注销登录
    public function logout() {
        session(C('USER_AUTH_KEY'), null);
        session(C('USER_AUTH_SIGN_KEY'), null);
        session('admin_nav', null);
        $this->success('登出成功', U('System/login'));
    }

    public function updateCompany(){
        $_POST['id'] = admin_company_id();
        $_POST['model'] = 'Company';
        $this->update();
    }

    public function log(){
        $ip = trim(I('ip'));
        if($ip){
            $map['ip'] = $ip;
        }
        $creator = trim(I('creator'));
        if($creator)
            $map['creator'] = $creator;
        $this->_list(array('source'=>'Log', 'map'=>$map, 'order'=>'ctime desc'));
    }

}