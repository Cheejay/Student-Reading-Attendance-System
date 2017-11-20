<?php
// +----------------------------------------------------------------------
// | Nologin [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017 Tangchao All rights reserved.
// +----------------------------------------------------------------------
// | Author: Tangchao <79300975@qq.com>
// +----------------------------------------------------------------------
namespace plugins\nologin;
use cmf\lib\Plugin;

class NologinPlugin extends Plugin
{

    public $info = array(
        'name'        => 'Nologin',
        'title'       => '前台免登陆',
        'description' => '前台免登陆',
        'status'      => 1,
        'author'      => 'Tangchao',
        'version'     => '1.0'
    );

    public $hasAdmin = 0;

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        return true;
    }

    public function footerStart($param)
    {
        return true;
    }

}