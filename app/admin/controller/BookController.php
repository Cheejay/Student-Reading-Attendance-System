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
        if (''==(input('post.name'))||''==(input('post.isbn'))||''==(input('post.category')))
        {
            return json(['code'=>1001,'msg'=>"有数据未填写"]);
        }
        else
        {
            db('book')->insert(['name'=>input('post.name'),'isbn'=>input('post.isbn'),'category'=>input('post.category')]);
            return json(['code'=>1000,'msg'=>"写入完成"]);
        }
    }

    public function EditBookPost()
    {
       if (''== input('post.id') || (''==(input('post.name'))||''==(input('post.isbn'))||''==(input('post.category')||''==(input('post.post.type')))))
       {
         return json['code'=>1001,'msg'=>"有数据未填写"]);
       }
       else
       {
         if (input('post.post.type') == 'edit')
         {
         db('book')->update(['id' => input('post.id'),'name'=>input('post.name'),'isbn'=>input('post.isbn'),'category'=>input('post.category')]);
         return json(['code'=>1002,'msg'=>"修改成功"]);
        }
        elseif (input('post.post.type') == 'del') {
          db('book')->where('id',input('post.id'))->delete();
          return json(['code'=>1003,'msg'=>"删除成功"]);
        }
       }
    }

    public function FindBookPost()
    {
      if ('' == input('post.id'))
      {
        return ['code'=>1001,'msg'=>"有数据未填写"];
      }
      else
      {
        return json(db('book')->where('id',input('post.id'))->find());
      }
    }

    public function GetBookId()
    {
      if ('' == input('post.name'))
      {
        return json(['code'=>1001,'msg'=>"有数据未填写"]);
      }
      else {
        return json((db('book')->where('name',input('post.name'))->find())['id']);
      }
    }
    
    public function test()
    {
        return json(['code'=>1000,'msg'=>"Hello!"]);
    }

    public function BookList()
    {
        //TODO
    }
}
