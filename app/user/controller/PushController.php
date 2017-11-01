<?php

namespace app\user\controller;

use cmf\controller\HomeBaseController;

class PushController extends HomeBaseController
{
	public function pushnews()
	{
		$sql = db('push')->query('SELECT * FROM T_USER  ORDER BY  RAND() LIMIT 1');
		return json_encode($sql);
	}
}