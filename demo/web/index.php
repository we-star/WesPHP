<?php
/**
 * 入口
 */
$dir = __DIR__;
$parentDir = dirname($dir); // 总项目目录
define("PATH_WESPHP", $_SERVER["PATH_WESPHP"]); // 框架目录
define("PATH_ROOT", $dir); // 项目目录
define("PATH_COMMON_CLASS", "{$parentDir}/class"); // 定义通用类路径
define("PATH_COMMON_CONFIG", "{$parentDir}/config"); // 定义通知用配置路戏
define("PATH_INCLUDES", "{$parentDir}/includes"); // 第三方类库
define("PATH_CLASS", "{$dir}/class"); // 项目类
define("PATH_CONFIG", "{$dir}/config"); // 项目配置
define("PATH_LOG", "{$dir}/logs"); // 项目日志
define("PATH_DATA", "{$dir}/data"); // 项目数据文件目录
define("APP_ENV", "dev"); // 应用环境 dev: 开发版本 beta: Beta版本 release: 线上正式版本

require_once PATH_WESPHP . "/Autoload.php";

$app = new WesApp();
$app->run("Page");