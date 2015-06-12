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

class field{
	private $_storage = null;
	private $_fieldName = null;
	public function __construct($storage, $fieldName){
		$this->_storage = $storage;
		$this->_fieldName = $fieldName;
	}
	public function getName(){
		return $this->_fieldName;
	}
	public function is($right){
		return condition::create($this, condition::is, $right);
	}
	public function not($right){
		return condition::create($this, condition::not, $right);
	}
	public function lt($right){
		return condition::create($this, condition::lt, $right);
	}
	public function lte($right){
		return condition::create($this, condition::lte, $right);
	}
	public function gt($right){
		return condition::create($this, condition::gt, $right);
	}
	public function gte($right){
		return condition::create($this, condition::gte, $right);
	}
	public function in($right){
		return condition::create($this, condition::in, $right);
	}
	
}

