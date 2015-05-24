<?php

/*
 * This file is part of the freestyle package.
 * Copyright (c) 2012 olamedia <olamedia@gmail.com>
 *
 * This source code is release under the MIT License.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace freestyle;

/**
 * modulesLoader
 * Modules class map autoloader
 * ::create(__DIR__.'/modules', 'my application')
 *
 * @package freestyle
 * @subpackage loader
 * @author olamedia
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class modulesLoader{
	protected $_path = null;
	protected $_namespace = null;
	protected $_options = array(
			'bootstrap' => 'module.php',
		);
	protected $_modulesPreloaded = false;
	protected $_modulesLoaded = false;
	protected static $_modules = array();
	public static function __construct($path, $namespace = null){
		$this->_path = $path;
		 = $namespace;
		autoloadManager::getInstance()->registerModulesLoader(array($this, 'preloadModules'));
		autoloadManager::getInstance()->registerModuleLoader(array($this, 'loadModules'));
	}
	public static function create(){
		$args = \func_get_args();
		$class = new \ReflectionClass(\get_called_class());
		$constructor = $class->getConstructor();
		if ($constructor->isPublic()){
			$instance = $class->newInstanceArgs($args);
		}else{
			$constructor->setAccessible(true);
			$instance = $class->newInstanceWithoutConstructor();
			$constructor->invokeArgs($instance, $args);
		}
		return $instance;
	}	
	public function loadModules(){
		if ($this->_modulesLoaded){
			return;
		}
		$this->_modulesLoaded = true;
		foreach (self::$_modules as $name => $f){
			$autoload = array();
			$map = include $f; // FIXME do automatic check for invalid code
			if (\is_array($autoload) && \count($autoload)){ // $autoload = array(); // classmap
				classMapLoader::create($autoload, \dirname($f));
			}
			if (\is_array($map) && \count($map)){ // second option: return $classMap;
				classMapLoader::create($map, \dirname($f));
			}
		}
	}
	public function preloadModules(){
		if ($this->_modulesPreloaded){
			return;
		}
		$this->_modulesPreloaded = true;
        foreach (\glob($this->_path.\DIRECTORY_SEPARATOR.'*') as $d){
            if (\is_dir($d)){
                $base = basename($d);
				$first = \substr($d, 0, 1);
                if (\in_array($first, array('.', '_'))){
                    continue;
                }
				$f = $d.\DIRECTORY_SEPARATOR.$this->_options['bootstrap'];
                if (\is_file($f)){
					self::$_modules[(null===$this->_namespace?'\\':$this->_namespace.'\\').$base] = $f;
                }
            }
        }
    }
}


