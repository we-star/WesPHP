<?php
/**
 * Mysql基类
 */
class Dao_Mysql extends WesPdo {
    protected $_dbName; //数据库名

    public function __construct(){
        if(!$this->_dbName) throw new ErrorException('db Name is null.', 2001);
    }

    /*
     * 获取mysql服务器, 可在子类中重写
     */
    protected function _getServer($mastSlave){
        $appEnv = WesApp::$appEnv;
		$server = WesConfig::get("{$appEnv}/mysql.{$this->_dbName}", true);
        return $server;
    }
}