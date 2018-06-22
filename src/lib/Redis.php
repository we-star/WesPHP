<?php
/**
 * WesPHP2.0
 * Redis
 */
abstract class WesRedis {
	private $_redids;
	private $_redisServer;
	private $_pconnect;

	public function __call($method, $params){
		if ($this->_redids) {
			try {
				return call_user_func_array(array($this->_redids, $method), $params);
			} catch (RedisException $e) {
				$this->_connect($this->_redisServer, $this->_pconnect);
				return false;
			}
		} else {
			return false;
		}
	}

	protected function _connect($redisServer, $pconnect = false) {
		$redis = new Redis();
		if (!$pconnect) {
			$res = $redis->connect($redisServer['host'], $redisServer['port'], $redisServer['timeout']);
		} else {
			$res = $redis->pconnect($redisServer['host'], $redisServer['port'], $redisServer['timeout']);
		}

		if (isset($redisServer['password']) && $redisServer['password']) {
			$redis->auth($redisServer['password']);
		}

		if(isset($redisServer['db']) && $redisServer['db']){
			$redis->select($redisServer['db']);
		}

		if ($res) {
			$this->_redisServer = $redisServer;
			$this->_pconnect = $pconnect;
			$this->_redids = $redis;
		}
	}
}
