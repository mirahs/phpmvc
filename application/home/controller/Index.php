<?php
namespace app\home\controller;


class Index extends \core\Controller
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
        $db = self::db('test');
        $data = $db->dataArray('SELECT * FROM `user`');
        print_r($data);
    }

    public function View()
    {
        $this->Template();

        $this->assign('name', 'mirahs');

        $this->import('index/view.html.php');
    }
}
