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

class path{
    protected $_segments = null;
    protected $_isAbsolute = false;
    protected $_isClosed = false;
    public function __construct($path = ''){
        $path = strval($path);
        $this->_segments = \explode('/', $path);
        $this->_segments = \array_map('rawurldecode', $this->_segments);
        if (\count($this->_segments)){
            if ('' === \reset($this->_segments)){
                $this->_isAbsolute = true;
            }
            if ('' === \end($this->_segments)){
                $this->_isClosed = true;
            }
        }
        if (\count($this->_segments) && '' === \reset($this->_segments)){
            \array_shift($this->_segments);
        }
        if (\count($this->_segments) && '' === \end($this->_segments)){
            \array_pop($this->_segments);
        }
    }
    public function isClosed(){
        return $this->_isClosed;
    }
    public function setClosed($isClosed = true){
        $this->_isClosed = $isClosed;
        return $this;
    }
    public function isAbsolute(){
        return $this->_isAbsolute;
    }
    public function setAbsolute($isAbsolute = true){
        $this->_isAbsolute = $isAbsolute;
        return $this;
    }
    public function first(){
        return isset($this->_segments[0])?$this->_segments[0]:null;
    }
    public function getSegments(){
        return $this->_segments;
    }
    public function setSegments($segments){
        return $this->_segments = $segments;
    }
    public function matchBase($path){
        $base = new self(strval($path));
        $a = $this->getSegments();
        $b = $base->getSegments();
        $i = 0;
        if (!count($b)){
            return true;
        }
        while (isset($b[$i])){
            if (!isset($a[$i]) || $a[$i] != $b[$i]){
                return false;
            }
            $i++;
        }
        return true;
    }
    public function sub($path){
        $base = new self(strval($path));
        $sub = new self();
        $a = $this->getSegments();
        $b = $base->getSegments();
        $s = array();
        $i = 0;
        while (isset($a[$i]) && isset($b[$i]) && $a[$i] == $b[$i]){
            $sub->setAbsolute(false);
            $i++;
        }
        $sub->setSegments(\array_slice($a, $i));
        if ($i){
            $sub->setAbsolute(false);
        }
        $sub->setClosed($this->isClosed());
        return $sub;
    }
    public function rel($path){
        $path = new self(strval($path));
        $path->setSegments(\array_merge($this->getSegments(), $path->getSegments()));
        $path->setAbsolute($this->isAbsolute());
        return $path;
    }
    public function __toString(){
        if (!\count($this->_segments)){
            return ($this->_isAbsolute||$this->_isClosed?'/':'');
        }
        return ($this->_isAbsolute?'/':'').\implode('/', \array_map('rawurlencode', $this->_segments)).($this->_isClosed?'/':'');
    }
}
