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

class propertyMap{
	private $_map = [];
	private $_fields = [];
	private $_classes = [];
	private function _unpack(){
		foreach ($this->_map as $propertyName => $a){
			if (isset($a['field'])){
				$this->fields[$propertyName] = $a['field'];
			}
			if (isset($a['class'])){
				$this->_classes[$propertyName] = $a['class'];
			}
		}
	}
	public function set($propertyMap){
		$this->_map = $propertyMap;
		$this->_unpack();
		return $this;
	}
	public function get(){
		return $this->_map;
	}
	public function getField($propertyName){
		return isset($this->_fields[$propertyName])?$this->_fields[$propertyName]:null;
	}
	public function getClass($propertyName){
		return isset($this->_classes[$propertyName])?$this->_classes[$propertyName]:null;
	}
}




