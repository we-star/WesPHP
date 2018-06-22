<?php
/**
 * 网页基类
 */
abstract class Page extends WesObj {
	protected $_hasUI = true; // 拥有界面
	protected $_checkLogin = true; // 检查登录

	protected $_tpl = ""; // 网页模板
	protected $_title = ""; // 网页标题

	protected $_cacheLifeTime = 10; // 页面缓存生命时长，默认10秒

	protected $_userInfo = array(); // 用户信息
	protected $_jumpUrl = true; // 未登录跳转登录界面
	protected $_gourl; // 登录后要跳转的页面

	protected $_device = "pc"; // 默认是PC

	public function __construct() {
		$this->_gourl = WesVar::server("REQUEST_URI");

		if ($this->_hasUI) {
			// smarty初始化设置
			WesView::setOptions(array(
				"PATH_TPL" => PATH_ROOT . "/tpl", // 模板目录
				"PATH_COMPILE" => "/tmp/smarty/compile/zdlh_front", // 模板编译后存放目录
				"PATH_CACHE" => "/tmp/smarty/cache/zdlh_front", // 模板缓存目录
				"CACHE_LIFETIME" => $this->_cacheLifeTime, // 缓存生命时长
				// "DELIMITER" => array("<%", "%>"), // 模板变量办界
				// "DEBUG" => true, // 是否调试
			));
		}
	}

	/**
	 * 运行
	 * @return [type] [description]
	 */
	public function run() {
		header("Content-type: text/html;charset=utf-8");
		$this->_getDevice();
		$this->_checkLogin(); // 检查是否登录
		$this->_do(); // 具体实现

		if ($this->_hasUI) {
			$website = WesConfig::get("misc.website");
			WesView::set("title", $website["title"] . $this->_title); // 设置标题
			WesView::set("static", $website[WesApp::$appEnv]["static"]); // 静太文件URL
			WesView::set("css_version", $website["css_version"]);
			WesView::set("js_version", $website["js_version"]);
			WesView::display("{$this->_device}/{$website['ui_version']}/{$this->_tpl}");
		}
	}

	/**
	 * 请在子类重写
	 */
	protected function _do() {
	}

	/**
	 * 检查是否登录，子类可重写
	 */
	protected function _checkLogin() {
		if ($this->_checkLogin) {
			$user = WesVar::cookie("user", "", true);
			$userInfo = WesArray::decrypt($user);
			if ($userInfo) {
				ob_start();
				$domain = WesVar::server("HTTP_HOST");
				setcookie("user", $user, WesApp::$now + 1800, "/", $domain);
				$this->_userInfo = $userInfo;
				WesView::set("user", $userInfo);
			} else {
				if ($this->_jumpUrl) {
					$gourl = WesString::base64encode($this->_gourl);
					header("location: /login?url={$gourl}");
				}
			}
		}
	}

	/**
	 * 获取设备信息
	 * @return [type] [description]
	 */
	private function _getDevice() {
		$userAgent = WesVar::server("HTTP_USER_AGENT");
		$userAgent = strtolower($userAgent);
		if (stripos($userAgent, "iphone") !== false || stripos($userAgent, "android")) {
			$this->_device = "h5";
			if (stripos($userAgent, "micromessenger") !== false) {
				// $this->_isWeiXin = true;
			}
		}
	}
}