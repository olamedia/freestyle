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
    public function getRoute(){
        return $this->_route;
    }
    public function nextSegment(){
        $this->getRoute()->nextSegment();
    }
    public function app(){
        return $this->_app;
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
    protected function _header(){
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
    private $_found = false;
    public function found($methodName = null){
        if (\method_exists($this, $methodName)){
            $this->_found = true;
            \call_user_func_array(array($this, $methodName), $this->_getArgs($methodName));
            return true;
        }
        return false;
    }
    public function notFound(){
        $this->_found = false;
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
            if (isset($_POST[$name])){
                $value = $_POST[$name];
            }elseif (isset($_GET[$name])){
                $value = $_GET[$name];
            }elseif (isset($predefinedArgs[$name])){
                $value = $predefinedArgs[$name];
            }else{
                $value = $p->isDefaultValueAvailable()?$p->getDefaultValue():null;
            }
            $args[] = $value; //$name
        }
        return $args;
    }
    private function _exists($methodName){
        return \method_exists($this, $methodName);
    }
    public static function run($base = '/', $options = array()){
        $c = new static(null, $options);
        $c->getRoute()->setBasePath($base);
        return $c->route();
    }
    public function runController($controllerClass, $options = array(), $mergeOptions = true){
        $c = new $controllerClass($this, $mergeOptions?\array_merge($this->_options, $options):$options);
        $this->_found = true;
        $c->getRoute()->setBasePath($this->arel());
        return $c->route();
    }
    private function _updateCanonical($methodName, $prefix){
        $o = new \ReflectionObject($this);
        try{
            $m = $o->getMethod($methodName);
            if ($m){
                $action = preg_replace('#^'.$prefix.'#is', '', $m->name);
                $this->getRoute()->setCanonical($this->rel($action));
            }
        }catch(\Exception $e){
        }
    }
    public function route(){
        if (!$this->getRoute()->match()){
            return false; // leave for other apps
        }
        $action = $this->getRoute()->getAction();
        $this->preRoute();
        if ($controller = self::getActionController(get_class($this), $action)){
            return $this->runController($controller);
        }
        $initMethod = 'init';
        $showMethod = 'show';
        if (null !== $action){
            $uc = \ucfirst($action);
            $initMethod = 'init'.$uc;
            $showMethod = 'show'.$uc;
        }
        if ($this->_exists($initMethod) || $this->_exists($showMethod)){
            if ($this->_exists($initMethod)){
                $this->_updateCanonical($initMethod, 'init');
            }
            if ($this->_exists($showMethod)){
                $this->_updateCanonical($showMethod, 'show');
            }
            if ($this->_exists($initMethod)){
                $this->found($initMethod)
            }
            if ($this->_exists($showMethod)){
                \session_write_close();
                $this->_header();
                $this->found($showMethod);
                $this->_footer();
            }
        }elseif (null !== $action){
            $this->found('action');
        }
        if ($this->_found){
            if (!$this->_parent){
                exit;
            }
        }
    }
    public function __get($name){
        return isset($this->_options[$name])?$this->_options[$name]:null;
    }
}

