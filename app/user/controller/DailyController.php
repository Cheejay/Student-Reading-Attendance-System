<?php
namespace app\user\controller;

use cmf\controller\UserBaseController;

class DailyController extends UserBaseController
{
	public function checkin()
	{
		if (''==input('id'))
		{
			$this->error('id不能为空!');
		}
		else
		{
			$info = db('user')->where('id',input('id'))->find();
			db('daily')->insert(['userid'=>input('id'),'username'=>$info['user_login'],'date'=>'NOW()']);
			return ['code'=>'1001','msg'=>'签到成功'];
		}
	}
}