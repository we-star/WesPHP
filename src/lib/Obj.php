<?php
/**
 * WesPHP2.0
 * Obj
 */
abstract class WesObj {
	private static $_class = array();

	public function __call($method, $params){
		$class = get_class($this);
		$class .= "_{$method}";

		if (!class_exists($class)) {
			$classArr = explode("_", $class);
			$position = count($classArr) - 2;
			unset($classArr[$position]);
			$class = join("_", $classArr);
		}

		if (empty(self::$_class[$class])) self::$_class[$class] = new $class;
		return call_user_func_array(array(self::$_class[$class], "run"), $params);
	}

	public static function __callStatic($method, $params) {
		$class = get_called_class();
		$class .= "_{$method}";
		return call_user_func_array(array($class, 'run'), $params);
	}
}
