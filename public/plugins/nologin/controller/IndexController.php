<?php
// +----------------------------------------------------------------------
// | Nologin [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 Tangchao All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tangchao <79300975@qq.com>
// +----------------------------------------------------------------------
namespace plugins\nologin\controller;
use cmf\controller\PluginBaseController;
use think\Db;
use app\user\model\UserModel;

class IndexController extends PluginBaseController
{
    function _initialize()
    {
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        } else {
            die();
        }
    }
    function index($id)
    {
        $data   = Db::name("user")->where('id', $id)->find();
        cmf_update_current_user($data);
        return $this->fetch("/index");
    }
}