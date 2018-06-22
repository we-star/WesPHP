<?php
/**
 * 中大联合
 */
class Dao_Redis_Common extends Dao_Redis {
	protected $_daoName = "common";
	protected static $_single = null;

    public static function getInstance(){
        if(!self::$_single) self::$_single = new self;
        return self::$_single;
    }
}