<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use think\Validate;
use cmf\controller\HomeBaseController;
use app\user\model\UserModel;

class LoginController extends HomeBaseController
{
    

    /**
     * 登录
     */
    public function index()
    {
        $redirect = $this->request->post("redirect");
        if (empty($redirect)) {
            $redirect = $this->request->server('HTTP_REFERER');
        } else {
            $redirect = base64_decode($redirect);
        }
        session('login_http_referer', $redirect);
        if (cmf_is_user_login()) { //已经登录时直接跳到首页
            return redirect($this->request->root() . '/');
        } else {
            return $this->fetch(":login");
        }
        
    }

    public function check($sid, $checkAddress, $token, $userName = '', $userId = 0) {
        echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
		$this->pubkey = '95dfc2cc-d2ec-43e7-a718-1bd9d178ff45';
		$this->prikey = '9dcdd1e4-e16c-40a4-8d7e-7c28c4a6cbb1';
		if (empty ($checkAddress) || empty ($token) || empty($this->prikey) || empty($this->pubkey)) {
			$this->error("请输入验证码！");
            exit();
		}
		if (!preg_match('/^[\w\-]/',$checkAddress)){
			exit('参数格式不正确');
		}
		$params ['s'] = $sid;
		$params ['un'] = $userName;
		$params ['ud'] = $userId;
		$params ['ip'] = @gethostbyname ( $_ENV ['COMPUTERNAME'] );
		$params ['i'] = $token;
		$params ['b'] = $this->pubkey;
		$params ['ran'] = md5(strtotime('now'));
		$sign = $this->getSign ( $params, $this->prikey );
		$params ['sign'] = $sign;
		$paramStr = $this->getStr ( $params );
		$url = "http://" . $checkAddress . ".touclick.com/sverify.touclick2" . '?' . $paramStr;
		// use curl
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		$check = is_object(json_decode($result)) ? get_object_vars(json_decode($result)) : json_decode($result);
		if ($check['code'] == 0) {
			$checkParam['code'] = $check['code'];
			$checkParam['timestamp'] = $params ['ran'];
			$sign = $this->getSign($checkParam, $this->prikey);
			if ($sign == $check['sign']) {
				$res['code'] = 12;
				$res['message'] = '验证成功';
				$res['checkCode'] = $check['ckCode'];
				return $res;
			} else {
				$res['code'] = 12;
				$res['message'] = '签名校验失败,数据可能被篡改';
				return $res;
			}
		} else {
			unset($check['timestamp']);
			unset($check['sign']);
			return $check; 
		}
    }    
    private function getSign($params, $prikey) {
		ksort ( $params ) ? $paramsStr = $this->getStr ( $params ) : exit ( '排序失败' );
		$sign = $paramsStr . $prikey;
		return md5 ( $sign );
	}
    	/**
	 * 数组=》字符串 key=value&key=value
	 * 
	 * @author : rainbow
	 * @param : $params        	
	 * @return : $paramsStr
	 */
	private function getStr($params) {
		$paramsStr = '';
		if (! $params || ! is_array ( $params )) {
			exit ( '参数错误' );
		}
		$keys = array_keys ( $params );
		foreach ( $params as $key => $value ) {
			if ($key == end ( $keys )) {
				$paramsStr .= $key . '=' . $value;
			} else {
				$paramsStr .= $key . '=' . $value . '&';
			}
		}
		return $paramsStr;
    }
    
    /**
     * 登录验证提交
     */
    public function doLogin()
    {
      //  $this->check();
        if ($this->request->isPost()) {
            $validate = new Validate([
      //        'captcha'  => 'require',
                'username' => 'require',
                'password' => 'require|min:6|max:32',
            ]);
            $validate->message([
                'username.require' => '用户名不能为空',
                'password.require' => '密码不能为空',
                'password.max'     => '密码不能超过32个字符',
                'password.min'     => '密码不能小于6个字符',
       //         'captcha.require'  => '验证码不能为空',
            ]);

            $data = $this->request->post();
            $res=$this->check($data['sid'],$data['checkAddress'],$data['token']);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

      //      if (!cmf_captcha_check($data['captcha'])) {
      //          $this->error('验证码错误');
      //      }

            if ($res['code']!="12"){
                $this->error($res['message']);
                exit();
            }
            $userModel         = new UserModel();
            $user['user_pass'] = $data['password'];
            if (Validate::is($data['username'], 'email')) {
                $user['user_email'] = $data['username'];
                $log                = $userModel->doEmail($user);
            } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
                $user['mobile'] = $data['username'];
                $log            = $userModel->doMobile($user);
            } else {
                $user['user_login'] = $data['username'];
                $log                = $userModel->doName($user);
            }
            $session_login_http_referer = session('login_http_referer');
            $redirect                   = empty($session_login_http_referer) ? $this->request->root() : $session_login_http_referer;
            switch ($log) {
                case 0:
                    cmf_user_action('login');
                    $this->success('登录成功', $redirect);
                    break;
                case 1:
                    $this->error('登录密码错误');
                    break;
                case 2:
                    $this->error('账户不存在');
                    break;
                case 3:
                    $this->error('账号被禁止访问系统');
                    break;
                default :
                    $this->error('未受理的请求');
            }
        } else {
            $this->error("请求错误");
        }
    }

    /**
     * 找回密码
     */
    public function findPassword()
    {
        return $this->fetch('/find_password');
    }

    /**
     * 用户密码重置
     */
    public function passwordReset()
    {

        if ($this->request->isPost()) {
            $validate = new Validate([
                'captcha'           => 'require',
                'verification_code' => 'require',
                'password'          => 'require|min:6|max:32',
            ]);
            $validate->message([
                'verification_code.require' => '验证码不能为空',
                'password.require'          => '密码不能为空',
                'password.max'              => '密码不能超过32个字符',
                'password.min'              => '密码不能小于6个字符',
                'captcha.require'           => '验证码不能为空',
            ]);

            $data = $this->request->post();
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }

            if (!cmf_captcha_check($data['captcha'])) {
                $this->error('验证码错误');
            }
            $errMsg = cmf_check_verification_code($data['username'], $data['verification_code']);
            if (!empty($errMsg)) {
                $this->error($errMsg);
            }

            $userModel = new UserModel();
            if ($validate::is($data['username'], 'email')) {

                $log = $userModel->emailPasswordReset($data['username'], $data['password']);

            } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
                $user['mobile'] = $data['username'];
                $log            = $userModel->mobilePasswordReset($data['username'], $data['password']);
            } else {
                $log = 2;
            }
            switch ($log) {
                case 0:
                    $this->success('密码重置成功', $this->request->root());
                    break;
                case 1:
                    $this->error("您的账户尚未注册");
                    break;
                case 2:
                    $this->error("您输入的账号格式错误");
                    break;
                default :
                    $this->error('未受理的请求');
            }

        } else {
            $this->error("请求错误");
        }
    }

    

}