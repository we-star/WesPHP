<?php
/**
 * 中大联合数据库
 */
class Dao_Mysql_ZdlhDB extends Dao_Mysql {
    protected $_dbName = "zdlh_car";

    private static $_single = null;

    /*
     * 单例模式，连接数据库
     */
    public static function getInstance(){
        if(!self::$_single) self::$_single = new self;
        return self::$_single;
    }
}