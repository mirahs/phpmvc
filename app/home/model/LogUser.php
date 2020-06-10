<?php
namespace app\home\model;

use think\Model;


class LogUser extends Model {
    protected $connection = 'log';
    protected $table = 'user';
}
