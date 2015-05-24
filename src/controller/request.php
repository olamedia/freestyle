<?php

/*
 * This file is part of the freestyle package.
 * Copyright (c) 2015 olamedia <olamedia@gmail.com>
 *
 * This source code is release under the MIT License.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace freestyle;

class request{
    public static $server = null;
    public static $get = null;
    public static $post = null;
    public static $vars = null;
    public static function isCli(){
        return 'cli' === PHP_SAPI;
    }
    public static function isAjax(){
        return 'XMLHttpRequest' == self::$server['HTTP_X_REQUESTED_WITH'];
    }
    public static function getHttpHeader($name){
        return self::$server['HTTP_'.\strtoupper(\strtr($name, '-', '_'))];
    }
    public static function isPost(){
        return 'POST' === self::getMethod();
    }
    public static function isGet(){
        return 'GET' === self::getMethod();
    }
    public static function isPut(){
        return 'PUT' === self::getMethod();
    }
    public static function isDelete(){
        return 'DELETE' === self::getMethod();
    }
    public static function getMethod(){
        return self::$server['REQUEST_METHOD'];
    }
    public static function getHost(){
        return isset(self::$server['HTTP_HOST'])?self::$server['HTTP_HOST']:self::$server['SERVER_NAME'];
    }
    public static function getDomainName(){
        $host = self::getHost();
        if (0 === \strpos($host, 'www.')){
            return \substr($host, 4);
        }
        return $host;
    }
    public static function getUri(){
        return isset(self::$server['DOCUMENT_URI'])?self::$server['DOCUMENT_URI']:self::$server['REQUEST_URI'];
    }
    public static function getReferer(){
        return self::$server['HTTP_REFERER'];
    }
    public static function getUseragent(){
        return self::$server['HTTP_USER_AGENT'];
    }
    public static function reset(){
        self::$server = new vars($_SERVER);
        self::$get = new vars($_GET);
        self::$post = new vars($_POST);
        self::$vars = new vars(new vars(self::$get, self::$post));
    }
}
