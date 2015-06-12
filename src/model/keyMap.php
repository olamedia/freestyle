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

class keyMap{
	private $_map = [];
	private $_primary = [];
	private $_auto = [];
	private function _unpack(){
		foreach ($this->_map as $fieldName => $a){
			if (isset($a['primaryKey'])){
				$this->_primary[] = $fieldName;
			}
			if (isset($a['auto'])){
				$this->_auto[] = $fieldName;
			}
		}
	}
	public function set($keyMap){
		$this->_map = $keyMap;
		$this->_unpack();
		return $this;
	}
	public function get(){
		return $this->_map;
	}
	public function getPrimary(){
		return $this->_primary;
	}
	public function getAuto(){
		return $this->_auto;
	}
}




