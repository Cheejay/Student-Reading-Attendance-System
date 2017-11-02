<?php

namespace app\admin\controller;

use cmf\controller\AdminBaseController;

class BookController extends AdminBaseController
{
    public function AddBook()
    {
        return $this->fetch();
    }

    public function AddBookPost()
    {
        if (''==(input('name'))||''==(input('isbn'))||''==(input('category')))
        {
            $this->error("有数据未填写");
        }
        else
        {
            db('book')->insert(['name'=>input('name'),'isbn'=>input('isbn'),'category'=>input('category')]);
            $this->success("添加成功");
        }
    }

    public function EditBookPost()
    {
       if (''== input('id') || (''==(input('name'))||''==(input('isbn'))||''==(input('category'))))
       {
         $this->error("有数据未填写!");
       }
       else
       {
         if ('ok' == 'edit')
         {
         db('book')->update(['id' => input('id'),'name'=>input('name'),'isbn'=>input('isbn'),'category'=>input('category')]);
         $this->success("修改成功!");
        }
        elseif ('ok' == 'del') {
          db('book')->where('id',input('id'))->delete();
          $this->success("删除成功!");
        }
       }
    }

    public function FindBook()
    {
      return $this->fetch();
    }

    public function FindBookPost()
    {
      if ('' == input('id'))
      {
        $this->error('Id不能为空');
      }
      else
      {
        $info = db('book')->where('id',input('id'))->find();
        $this->assign('info',$info);
        return $this->fetch();
      }
    }

    public function GetBookId()
    {
      if ('' == input('name'))
      {
        $this->error("Name不能为空");
      }
      else {
        return (db('book')->where('name',input('name'))->find())['id'];
      }
    }
    
    public function BookList()
    {
        //TODO
    }
}
