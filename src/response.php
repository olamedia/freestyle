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
}
