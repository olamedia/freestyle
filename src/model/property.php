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

class property{
	protected $_model = null;
	protected $_name = null;
	protected $_fieldName = null;
	public function __construct($model, $name, $fieldName){
		$this->_model = $model;
		$this->_name = $name;
		$this->_fieldName = $fieldName;
	}
	public function getValue(){
		return $this->_model[$this->_name];
	}
	public function setValue($value){
		$this->_model[$this->_name] = $value;
	}
	
    public function strval(){
    	return strval($this->getValue());
    }
	public function floatval(){
    	return floatval($this->getValue());
    }
    public function intval(){
    	return intval($this->getValue());
    }
    public function boolval(){
    	$v = $this->getValue();
    	return is_bool($v)?$v:(!!intval($this->getValue()));
    }
	public function html(){
		return \htmlspecialchars($this->strval());
	}
	public function preLoad(){
		throw new \Exception("Use postLoad instead");
	}
	public function preInsert(){
		//echo $this->_name."->preInsert()<br />";
	}
	public function preUpdate(){
		//echo $this->_name."->preUpdate()<br />";
	}
	public function preSave(){
		//echo $this->_name."->preSave()<br />";
	}
	public function preDelete(){
		//echo $this->_name."->preDelete()<br />";
	}
	public function postLoad(){
		//echo $this->_name."->postLoad()<br />";
	}
	public function postInsert(){
		//echo $this->_name."->postInsert()<br />";
	}
	public function postUpdate(){
		//echo $this->_name."->postUpdate()<br />";
	}
	public function postSave(){
		//echo $this->_name."->postSave()<br />";
	}
	public function postDelete(){
		//echo $this->_name."->postDelete()<br />";
	}
}
