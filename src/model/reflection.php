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

class reflection{
	private static $_classes = [];
	private static $_methods = [];
	public static function getClass($className){
		if (!isset(self::$_classes[$className])){
			self::$_classes[$className] = new \ReflectionClass($className);
		}
		return self::$_classes[$className];
	}
	public static function getClassMethod($className, $methodName){
		$k = $className.'.'.$methodName;
		if (!isset(self::$_methods[$k])){
			self::$_methods[$k] = self::getClass($className)->getMethod($methodName);
			self::$_methods[$k]->setAccessible(true);
		}
		return self::$_methods[$k];
	}
	public static function invokeArgs($className, $methodName, $object, $args = []){
		$m = self::getClassMethod($className, $methodName);
		return $m->invokeArgs($object, $args);
	}
}	
	