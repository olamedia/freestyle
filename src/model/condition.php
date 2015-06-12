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

class condition{
	const is = 1;
	const not = 2;
	const lt = 3;
	const lte = 4;
	const gt = 5;
	const gte = 6;
	const _and = 7;
	const _or = 8;
	private $_left = null;
	private $_op = null;
	private $_right = null;
	public function __construct($left, $op, $right){
		$this->_left = $left;
		$this->_op = $op;
		$this->_right = $right;
	}
	public static function create($left, $op, $right){
		return new self($left, $op, $right);
	}
	public function getLeft(){
		return $this->_left;
	}
	public function getOp(){
		return $this->_op;
	}
	public function getRight(){
		return $this->_right;
	}
	public function _and($right){
		return condition::create($this, condition::_and, $right);
	}
	public function _or($right){
		return condition::create($this, condition::_or, $right);
	}
}
