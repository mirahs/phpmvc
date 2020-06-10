<?php
namespace app\home\controller;

use app\home\model\LogUser;
use \core\Controller;
use think\Db;
use app\home\model\User;


class Index extends Controller {
    public function Index() {
        echo 'hello index';
    }

    public function Test() {
        echo 'hello test';
    }

    public function View() {
        $this->Template();

        $this->assign('name', 'mirahs');

        $this->import();
    }


    public function Db() {
        $db = \core\db('core');
        $data = $db->dataArray('SELECT * FROM `user`');
        print_r($data);
    }

    public function DbCore() {
        Db::table('user')->data(['account'=>'thinkphp','password'=>'123456'])->insert();
        print_r(Db::table('user')->find());
        Db::table('user')->where('account','thinkphp')->update(['password' => 'abcdef']);
        print_r(Db::table('user')->find());
        Db::table('user')->where('account','thinkphp')->delete();

        Db::table('user')->data(['account'=>time(),'password'=>'123456'])->insert();
    }

    public function DbLog() {
        Db::connect('log')->table('user')->data(['account'=>'thinkphp','password'=>'123456'])->insert();
        print_r(Db::connect('log')->table('user')->find());
        Db::connect('log')->table('user')->where('account','thinkphp')->update(['password' => 'abcdef']);
        print_r(Db::connect('log')->table('user')->find());
        Db::connect('log')->table('user')->where('account','thinkphp')->delete();

        Db::connect('log')->table('user')->data(['account'=>time(),'password'=>'123456'])->insert();
    }

    public function OrmCore() {
        $user = new User;
        $user->account = 'cy';
        $user->password = 'cy';
        $user->save();

        $user2 = User::where('account', 'cy')->find();
        echo '$user2->account:' . $user2->account . ', $user2->password:' . $user2->password . '<br />';
        $user2->account = 'cb';
        $user2->password = 'cb';
        $user2->save();

        $user3 = User::where('account', 'cb')->find();
        echo '$user3->account:' . $user3->account . ', $user3->password:' . $user3->password . '<br />';

        User::destroy(['account' => 'cb']);
    }

    public function OrmLog() {
        $user = new LogUser();
        $user->account = 'cy';
        $user->password = 'cy';
        $user->save();

        $user2 = LogUser::where('account', 'cy')->find();
        echo '$user2->account:' . $user2->account . ', $user2->password:' . $user2->password . '<br />';
        $user2->account = 'cb';
        $user2->password = 'cb';
        $user2->save();

        $user3 = LogUser::where('account', 'cb')->find();
        echo '$user3->account:' . $user3->account . ', $user3->password:' . $user3->password . '<br />';

        LogUser::destroy(['account' => 'cb']);
    }
}
