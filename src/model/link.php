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

class link{
	private static $_instances = [];
	private static $_ai = 0;
	private $_id = 0;
	private $_keyMap = null;
	public function __construct(){
		$this->_id = ++self::$_ai;
		$this->_keyMap = new keyMap();
	}
	public function getId(){
		return $this->_id;
	}
	public function setName($name){
		self::$_instances[$name] = $this;
		return $this;
	}
	public static function getInstance($name){
		return self::$_instances[$name];
	}
	public function getStorage($tableName){
		return modelStorage::create($this, $tableName);
	}
	public function register($className, $tableName = null, $keyMap = []){
		$this->getStorage($tableName)
			->register($className)
			->setKeyMap($keyMap)
			;
		//storageManager::getInstance()[$className] = $storage;
		return $this;
	}
	protected function _setModelSaved($model){
		reflection::invokeArgs('freestyle\\model', '_setFromDb', $model, [true]);//$model->setFromDb(true);
		reflection::invokeArgs('freestyle\\model', '_resetChangedKeys', $model, []);//$model->resetChangedKeys();
	}
}
	
	
	
	
	