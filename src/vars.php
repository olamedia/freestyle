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

class vars implements \ArrayAccess{
    private $_defaults = array();
    private $_values = array();
    public function __construct($defaults = array(), $values = array()){
        $this->_defaults = $defaults;
        $this->_values = $values;
    }
    public function setDefaults($defaults = array()){
        $this->_defaults = $defaults;
    }
    public function setValues($values = array()){
        $this->_values = $values;
    }
    public function set($key, $value = null){
        $this->_values[$key] = $value;
    }
    public function offsetSet($offset, $value){
        $this->set($offset, $value);
    }
    public function remove($key){
        unset($this->_defaults[$key]);
        unset($this->_values[$key]);
    }
    public function offsetUnset($offset){
        $this->remove($offset);
    }
    public function exists($key){
        return \array_key_exists($key, $this->_values) || \array_key_exists($key, $this->_defaults);
    }
    public function offsetExists($offset){
        return $this->exists($offset);
    }
    public function get($key, $default = null){
        return \array_key_exists($key, $this->_values)?$this->_values[$key]:(\array_key_exists($key, $this->_defaults)?$this->_defaults[$key]:$default);
    }
    public function offsetGet($offset){
		return $this->get($offset);
	}
}
