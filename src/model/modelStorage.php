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

class modelStorage{
	private static $_registry = [];
	public function register($className){
		self::$_registry[$className] = $this;
		return $this;
	}
	public static function get($className){
		return self::$_registry[$className];
	}
	protected $_link = null;
	protected $_tableName = null;
	protected $_keyMap = null;
	public function __construct($link, $tableName = null){
		$this->_link = $link;
		$this->_tableName = $tableName;
		$this->_keyMap = new keyMap();
	}
	public static function create($link, $tableName = null){
		return new self($link, $tableName);
	}
	public function setLink($link){
		$this->_link = $link;
		return $this;
	}
	public function getLink(){
		return $this->_link;
	}
	public function setTableName($tableName){
		$this->_tableName = $tableName;
		return $this;
	}
	public function getTableName(){
		return $this->_tableName;
	}
	public function setKeyMap($keyMap){
		$this->_keyMap->set($keyMap);
		return $this;
	}
	public function getKeyMap(){
		return $this->_keyMap;
	}
	public function insert($model){
		$this->_link->insert($this, $model);
	}
	public function update($model){
		$this->_link->update($this, $model);
	}
}
