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

class result implements \IteratorAggregate, \Countable{
	private $_query = null;
	private $_result = null;
	public function __construct($query){
		$this->_query = $query;
	}
	public function toArray(){
		
	}
	public function reset(){
		$storage = $this->_query->getStorage();
		$link = $storage->getLink();
		$this->_result = $link->query($this->_query);
	}
	public function fetch(){
		$storage = $this->_query->getStorage();
		$link = $storage->getLink();
		return $link->fetch($this->_result);
	}
	public function getIterator(){
        return new resultIterator($this);
    }
	public function count(){
		$storage = $this->_query->getStorage();
		$link = $storage->getLink();
		return $link->queryCount($this->_query);
	}
}
