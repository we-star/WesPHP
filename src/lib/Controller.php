<?php
/**
 * WesPHP2.0
 * Controller
 */
class WesController {
	private static $_classes = array();

	public static function get($classMethod, $args = null) {
		if (!$classMethod) throw new ErrorException("class is empty!", 1002);

		if ($args !== null) {
			$noArges = false;
			$funcNum = func_num_args();
			if ($funcNum > 2) {
				$args = func_get_args();
				unset($args[0]);
			} else {
				if (!is_array($args)) {
					$args = (array)$args;
				} else {
					$args = array($args);
				}
			}
		} else {
			$args = array();
			$noArges = true;
		}

		// 带点的情况
		$method = "run";
		if (stripos($classMethod, ".") !== false) {
			$classMethod = explode('.', $classMethod);
			$class = &$classMethod[0];
			$method = end($classMethod);
		} else {
			$class = $classMethod;
			if (!class_exists($class)) {
				$lastPostion = strripos($class, "_");
				$method = substr($class, $lastPostion + 1);
				$class = substr($class, 0, $lastPostion);
			}
		}

		if (!isset(self::$_classes[$class])) {
			self::$_classes[$class] = new $class;
		}


		if (!$noArges) {
			return call_user_func_array(array(self::$_classes[$class], $method), $args);
		} else {
			return self::$_classes[$class];
		}
	}
}
