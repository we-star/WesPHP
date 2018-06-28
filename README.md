WesPHP简介
====
WesPHP是一款轻型，灵巧的PHP项目开发框架。非常适合中小型项目，可以用在WEB，API接口，命令行或者守护程序等场景应用。就框架本身而言也是对一些基础类库做了封装和对其他第三方法的类库做了二次封装。如：Smarty, PHPExcel, PHPMailer和PHPQrcode等。整体上还是MVC的设计思路，但在项目的工程实施方面会略有不同。具体可以参考样例代码。

欢迎联系作者：17217733@qq.com

主要内容
====
1. [基础](#基础)
 * [安装WesPHP](#安装wesphp)
 * [目录结构](#目录结构)
 * [开发规范](#开发规范)
 * [快速开始](#快速开始)
2. [架构](#架构)
 * [总体架构](#总体架构)
 * [入口文件](#入口文件)
 * [访问控制](#访问控制)
 * [模块化设计](#模块化设计)
 * [自动加载](#自动加载)
 * [配置](#配置)
3. [变量](#变量)
4. [数据库](#数据库)
 * [Mysql](#mysql)
 * [Redis](#redis)
 * [Memcache](#memcache)
5. [模板](#模板)
6. [通用工具](#通用工具)
7. [字符串处理](#字符串处理)
8. [数组处理](#数组处理)
9. [Curl处理](#curl处理)
10. [发邮件](#发邮件)
11. [Excel处理](#excel处理)
12. [日志](#日志)
13. [错误和调试](#错误和调试)
14. [文件操作](#文件操作)
15. [部署](#部署)

基础
====
WesPHP框架要求PHP版本 > 5.0, 使用Mysql数据必须用PDO扩展（支持Mysql）,其他扩展根据业务需求安装，用到了再装也可以。

``` flow
st=>start: 开始
e=>end: 结束合并到测试分支
op1=>operation: 检出开发分支
op2=>operation: 开发调试
cond=>condition: 是否开发完毕？

st->op1->op2->cond
cond(yes)->e
cond(no)->op2
```

安装WesPHP
----
从GitHub下载源码包后，解压。项目中引用即可。如：
``` php
define("PATH_WESPHP", "your path"); // 框架目录，指到lib下
require_once PATH_WESPHP . "/Autoload.php";
```

目录结构
----
这里目录结构是单一项目的目录结构，大项目结构请参数样例代码和文档。WEB项目为例
```
| // 项目目录
|index.php // 入口文件
|___class // 项目类文件目录
	|___Page // 显示层
	|___Mod // 业务层
	|___Obj // 对象数据层
|___config // 项目配置文件目录
|___data // 项目数据文件目录
|___includes // 第三方类库
|___misc // 项目资源文件目录
	|___media // 媒体文件目录
	|___img // 图片文件目录
	|___script // JS脚本文件目录
	|___css // 样式文件目录
|___tpl // 网页模板文件目录
```

开发规范
----
这里只讲类文件的规范，其他变量命名就不多重复讲了。可以参考其他资料。应用在项目中的类，在框架调用的时候默认方法是run，而具体执行的方法为_do。具体请参考样例文件
类文件名和类名保持一致，且首字母必须大写。如：
``` php
class User {
	public function get() {

	}
}
```
那么与之对应的类文件名就一定要大写，即User.php。如果是放在目录下面的那文件目录名也要大写，即 User/User.php

考虑到类的方法会不断增加，框架支持将类的方法拆成文件。如：
``` php
class User_set extends User {
	protected function _do() {

	}
}
```
与之对应的方法类文件名一般为小写，即set.php。文件名请放在类文件目录下面User/set.php

快速开始
----
WEB项目需要配置重写规则，以下分别是Apache和Nginx的重写配置

Apache Rewrite配置
``` php
RewriteEngine On
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-f
RewriteRule ^/(.*) /index.php?target=$1&%{QUERY_STRING}
```
Nginx Rewrite配置
``` php
location / {
	if ( !-e $request_filename){
		rewrite "^/(.*)" /index.php?target=$1 last;
	}
}
```
入口文件
``` php
$dir = __DIR__;
define("PATH_WESPHP", $_SERVER["PATH_WESPHP"]); // 框架目录
define("PATH_ROOT", $dir); // 项目目录
define("PATH_INCLUDES", "{$dir}/includes"); // 第三方类库
define("PATH_CLASS", "{$dir}/class"); // 项目类
define("PATH_CONFIG", "{$dir}/config"); // 项目配置
define("PATH_LOG", "{$dir}/logs"); // 项目日志
define("PATH_DATA", "{$dir}/data"); // 项目数据文件目录
define("APP_ENV", "dev"); // 应用环境 dev: 开发版本 beta: Beta版本 release: 线上正式版本

require_once PATH_WESPHP . "/Autoload.php";

$app = new WesApp();
$app->run("Page");
```

架构
====
WesPHP的框架总体上遵循MVC的设计理念。框架从项目的角度，按人机交互界面是什么，具体做什么业务，底层数据是有哪些，大致划分了这三个层面。即显示层（View），业务逻辑层（Mod），数据对象层（Obj）。在这三层基础之个再加个数据访问对象（Dao）。一共四层架构。具体请参考样例。

总体架构
----
显示层（View）：人机界面交互展示方式。包括如：WEB网页形式，API接口JSON格式，命令行文本形式等

业务逻辑层（Mod）：业务实现方案。如：注册，登录等

数据对象层（Obj）：具体对象数据获取。如：用户数据获取，更新

数据访问对象（Dao）：访问数据库的方式。如：Mysql数据库连接，Redis连接等

入口文件
----
WEB网页形式入口文件
```` php
$dir = __DIR__;
define("PATH_WESPHP", $_SERVER["PATH_WESPHP"]); // 框架目录
define("PATH_ROOT", $dir); // 项目目录
define("PATH_INCLUDES", "{$dir}/includes"); // 第三方类库
define("PATH_CLASS", "{$dir}/class"); // 项目类
define("PATH_CONFIG", "{$dir}/config"); // 项目配置
define("PATH_LOG", "{$dir}/logs"); // 项目日志
define("PATH_DATA", "{$dir}/data"); // 项目数据文件目录
define("APP_ENV", "dev"); // 应用环境 dev: 开发版本 beta: Beta版本 release: 线上正式版本

require_once PATH_WESPHP . "/Autoload.php";

$app = new WesApp();
$app->run("Page");
````
访问方式：http://localhost

API形式入口文件
```` php
$dir = __DIR__;
define("PATH_WESPHP", $_SERVER["PATH_WESPHP"]); // 框架目录
define("PATH_ROOT", $dir); // 项目目录
define("PATH_INCLUDES", "{$dir}/includes"); // 第三方类库
define("PATH_CLASS", "{$dir}/class"); // 项目类
define("PATH_CONFIG", "{$dir}/config"); // 项目配置
define("PATH_LOG", "{$dir}/logs"); // 项目日志
define("PATH_DATA", "{$dir}/data"); // 项目数据文件目录
define("APP_ENV", "dev"); // 应用环境 dev: 开发版本 beta: Beta版本 release: 线上正式版本

require_once PATH_WESPHP . "/Autoload.php";

$app = new WesApp();
$app->run("Api");
````
访问方式：http://localhost

命令行形式入口文件
```` php
$dir = __DIR__;
define("PATH_WESPHP", "your path"); // 框架目录, 需要手动指定
define("PATH_ROOT", $dir); // 项目目录
define("PATH_INCLUDES", "{$dir}/includes"); // 第三方类库
define("PATH_CLASS", "{$dir}/class"); // 项目类
define("PATH_CONFIG", "{$dir}/config"); // 项目配置
define("PATH_LOG", "{$dir}/logs"); // 项目日志
define("PATH_DATA", "{$dir}/data"); // 项目数据文件目录
define("APP_ENV", "dev"); // 应用环境 dev: 开发版本 beta: Beta版本 release: 线上正式版本

require_once PATH_WESPHP . "/Autoload.php";

$app = new WesApp();
$app->run("Cli");
````
运行方式：php index.php User.get 1

守护进程形式入口文件
```` php
$dir = __DIR__;
define("PATH_WESPHP", "your path"); // 框架目录, 需要手动指定
define("PATH_ROOT", $dir); // 项目目录
define("PATH_INCLUDES", "{$dir}/includes"); // 第三方类库
define("PATH_CLASS", "{$dir}/class"); // 项目类
define("PATH_CONFIG", "{$dir}/config"); // 项目配置
define("PATH_LOG", "{$dir}/logs"); // 项目日志
define("PATH_DATA", "{$dir}/data"); // 项目数据文件目录
define("APP_ENV", "dev"); // 应用环境 dev: 开发版本 beta: Beta版本 release: 线上正式版本

require_once PATH_WESPHP . "/Autoload.php";

ini_set("default_socket_timeout", -1);
ini_set("max_execution_time", 0);

$app = new WesApp();
$app->run("Daemon");
````
运行方式： php index.php xxx.start|xxx.stop|xxx.restart

访问控制
----
无论是WEB形式，还是命令行形式，都遵循统一的访问控制方式。即其他框架中路由的概念。那么在WesPHP中是如何实现的呢？根据项目不同，可以分为WEB类和命令行类这两种。WEB类的由WEB服务器的重写功能实现路由重定向，而命令行的则是有使用者直接指定。框架支持无限级目录设计。以WEB形式举例。即，http://localhost/aa/bb/cc/dd/ee/ff/gg/....

参考WEB网页形式入口文件，$app->run("Page")。如果直接访问 [http://localhost/]，那么框架会找到class/Page/Index.php，如果访问的是 [http://localhost/user/reg]，那么框架会找到class/Page/User/User.php（reg方法在User.php存在）或class/Page/User/reg.php（reg.php方法在User.php不存在）。API接口方式一样。

命令行指定方式，如：/usr/local/php/bin/php index.php user.get 这样的方式框架会找到class/Cli/User.php（get方法在User.php存在）或class/Cli/User/get.php（get方法在User.php中不存在）。

守护进程指定方式，如：/usr/local/php/bin/php sms.start 守护进程按服务的方式启动。分为start|stop|restart的方式，一般应用场景是处理一些任务队列什么的，再复杂一些就得使用专门的框架来处理了。

模块化设计
----
大的框架设计在总体架构中已经提到了，分为View，Mod，Obj和Dao四层。WesPHP在具体的某个类中，提倡将公有方法拆成独立的文件单独维护。这样的好处是修改该方法的时候绝对不会影响到其他功能，也能防止类文件无限扩大。可提升项目的可读性和可维护性。存在的问题是影响一点效率，这个主要看项目实施者的取舍了。不过话说回来，也不会严重影响性能。本人推荐拆开的方式。

自动加载
----
框架所有类文件引用都实现的自动加载，开发都只要知道类文件在哪个目录即可。不需要先require进来，然后再使用。举个例子：如果开发知道在Obj/User.php这个类。那么可以直接 new Obj_User()。或者WesController::get("Obj_User")就可以实现。本人推荐使用WesController::get()这种方式。因为这种方式会做类对象缓存，在同一段代码中调用不会重复实例化。如果自己手工写也可以，但要注意的就比较多，初中级开发人员需要注意，重复实例化的问题。

配置
----
相关的配置文件都放在config目录中，文件名格式为xxxx.conf.php。配置文件内容如下：
``` php
return [
	"user" => "baojunbo",
	"interest" => ["movie", "PHP"],
	"info" => [
		"sex" => "male",
		"age" => 18
	]
];
```
获取配置的方式如下，其中xxxx表示配置文件名，如果WesConfig::get("xxxx.user", true)则表示获取通用配置。具体请参考样例代码
``` php
$user = WesConfig::get("xxxx.user");
$interest = WesConfig::get("xxxx.interest");
$age = WesConfig::get("xxxx.info.age");
```

变量
====
参考代码，具体方法请参考文件lib/Var.php
``` php
// GET方式
$user = WesVar::get("user", ""); // 获取一个
$get = WesVar::getx(); // 获取所有
$get = WesVar::getx("user", "age"); // 获取指定几个

// POST
$user = WesVar::post("user", ""); // 获取一个
$post = WesVar::postx(); // 获取所有
$post = WesVar::postx("user", "age"); // 获取指定几个

// REQUEST
$user = WesVar::request("user", ""); // 获取一个
$request = WesVar::requestx(); // 获取所有
$request = WesVar::requestx("user", "age"); // 获取指定几个

// SERVER
$user = WesVar::server("user", ""); // 获取一个
$server = WesVar::serverx(); // 获取所有
$server = WesVar::serverx("user", "age"); // 获取指定几个

// COOKIE
$user = WesVar::cookie("user", ""); // 获取一个
$cookie = WesVar::cookiex(); // 获取所有
$cookie = WesVar::cookiex("user", "age"); // 获取指定几个

// SESSION
$user = WesVar::session("user", ""); // 获取一个
$session = WesVar::sessionx(); // 获取所有
$session = WesVar::sessionx("user", "age"); // 获取指定几个

// FILE
$user = WesVar::file("user"); // 获取一个
$file = WesVar::filex(); // 获取所有
$file = WesVar::filex("user", "age"); // 获取指定几个
````

数据库
====
目前已经测试过的关系型数据库是Mysql，Key-Value数据是Redis和Memcache。所有的数据库访问对象必须有一个基类和具体数据库类，具体的数据库采用单例模式。

Mysql
----
``` php
// Mysql基类 class/Dao/Mysql/Mysql.php
class Dao_Mysql extends WesPdo {
    protected $_dbName; //数据库名

    public function __construct(){
        if(!$this->_dbName) throw new ErrorException('db Name is null.', 2001);
    }

    /*
     * 获取mysql服务器, 可在子类中重写
     */
    protected function _getServer($mastSlave){
    	$server = WesConfig::get("mysql.{$this->_dbName}.{$mastSlave}", true);
        return $server;
    }
}

// 某个具体库 class/Dao/Mysql/YourDB.php
class Dao_Mysql_YourDB extends Dao_Mysql {
    protected $_dbName = "yourdb";

    private static $_single = null;

    /*
     * 单例模式，连接数据库
     */
    public static function getInstance(){
        if(!self::$_single) self::$_single = new self;
        return self::$_single;
    }
}

```
需要一个Mysql配置文件, config/mysql.conf.php
``` php
$pdoParam = array(
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
);
return [
	"yourdb" => [
		"mast" => ["dsn" => "mysql:dbname=yourdb;host=127.0.0.1", "user" => "user", "pass" => "password", "params" => $pdoParam],
		"slave" => ["dsn" => "mysql:dbname=yourdb;host=127.0.0.1", "user" => "user", "pass" => "password", "params" => $pdoParam]
	]
];
```
使用方法，样例如下。具体使用方法请参考lib/Pdo.php lib/Sql.php和lib/Pagination.php
``` php
$db = Dao_Mysql_YourDB::getInstance();
$info = $db->table("user")->where(1)->find(); // 获取一条数据
$list = $db->table("user")->where("sex" => "male")->select(); // 获取满足条件的所有数据
$list = $db->table("user")->where("sex" => "male")->pselect(); // 分页获取满足条件的所有数据
// 完整的SQL样例，类thinkPHP的SQL链操作
$data = $db->table("user") // 查哪张表
	->alias("u") // 别名是什么
	->join("`user_extends` AS `ue`", "`u`.`u_id` = `ue`.`u_id`", "LEFT") // 连表 LEFT：左连 RIGHT：右连 INNER：内连
	->join("`user_xxxx` AS `ux`", "`u`.`u_id` = `ux`.`u_id`"), // 连表 默认是左连
	->field("`u`.`name`, `ue`.`sex`, `ux`.`age`") // 查哪些字段
	->where(["sex" => "male", "status" => ["in", [1, 2, 3]]]) // 查询条件
	->order("`u`.`id` DESC, `ue`.`u_id`") // 排序
	->select()
// 也支持直接SQL查询
$data = $db->get("SELECT * FROM `user` WHERE `id` = :id", ["id" => 1]); // 查一条
$data = $db->mget("SELECT * FROM `user` WHERE `id` = :id", ["id" => 1]); // 查多条
$data = $db->pget("SELECT * FROM `user` WHERE `id` = :id", ["id" => 1], ["perPage" => 20]); // 分页查，每页20条
```

Redis
----
``` php
// Redis基类 class/Dao/Redis/Redis.php
class Dao_Redis extends WesRedis{
    protected $_daoName;

    public function __construct(){
        if(!$this->_daoName) throw new ErrorException('Dao name is null.', 20001);

        $server = $this->_getServer();
        $this->_connect($server);
    }

    protected function _getServer(){
        $config = WesConfig::get("redis." . $this->_daoName, true);
        return $config;
    }
}

// 某个具体的Redis实例 class/Dao/Redis/YourRedis.php
class Dao_Redis_YourRedis extends Dao_Redis {
	protected $_daoName = "yourredis";
	protected static $_single = null;

    public static function getInstance(){
        if(!self::$_single) self::$_single = new self;
        return self::$_single;
    }
}
```
需要一个Redis配置文件，config/reids.conf.php
``` php
return [
	"yourredis" => ["host" => "127.0.0.1", "port" => 6379, "timeout" => 0.5, "password" => "7QaqfkfUjIckR"], // 通用
];
```
因为是继承了Redis类，所以原来的Redis方法可以直接使用。PHPRedis请参考https://github.com/phpredis/phpredis
``` php
$redis = Dao_Redis_YourRedis::getInstance();
$data = $redis->get("key");
```

Memcache
----
``` php
// Memcache基类 class/Dao/Memcache/Memcache.php
class Dao_Memcache extends WesMemcache{
    protected $_daoName;

    public function __construct(){
        if(!$this->_daoName) throw new ErrorException('Dao name is null.', 20001);

        $server = $this->_getServer();
        $this->_connect($server);
    }

    protected function _getServer(){
        $config = WesConfig::get("memcache." . $this->_daoName, true);
        return $config;
    }
}

// 某个具体的Redis实例 class/Dao/WesMemcache/YourWesMemcache.php
class Dao_WesMemcache_YourMC extends Dao_WesMemcache {
	protected $_daoName = "yourMC";
	protected static $_single = null;

    public static function getInstance(){
        if(!self::$_single) self::$_single = new self;
        return self::$_single;
    }
}
```
需要一个Redis配置文件，config/reids.conf.php
``` php
return [
	"yourMC" => ["host" => "127.0.0.1", "port" => 6379, "timeout" => 0.5, "password" => "7QaqfkfUjIckR"], // 通用
];
```
因为是继承了Memcache类，所以原来的Memcache方法可以直接使用。具体方法请参考Memcache扩展方法
``` php
$redis = Dao_Redis_YourMC::getInstance();
$data = $redis->get("key");
```

模板
====
本框架的模板技术采用了Smarty。Smarty的模板代码分离技术在目前已有的模板技术中相对优秀。其性能，灵活程序都不错。Smarty相关技术文档请参考https://www.smarty.net
``` php
// smarty初始化设置
WesView::setOptions(array(
	"PATH_TPL" => PATH_ROOT . "/tpl", // 模板目录
	"PATH_COMPILE" => "/tmp/smarty/compile", // 模板编译后存放目录
	// "PATH_CACHE" => "/tmp/smarty/cache", // 模板缓存目录
	// "CACHE_LIFETIME" => 10, // 缓存生命时长
	// "DELIMITER" => array("<%", "%>"), // 模板变量办界
	// "DEBUG" => true, // 是否调试
));

WesView::set("title", "WesPHP"); // 设置标题
WesView::display("index.html");
```
详情的代码，请参考样例

通用工具
====
框架提供了一些基础的并常用的工具方法，可以直接调用。如有其他通用的，欢迎提供。
``` php
WesUtil::p($var, false); // 打印
WesUtil::ip(); // 获取IP地址
```

字符串处理
====
使用方法
``` php
WesString::sub($str, $start, $len, $suffix = "...", $encoding = "utf8"); // 字符串截取
$encodeStr = WesString::crypt("hi", $key = "key", "encode"); // 加密
$decodeStr = WesString::crypt($encodeStr, $key = "key", "decode"); // 解密
WesString::isEmail("17217733@qq.com"); // 邮箱验证
WesString::isMobile("18612539881"); // 手机验证
WesString::isUrl("https://github.com/baojunbo/WesPHP"); // URL验证
WesString::isIp("127.0.0.1"); // IP地址验证
WesString::base64Encode("string"); // BASE64加密
WesString::base64Decode("string"); // BASE64解密
WesString::mobileFormat("18612539881"); // 手机格式化
WesString::getInitial("中国"); // 获取首字母
```

数组处理
====
使用方法
``` php
$arr = [
	["id" => 1, "name" => "baojunbo", "age" => 18],
	["id" => 2, "name" => "lisi", "age" => 28],
	["id" => 3, "name" => "zhangsan", "age" => 38]
];
WesArray::multisort($arr, ["id", SORT_DESC], ["age", SORT_ASC]); // 多维数组排序
$newArr = WesArray::resetIndex($arr, "id"); // 重设数组索引
$str = WesArray::encrypt($arr); // 数组加密
$arr = WesArray::decrypt($str); // 数组解密
```

Curl处理
====
使用方法
``` php
// 单调
WesCurl::setData(array('id' => 1, 'name' => 'baojunbo'), "GET");
WesCurl::setOption(CURLOPT_RETURNTRANSFER, false);
WesCurl::setCookie('key', 'value');
WesCurl::setUserPass('user', 'password');
$content = WesCurl::call('http://www.xxx.com/xxx/xx.php', 1);

// 多调
$urls = array(
	'sina' => array(
		'url' => 'http://www.sina.com.cn',
		'method' => 'GET'
	),
	'baidu' => array(
		'url' => 'http://www.baidu.com',
		'method' => 'POST'
	),
	'google' => array(
		'url' => 'http://www.google.com',
		'method' => 'GET',
		'data' => array('q' => 'hello world'),
	)
);
$contents = WesCurl::call($urls, 0.1);
```

发邮件
====
使用方法
``` php
$mail = new WesMail();
$mail->setSMTP("smtp.qiye.163.com", "user", "password");
$mail->setFrom("service@yourdomain.com", "发件邮箱名称");
$mail->setTo($staffers);
$subject = "邮件主题";
$body = "邮件主体内容";
$mail->sendMail($subject, $body);
```

Excel处理
====
使用方法。框架封装了PHPExcel相关文章请参考 https://github.com/PHPOffice/PHPExcel
``` php
$excel = new WesExcel;
$list = $excel->read("/tmp/a.xlsx"); // 读取电子表格数据
```

日志
====
使用方法。开发者根据场景，选择一个即可。
``` php
WesLog::info("log string");
WesLog::error("log string");
WesLog::notice("log string");
WesLog::warning("log string");
WesLog::fatal("log string");
WesLog::debug("log string");
```

错误和调试
====
自定义错误信息收集，以便及时发现程序运行中的问题
``` php
// 设置自定义错误收集方法，在程序开始时设置
set_error_handler(array('WesError', 'setError'));

// 设置调试
WesDebug::$debug = true;
WesDebug::start("key");
代码片段...
WesDebug::setDebugInfo("key", "infoKey", $infoData);
WesDebug::end($key);
```

文件操作
====
使用方法
``` php
// 读取文件内容
$content = WesFile::read("/tmp/a.txt");

// 读取文件目录
$files = WesFile::read("/tmp");

// 写文件 最后一个参数 a：表示追加 w：表示覆盖写入
WesFile::write("/tmp/a.txt", "内容", "a");

// 复制文件
WesFile::cp("/tmp/a.txt", "/tmp/a_copy.txt");

// 复制目录
WesFile::cpDir("/tmp/a", "/tmp/a_copy");

// 移动文件
WesFile::mv("/tmp/a.txt", "/tmp/a/a.txt");

// 删除文件
WesFile::del("/tmp/a.txt");
```

部署
====
第一步：将框架上传到服务器。小技巧：不推荐将框架代码与项目放在同一目录下面，可以将框架代码放在项目外。如果框架有更新了，只需要更新一处即可。如果放在项目内就更新N个项目。推荐放在WEB公共目录，并在WEB服务器环境变量中配置，如：
``` php
// Apache配置方法
SetEnv PATH_WESPHP /data/web/WesPHP/lib

// Nginx配置方法
fastcgi_param PATH_WESPHP	"/data/web/WesPHP/lib";
```
第二步：设置WEB服务器站点配置，启用Rewrite
``` php
// Apache Rewrite配置
RewriteEngine On
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-f
RewriteRule ^/(.*) /index.php?target=$1&%{QUERY_STRING}

// Nginx Rewrite配置
location / {
	if ( !-e $request_filename){
		rewrite "^/(.*)" /index.php?target=$1 last;
	}
}
```

第三步：上传项目代码