<?php
/**
 * 自定义错误日志
 */
class WesError {
    public static function setError($errNo, $errMsg, $fileName, $lineNum, $vars){
        $errorType = array(
        	E_ERROR => 'Error',
        	E_WARNING => 'Warning',
        	E_NOTICE => 'Notice',
        	E_PARSE => 'Parsing Error',
        	E_CORE_ERROR => 'Core Error',
        	E_CORE_WARNING => 'Core Warning',
        	E_COMPILE_ERROR => 'Compile Error',
        	E_COMPILE_WARNING => 'Compile Warning',
        	E_USER_ERROR => 'User Error',
        	E_USER_WARNING => 'User Warning',
        	E_USER_NOTICE => 'User Notice',
        	E_STRICT => 'Strict',
        	E_RECOVERABLE_ERROR => 'Recoverable Error',
        	E_DEPRECATED => 'Deprecated',
        );

        WesLog::error("{$errNo}:{$errorType[$errNo]} [{$errMsg}] {$fileName} {$lineNum}");
    }
}