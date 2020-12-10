<?php
namespace app\home\controller;

use core\Controller;


class Index extends Controller {
    public function Index() {
        echo 'hello from Home.Index';
    }

    public function Test() {
        echo 'hello from Home.Test';
    }

    public function View() {
        $this->assign('name', '老王');
        $this->import();
    }

    public function View2() {
        $this->assign('name', '老宋');
        $this->import('Index/index_view2.html.php');
    }
}
