<?php
/**
 * 入口
 */
$dir = __DIR__;
$parentDir = dirname($dir);
define("PATH_WESPHP", "/data/web/WesPHP/lib");
define("PATH_ROOT", $dir);
define("PATH_COMMON_CLASS", "{$parentDir}/class"); // 定义通用类路径
define("PATH_COMMON_CONFIG", "{$parentDir}/config"); // 定义通知用配置路戏
define("PATH_INCLUDES", "{$parentDir}/includes"); // 第三方类库
define("PATH_PARENT", $parentDir); // 项目主目录
define("PATH_CLASS", "{$dir}/class");
define("PATH_CONFIG", "{$dir}/config");
define("PATH_LOG", "{$dir}/logs");
define("APP_ENV", "dev");

require_once PATH_WESPHP . "/Autoload.php";

ini_set("default_socket_timeout", -1);
ini_set("max_execution_time", 0);

$app = new WesApp();
$app->run("Daemon"); // 开发测试环境，开启调试模式，方便排查问题