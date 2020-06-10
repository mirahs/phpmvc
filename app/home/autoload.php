<?php
/**
 * 应用自动加载文件
 */


/** app数据库配置 */
\core\conf(require_once 'config/database.php');


/** 设置多个数据库配置信息 */
\think\Db::setConfig(\core\conf('db'));
/** 数据库配置信息设置（全局有效） */
\think\Db::setConfig(\core\conf('db.core'));
