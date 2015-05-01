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
    public function setRequestPath($path){
        $this->_requestPath = new path($path);
    }
    public function setBasePath($path){
        $this->_basePath = new path($path);
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
}

