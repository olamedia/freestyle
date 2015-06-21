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

class query implements \IteratorAggregate, \Countable{
	const desc = 0;
	const asc = 1;
	public function getIterator(){
        return $this->getResult();
    }
	public function count(){
		return count($this->getResult());
	}
	private $_storage = null;
	private $_className = null;
	private $_what = [];
	private $_where = [];
	private $_order = [];
	public function __construct($storage, $className){
		$this->_storage = $storage;
		$this->_className = $className;
	}
	public function getClassName(){
		return $this->_className;
	}
	public function getStorage(){
		return $this->_storage;
	}
	public function select($what){
		if (\is_object($what)){
			if ($what instanceof condition){
				return $this->where($what);
			}
		}
		$this->_what[] = $what;
		return $this;
	}
	public function getWhat(){
		return $this->_what;
	}
	public function where($condition){
		$this->_where[] = $condition;
		return $this;
	}
	public function getWhere(){
		return $this->_where;
	}
	public function orderBy($statement, $order){
		$this->_order[] = [$statement, $order];
		return $this;
	}
	public function asc($statement){
		return $this->orderBy($statement, self::asc);
	}
	public function desc($statement){
		return $this->orderBy($statement, self::desc);
	}
	public function getOrder(){
		return $this->_order;
	}
	public function delete(){
		return $this->_storage->getLink()->queryDelete($this);
	}
	public function getResult(){
		return $this->_storage->getLink()->select($this);
	}
	public function toArray(){
		return $this->getResult()->toArray();
	}
	public function getSql(){
		return $this->_storage->getLink()->getSql($this);
	}
}


