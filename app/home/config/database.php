<?php
return [
    'db' => [
        'core' => [
            'type'      => 'mysql',
            'hostname'  => 'localhost',
            'hostport'  => '3306',
            'database'  => 'phpweb_core',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'prefix'    => '',
        ],
        'log' => [
            'type'      => 'mysql',
            'hostname'  => 'localhost',
            'hostport'  => '3306',
            'database'  => 'phpweb_log',
            'username'  => 'root',
            'password'  => 'root',
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
            'prefix'    => '',
        ],
    ],
];
// 两个数据库都创建这个表
/*CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `account` varchar(32) NOT NULL COMMENT '帐号',
  `password` char(32) NOT NULL COMMENT '密码',
  PRIMARY KEY (`id`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='账号表';*/
