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

class route{
    private $_requestPath = null;
    private $_basePath = null;
    private $_relativePath = null;
    private $_action = null;
    private $_ext = null;
    public function __construct($parentRoute = null){
        if (null === $parentRoute){
            $urla = explode('?', request::getUri());
            $this->_requestPath = new path($urla[0]);
        }else{
            $this->_requestPath = $parentRoute->getRequestPath();
            $this->setBasePath($parentRoute->getBasePath());
        }
    }
    public function setRequestPath($path){
        $this->_requestPath = new path($path);
        $this->_updateAction();
    }
    public function setBasePath($path){
        $this->_basePath = new path($path);
        $this->_updateAction();
    }
    public function setRelativePath($path){
        $this->_relativePath = new path($path);
    }
    public function setAction($action){
        $this->_action = $action;
    }
    public fucntion getRequestPath(){
        return $this->_requestPath;
    }
    public fucntion getBasePath(){
        return $this->_basePath;
    }
    public fucntion getRelativePath(){
        return $this->_relativePath;
    }
    public fucntion getAction(){
        return $this->_action;
    }
    public function rel($path = ''){
        return $this->_basePath->rel($path);
    }
    public function arel($path = ''){
        return $this->rel($this->_action?$this->_action:'')->rel($path);
    }
    private function _updateAction(){
        if (null === $this->_relativePath || null === $this->_basePath){
            return;
        }
        $this->_relativePath = $this->_requestPath->sub($this->_basePath);
		$this->_action = $this->_relativePath->first();
        $a = \explode('.', $this->_action);
        if (count($a) > 1){
            $this->_ext = \array_pop($a);
            $this->_action = \implode('.', $a);
        }
    }
    public function rewrite($requestPath, $basePath){
		$this->_requestPath = new path($requestPath);
		$this->setBasePath($basePath);
    }
    public function nextSegment(){
        $this->setBasePath($this->arel());
    }
}

