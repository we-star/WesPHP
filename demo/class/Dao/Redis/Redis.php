<?php
/**
 * Redis
 */
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