<?php

namespace app\user\controller;

use cmf\controller\UserBaseController;

class BookController extends UserBaseController
{
    public function AddBook()
    {
        return $this->fetch();
    }

    public function AddBookPost()
    {
        if ((''==input('post.name'))||(''==input('post.studentid'))||(''==input('post.author'))||(''==input('post.press'))||(''==input('post.bookpage')))
        {
            return json(['code'=>1001,'msg'=>"有数据未填写"]);
        }
        else
        {
            db('book')->insert(['bookname'=>input('post.name'),'studentid'=>input('post.studentid'),'author'=>input('post.author'),'press'=>input('post.press'),'bookpage'=>input('post.bookpage'),'createtime'=>'now()']);
            return json(['code'=>1000,'msg'=>"写入完成"]);
        }
    }

    public function EditBookPost()
    {
       if ((''==input('post.name'))||(''==input('post.studentid'))||(''==input('post.author'))||(''==input('post.press'))||(''==input('post.bookpage'))||(''==input('post.type')))
        {
         return json['code'=>1001,'msg'=>"有数据未填写"]);
        }
       else
        {
         db('book')->update(['bookname'=>input('post.name'),'studentid'=>input('post.studentid'),'author'=>input('post.author'),'press'=>input('post.press'),'bookpage'=>input('post.bookpage')]);
         return json(['code'=>1002,'msg'=>"修改成功"]);
        }
    }

    public function DelBookPost()
    {
      db('book')->where('id',input('post.id'))->delete();
      return json(['code'=>1003,'msg'=>"删除成功"]);
    }

    /*
    * 废弃的功能
    */
    // public function FindBookPost()
    // {
    //   if ('' == input('post.id'))
    //   {
    //     return ['code'=>1001,'msg'=>"有数据未填写"];
    //   }
    //   else
    //   {
    //     return json(db('book')->where('id',input('post.id'))->find());
    //   }
    // }

    public function GetBookId()
    {
      if ('' == input('post.name'))
      {
        return json(['code'=>1001,'msg'=>"有数据未填写"]);
      }
      else {
        return json(['code'=>1004,'id'=>(db('book')->where('name',input('post.name'))->find())['id']]);
      }
    }


    /*
      Top10
    */
    public function BookList()
    {
        //TODO
      
    }
}
