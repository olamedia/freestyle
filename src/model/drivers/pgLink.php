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

class pgLink extends link{
	private static $_prepared = [];
	protected $_connectionString;
	protected $_connectType;
	protected $_link = null;
	public function __construct($connection_string, $connect_type = 0){
		parent::__construct();
		$this->_connectionString = $connection_string;
		$this->_connectType = $connect_type;
	}
	public static function create($connection_string, $connect_type = 0){
		return new self($connection_string, $connect_type);
	}
	public function connect(){
		if (null === $this->_link){
			$this->_link = \pg_connect($this->_connectionString, $this->_connectType);
		}
		return $this;
	}
	public function getLink(){
		$this->connect();
		return $this->_link;
	}
	public function insert($storage, $model){
		$a = $model->toArray();
		$keyMap = $storage->getKeyMap();
		$pka = $keyMap->getPrimary();
		$aa = $keyMap->getAuto();
		$keys = [];
		$valueSubs = [];
		$values = [];
		$n = 0;
		foreach ($a as $k => $v){
			$keys[] = $k;
			if (\in_array($k, $aa)){
				$valueSubs[] = 'DEFAULT';
			}else{
				$valueSubs[] = '$'.(++$n);
				$values[] = $v;
			}
		}
		foreach ($aa as $k){
			if (!isset($aa[$k])){
				$keys[] = $k;
				$valueSubs[] = 'DEFAULT';
			}
		}
		$tableName = $storage->getTableName();
		$stKey = 'insert/'.$tableName.'/'.\implode(',', $keys);
		$sql = 'INSERT INTO '.$tableName.' ('.\implode(',', $keys).') VALUES ('.\implode(', ', $valueSubs).')';
		if (count($aa)){
			$sql .= ' RETURNING '.implode(',', $aa);
		}
		if (!isset(self::$_prepared[$stKey])){
			\pg_prepare($this->getLink(), $stKey, $sql);
		}
		$q = \pg_execute($this->getLink(), $stKey, $values);
		$aa = \pg_fetch_assoc($q);
		foreach ($aa as $k => $v){
			$model[$k] = $v;
		}
		$this->_setModelSaved($model);
	}
	public function update($storage, $model){
		$a = $model->toArray();
		$tableName = $storage->getTableName();
		$keyMap = $storage->getKeyMap();
		$pka = $keyMap->getPrimary();
		$changedKeys = reflection::invokeArgs('freestyle\\model', '_getChangedKeys', $model, []);
		$stKey = 'update/'.$tableName.'/'.\implode(',', $changedKeys);
		$n = 0;
		$values = [];
		$sql = 'UPDATE '.$tableName.'';
		$seta = [];
		foreach ($changedKeys as $k){
			$seta[] = $k.' = $'.(++$n);
			$values[] = $a[$k];
		}
		$sql .= ' SET '.\implode(', ', $seta);
		$wa = [];
		foreach ($pka as $k){
			$wa[] = $k.' = $'.(++$n);
			$values[] = $a[$k];
		}
		$sql .= ' WHERE '.\implode(' AND ', $wa);
		if (!isset(self::$_prepared[$stKey])){
			\pg_prepare($this->getLink(), $stKey, $sql);
		}
		$q = \pg_execute($this->getLink(), $stKey, $values);
		$this->_setModelSaved($model);
	}
}
