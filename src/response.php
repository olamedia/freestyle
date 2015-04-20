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

class response{
    private static $_statusMessages = array(
        100 => 'Continue', 101 => 'Switching Protocols',
        200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authorative Information',
        204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',
        300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found',
        303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Temporary Redirect',
        400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required',
        403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict', 410 => 'Gone',
        411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type', 416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway',
        503 => 'Service Unavailable', 504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported'
        );
    private static $_callbacks = array();
    public static function on($code, $callback = null){
        if (null === $callback){
            if (isset(self::$_callbacks[$code])){
                self::$_callbacks[$code]();
            }
        }else{
            self::$_callbacks[$code] = $callback;
        }
    }
    public static function setStatus($code){
        \header('Status: '.$code.(isset(self::$_statusMessages[$code])?self::$_statusMessages[$code]:''));
        self::on($code);
    }
    public static function ok(){
        self::setStatus(200);
    }
    private static function _preventLoop($location){
        if (request::isGet() && 
            (isset(request::$server['HTTP_REFERER']) && $location == request::$server['HTTP_REFERER'])) &&
            $location == request::getUri()){
            self::setStatus(500);
        }
    }
    public static function redirect($location, $code = 303){
        self::_preventLoop($location);
        \ignore_user_abort(true);
        \header('Location: '.$location, true, $code);
        self::on($code);
    }
    public static function movedPermanently($location){
        self::redirect($location, 301);
    }
    public static function seeOther($location){
        self::redirect($location, 303);
    }
    public static function notModified(){
        self::setStatus(304);
    }
    public static function unauthorized(){
        self::setStatus(401);
    }
    public static function forbidden(){
        self::setStatus(403);
    }
    public static function notFound(){
        self::setStatus(404);
    }
}
