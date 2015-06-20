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

class resultIterator implements \Iterator{
	private $_result = null;
	private $_current = null;
	private $_key = 0;
	public function __construct($result){
		$this->_result = $result;
	}
	private function _fetch(){
		$this->_current = $this->_result->fetch();
		return $this->_current;
	}
	public function rewind(){
		$this->_result->reset();
		$this->_key = 0;
		return $this->_fetch();
	}
	public function current(){
		return $this->_current;
	}
	public function key(){
		return $this->_key;
	}
	public function next(){
		$this->_key++;
		return $this->_fetch();
	}
	public function valid(){
		return ($this->current() !== false) && ($this->current() !== null);
	}
}