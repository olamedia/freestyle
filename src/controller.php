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

// FIXME another draft version
class controller{
    protected $_app = null;
    protected $_parent = null;
    protected $_parents = array();
    
    protected $_route = null;
    
    protected $_options = array();
    public function __get($name){
        return isset($this->_options[$name])?$this->_options[$name]:null;
    }
    public function getRoute(){
        return $this->_route;
    }
    public function __construct($parent = null, $options = array()){
        if ($parent){
            $this->_app = $parent->app();
        }else{
            $this->_app = $this;
        }
        $this->_parent = $parent;
        $this->_options = $options;
        if (null !== $parent){
            $this->_parents[get_class($parent)] = $parent;
            $this->_route = new route($parent->getRoute());
        }else{
            $this->_route = new route();
        }
    }
    public static function run($base = '/', $options = array()){
        $c = new static(null, $options);
        $c->getRoute()->setBasePath($base);
        return $c->route();
    }
    public function runController($controllerClass, $options = array(), $mergeOptions = true){
        $c = new $controllerClass($this, $mergeOptions?\array_merge($this->_options, $options):$options);
        $c->getRoute()->setBasePath($this->arel());
        return $c->route();
    }
    public function rel($path = ''){
        return $this->getRoute()->rel($path);
    }
    public function arel($path = ''){
        return $this->getRoute()->arel($path);
    }
    public function url(){
        return 'http://'.request::getHost().$this->getRoute()->getRequestPath();
    }
    public function app(){
        return $this->_app;
    }
    protected function _header(){
        //response::sendHeaders();
        if ($this->_parent){
            $this->_parent->_header();
        }
        if (\method_exists($this, 'header')){
            $this->header();
        }
    }
    protected function _footer(){
        if (\method_exists($this, 'footer')){
            $this->footer();
        }
        if ($this->_parent){
            $this->_parent->_footer();
        }
    }
    public static function getActionController($parent, $action){
        // TODO
    }
    public static function setActionController($parent, $action, $class){
        // TODO
    }
    private $_forceNotFound = false;
    public function notFound(){
        $this->_forceNotFound = true;
        if ($this->_parent){
            $this->_parent->notFound();
        }
    }
    public function preRoute(){
    }
    protected function _getArgs($methodName, $predefinedArgs = array()){
        $me = new \ReflectionClass(get_class($this));
        $method = $me->getMethod($methodName);
        $parameters = $method->getParameters();
        $args = array();
        foreach ($parameters as $p){
            $name = $p->getName();
            if (isset($predefinedArgs[$name])){
                $value = $predefinedArgs[$name];
            }elseif (isset($_GET[$name])){
                $value = $_GET[$name];
            }elseif (isset($_POST[$name])){
                $value = $_POST[$name];
            }else{
                $value = $p->isDefaultValueAvailable()?$p->getDefaultValue():null;
            }
            $args[] = $value; //$name
        }
        return $args;
    }
    public function nextSegment(){
        $this->getRoute()->nextSegment();
    }
    public function route($path){
        $this->_basePath = new path($path);
        if (!$this->_requestPath->matchBase($this->_basePath)){
            return false; // leave for other apps
        }
        $action = $this->getRoute()->getAction();
        $a = \explode('.', $this->_action);
        if (count($a) > 1){
            $this->_ext = \array_pop($a);
            $this->_action = \implode('.', $a);
        }
        $this->preRoute();
        
        if ($controller = self::getActionController(get_class($this), $this->_action)){
            $this->runController($controller);
            if (!$this->_parent){
                exit;
            }
            return true;
        }
        $initMethod = 'init';
        $showMethod = 'show';
        $methodFound = false;
        if (null !== $this->_action){
            $uc = \ucfirst($this->_action);
            $initMethod = 'init'.$uc;
            $showMethod = 'show'.$uc;
        }
        if (\method_exists($this, $initMethod)){
            $methodFound = true;
            \call_user_func_array(array($this, $initMethod), $this->_getArgs($initMethod));
        }
        if (\method_exists($this, $showMethod)){
            $methodFound = true;
            \session_write_close();
            $this->_header();
            \call_user_func_array(array($this, $showMethod), $this->_getArgs($showMethod));
            $this->_footer();
        }
        if (!$methodFound){
            $initMethod = 'action';
            if (\method_exists($this, $initMethod)){
                $methodFound = true;
                \call_user_func_array(array($this, $initMethod), $this->_getArgs($initMethod));
            }
        }
        if ($this->_forceNotFound){
            $methodFound = false;
        }
        if ($methodFound){
            if (!$this->_parent){
                exit;
            }
            return true;
        }
        return false;
    }
}

