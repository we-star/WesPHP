<?php
/**
 * WesPHP2.0
 * autoload
 */
class WesAutoload {
	private static $_classes = array(); // 类

	/**
	 * 加载类文件
	 */
	public static function load($class) {
		$dirSeparator = DIRECTORY_SEPARATOR;
		$dir = str_replace(array('_', '/', '\\'), $dirSeparator, $class);
		if (isset(self::$_classes[$class])) {
			$file = self::$_classes[$class];
		} else {
			$isWesPHPClass = false;
			$isProject = self::_isProject($class);
			$Wesphp = stripos($class, "Wes");
			if ($Wesphp !== false) {
				$isWesPHPClass = true;
				$dir = str_replace("Wes", $dirSeparator, $dir);
				$file = PATH_WESPHP . "{$dir}.php";
			} else if ($isProject){
				$isWesPHPClass = true;
				// 更改类目录大小写
				$dirArr = explode($dirSeparator, $dir);
				$endDir = end($dirArr);
				foreach($dirArr as &$v) {
					if ($v != $endDir) $v = ucfirst($v);
				}
				$dir = join($dirSeparator, $dirArr);
				if (defined("PATH_COMMON_CLASS") && (stripos($class, "Obj") !== false || stripos($class, "Mod") !== false || stripos($class, "Dao") !== false)){
					$dirName = PATH_COMMON_CLASS . "{$dirSeparator}{$dir}";
					$file = "{$dirName}{$dirSeparator}{$endDir}.php";
					if (!is_file($file)) $file = "{$dirName}.php";
					if (!is_file($file)) {
						$dirName = PATH_CLASS . "{$dirSeparator}{$dir}";
						$file = "{$dirName}{$dirSeparator}{$endDir}.php";
					}
				} else {
					$dirName = PATH_CLASS . "{$dirSeparator}{$dir}";
					$file = "{$dirName}{$dirSeparator}{$endDir}.php";
				}

				// 当前目录找
				if (!is_file($file)) $file = "{$dirName}.php";
				// 向上找
				if (!is_file($file)) {
					$dirArr = explode($dirSeparator, $dirName);
					$position = count($dirArr) - 1;
					unset($dirArr[$position]);
					$file = join($dirSeparator, $dirArr) . ".php";
				}
				// 大写字母开头的文件
				if (!is_file($file)) {
					$ucFirstEnddir = ucfirst($endDir);
					$dirName = str_replace($endDir, $ucFirstEnddir, $dirName);
					$file = "{$dirName}{$dirSeparator}" . $ucFirstEnddir . ".php";
				}
			}
		}

		if (isset($file)) {
			if (!is_file($file) && $isWesPHPClass) {
				// throw new ErrorException("class '{$class}' not exists!", 1001);
				return;
			}
			require_once $file;
		}
	}

	/**
	 * 是否是项目类
	 */
	private static function _isProject($class) {
		// "Lt"， “Aop”, "Alipay", "Sign" 过滤支付宝第三方类库
		$classPrefix = array("Smarty_Internal_", "PHPExcel", "Facebook\\", "Lt", "Aop", "Alipay", "Sign");
		$isTrue = true;
		foreach($classPrefix as $prefix) {
			if (stripos($class, $prefix) === 0) {
				$isTrue = false;
				break;
			}
		}
		return $isTrue;
	}
}

spl_autoload_register(array("WesAutoload", "load"));
