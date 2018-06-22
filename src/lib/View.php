<?php
/**
 * WesPHP2.0
 * View
 */
$dir = dirname(__DIR__);
require_once $dir . "/Smarty-3.1.21/Smarty.class.php";

class WesView {
	private static $_smarty = null;
	private static $_options = array();

	public static function setOptions($key, $val = null) {
		if (is_array($key) && $key) {
			foreach($key as $k => $v) {
				self::$_options[$k] = $v;
			}
		} else if ($val) {
			self::$_options[$key] = $val;
		}
	}

	public static function set($key, $val = null){
		$smarty = self::_smarty();
		if (is_array($key) && $key) {
			foreach($key as $k => $v) {
				$smarty->assign($k, $v);
			}
		} else {
			$smarty->assign($key, $val);
		}
	}

	public static function display($tpl) {
		$smarty = self::_smarty();
		$smarty->display($tpl);
	}

	public static function fetch($tpl) {
		$smarty = self::_smarty();
		return $smarty->fetch($tpl);
	}

	private static function _smarty() {
		if (!self::$_smarty) {
			$smarty = new Smarty;
			if (isset(self::$_options["PATH_TPL"])) $smarty->setTemplateDir(self::$_options["PATH_TPL"]);
			if (isset(self::$_options["PATH_COMPILE"])) $smarty->setCompileDir(self::$_options["PATH_COMPILE"]);
			if (isset(self::$_options["PATH_CACHE"])) $smarty->setCacheDir(self::$_options["PATH_CACHE"]);
			if (isset(self::$_options["PATH_CONFIG"])) $smarty->setConfigDir(self::$_options["PATH_CONFIG"]);
			if (isset(self::$_options["CACHE_LIFETIME"])) {
				$smarty->caching = true;
				$smarty->cache_lifetime = self::$_options["CACHE_LIFETIME"];
			}
			if (isset(self::$_options["DELIMITER"])) {
				$smarty->left_delimiter = self::$_options["DELIMITER"][0];
				$smarty->right_delimiter = self::$_options["DELIMITER"][1];
			}
			if (isset(self::$_options["DEBUG"])) $smarty->debugging = self::$_options["DEBUG"];
			self::$_smarty = $smarty;
		} else {
			$smarty = self::$_smarty;
		}
		return $smarty;
	}
}
