<?php
namespace app\user\controller;

use cmf\controller\UserBaseController;

class DailyController extends UserBaseController
{
	public function checkin()
	{
		if ((''==input('post.userid'))||(''==input('post.bookid'))||(''==input('post.signday'))||(''==input('post.startpage'))||(''==input('post.endpage'))||(''==input('post.notes')))
		{
			return json(['code'=>1001,'msg'=>"有信息未填写!"]);
		}
		else
		{
			db('daily')->insert(['userid'=>input('post.userid'),'bookid'=>input('post.bookid'),'signtime'=>'NOW()','signday'=>input('post.day'),'startpage'=>input('post.startpage'),'endpage'=>input('post.endpage'),'notes'=>input('post.notes')]);
			return ['code'=>'1001','msg'=>'签到成功'];
		}
	}
	public function find()
	{
		return db('daily')->where('user',input('post.userid'))->find();
	}
}