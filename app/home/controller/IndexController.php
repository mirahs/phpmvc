<?php
namespace app\home\controller;

use \core\Controller;

use think\Db;

use app\home\model\User;


class IndexController extends Controller
{
    public function Index()
    {
        echo 'hello index';
    }

    public function Test()
    {
        echo 'hello test';
    }

    public function Tdb()
    {
        $db = DB('test');
        $data = $db->dataArray('SELECT * FROM `user`');
        print_r($data);
    }

    public function TThinkDb()
    {
        Db::setConfig(C('db.test'));

        Db::table('user')->data(['name'=>'thinkphp','email'=>'thinkphp@qq.com'])->insert();
        print_r(Db::table('user')->find());
        Db::table('user')->where('name','thinkphp')->update(['email'=>'mirahs']);
        print_r(Db::table('user')->find());
        Db::table('user')->where('name','thinkphp')->delete();
    }

    public function TThinkOrm()
    {
        Db::setConfig(C('db.test'));

        $user = new User;
        $user->name = 'cy';
        $user->email = 'cy';
        $user->save();

        $user2 = User::get(1);
        echo '$user2->name:' . $user2->name . ', $user2->email:' . $user2->email . '<br />';
        $user2->name = 'cb';
        $user2->email = 'cb';
        $user2->save();

        $user3 = User::get(1);
        echo '$user3->name:' . $user3->name . ', $user3->email:' . $user3->email . '<br />';

        User::destroy(1);
    }

    public function View()
    {
        $this->Template();

        $this->assign('name', 'mirahs');

        $this->import();
    }
}
