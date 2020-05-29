<?php
namespace app\home\model;

use think\Model;


class User extends Model
{
    // 多个数据库切换一定需要先设置一个默认的数据库连接信息, 不然Model Query会报错
    //protected $connection = 'test';
}
