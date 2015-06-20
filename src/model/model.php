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

class model implements \ArrayAccess, \IteratorAggregate{
	public function __construct($g = [], $fromDb = false){
		$this->_data = $g;
		$this->_fromDb = $fromDb;
		$this->_isDraft = !$fromDb;
	}
	public function __get($name){
		$storage = modelStorage::get(\get_called_class());
		$map = $storage->getPropertyMap();
		$field = $map->getField($name);
		if (null === $field){
			$field = $name;
		}
		$class = $map->getClass($name);
		if (null === $class){
			$class = __NAMESPACE__.'\\property';
		}
		return new $class($this, $name, $field);
	}
	public function __set($name, $value){
		$this->__get($name)->setValue($value);
	}
	public function offsetSet($offset, $value){
        if (null === $offset){
            $this->_data[] = $value;
        }else{
            $this->_data[$offset] = $value;
        }
		$this->_changedKeys[$offset] = $offset;
		$this->_isDraft = true;
    }
    public function offsetExists($offset){
        return \array_key_exists($offset, $this->_data);
    }
    public function offsetUnset($offset){
        unset($this->_data[$offset]);
    }
    public function offsetGet($offset){
        return \array_key_exists($offset, $this->_data)?$this->_data[$offset]:null;
    }
	public function getIterator(){
        return new \ArrayIterator($this->_data);
    }
	public function toArray(){
		return $this->_data;
	}
	public function insert(){
		if (!$this->_isDraft){
			return;
		}
		if ($this->_getStorage()->insert($this)){
			$this->_fromDb = true;
			$this->_isDraft = false;
			$this->_changedKeys = [];
		}
	}
	public function update(){
		if (!$this->_isDraft){
			return;
		}
		if ($this->_getStorage()->update($this)){
			$this->_isDraft = false;
			$this->_changedKeys = [];
		}
	}
	public function save(){
		if ($this->_fromDb){
			$this->update();
		}else{
			$this->insert();
		}
	}
	/*public static function q($sql){
		$storage = modelStorage::get(\get__called_class());
		return $storage->q($sql);
	}*/
	public static function all(){
		return modelStorage::get(\get_called_class());
	}
	
	
	
	private $_data = null;
	private $_changedKeys = [];
	private $_isDraft = true;
	private $_fromDb = false;
	private function _setFromDb($fromDb = true){
		$this->_fromDb = $fromDb;
		$this->_isDraft = false;
		return $this;
	}
	private function _getKeys(){
		return \array_keys($this->_data);
	}
	private function _getValues(){
		return \array_values($this->_data);
	}
	private function _resetChangedKeys(){
		$this->_changedKeys = [];
	}
	private function _getChangedKeys(){
		return \array_keys($this->_changedKeys);
	}
	private function _getStorage(){
		return modelStorage::get(\get_class($this));
	}	
}
