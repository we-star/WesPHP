<?php
/**
 * WesPHP2.0
 * Memcache
 */
abstract class WesMemcache {
	private static $_mc = array();

	public function __call($method, $params){
		$this->_check();
		return call_user_func_array(array(self::$_mc[$this->_daoName], $method), $params);
	}

	public function mget($keys) {
		$this->_check();
		$data = self::$_mc[$this->_daoName]->getMulti($keys);
		return $data;
	}

	protected function _connect($servers) {
		if (!isset(self::$_mc[$this->_daoName])) {
			foreach ($servers as $key => $server) {
				if (empty($server[0]) || empty($server[1])) unset($servers[$key]);
			}
			if (!$servers) throw new ErrorException("Error Processing Request", 2001);
			$mc = new Memcached();
			$bool = $mc->addServers($servers);
			if ($bool) {
				$mc->setOption(Memcached::OPT_NO_BLOCK, true);
				$mc->setOption(Memcached::OPT_CONNECT_TIMEOUT, 200);
				$mc->setOption(Memcached::OPT_POLL_TIMEOUT, 50);
				self::$_mc[$this->_daoName] = $mc;
			} else {
				throw new ErrorException("Memcached add servers failed!", 2002);
			}
		}
	}

	private function _check() {
		if (empty(self::$_mc[$this->_daoName]) || !self::$_mc[$this->_daoName] instanceof Memcached) {
			throw new ErrorException("Memcached is not connected!", 2003);
		}
	}
}
