<?php
namespace app\home\controller;

use app\common\BaseController;
use app\home\model\LogUser;
use think\Db;
use app\home\model\User;


class Index extends BaseController {
    public function Index() {
        echo 'hello index';
    }

    public function Test() {
        echo 'hello test' . "<br />";
        echo "\core\uri_full():" . \core\uri_full() . "<br />";
        echo '$_SERVER[\'REQUEST_URI\']:' . $_SERVER['REQUEST_URI'] . "<br />";
        echo '\core\ip():' . \core\ip() . "<br />";
        echo '\core\url():' . \core\url('/test', ['a' => 'aaa', 'b' => 222]) . "<br />";
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

    public function TestDebug() {
        $this->log('from Index.TestDebug', 111);
        $this->log('from Index.TestDebug 222');
    }

    public function TestDebug2() {
        $this->debug('test2.log');
        $this->log('from Index.TestDebug2', 111);
        $this->log('from Index.TestDebug2 222');
    }
}
