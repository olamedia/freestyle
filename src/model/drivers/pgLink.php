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
	// http://php.net/manual/en/function.pg-connect.php
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
	private static $_opa = [
		1 => '=',
		2 => '!=',
		3 => '<',
		4 => '<=',
		5 => '>',
		6 => '>=',
		7 => 'AND',
		8 => 'OR',
	];
	private function _value($v, &$n, &$values){
		$sql = '';
		if ($v instanceof field){
			$sql .= $v->getName();
		}else{
			$sql .= '$'.(++$n);
			$values[] = $v;
		}
		return $sql;
	}
	private function _condition($c, &$n, &$values){
		$sql = '';
		$l = $c->getLeft();
		$op = $c->getOp();
		$r = $c->getRight();
		$lv = null;
		if ($l instanceof condition){
			$sql .= $this->_condition($l, $n, $values); 
		}else{
			$sql .= $this->_value($l, $n, $values); 
		}
		$sql .= ' '.self::$_opa[$op].' ';
		if ($r instanceof condition){
			$sql .= $this->_condition($r, $n, $values); 
		}else{
			$sql .= $this->_value($r, $n, $values); 
		}
		return $sql;
	}
	private function _order($o, &$n, &$values){
		$sql = '';
		list($v, $d) = $o;
		if ($v instanceof condition){
			$sql .= $this->_condition($v, $n, $values); 
		}else{
			$sql .= $this->_value($v, $n, $values); 
		}
		if (query::asc === $d){
			$sql .= ' ASC';
		}else{
			$sql .= ' DESC';
		}
		return $sql;
	}
	public function select($query){
		return new result($query);
	}
	public function fetch($result){
		return \pg_fetch_assoc($result);
	}
	public function delete($query){
		$values = [];
		$sql = $this->_getSql($query, $values, false, true);
		$stKey = $sql;
		if (!isset(self::$_prepared[$stKey])){
			\pg_prepare($this->getLink(), $stKey, $sql);
		}
		$q = \pg_execute($this->getLink(), $stKey, $values);
		return $q;
	}
	public function query($query){
		$values = [];
		$sql = $this->_getSql($query, $values, false);
		$stKey = $sql;
		if (!isset(self::$_prepared[$stKey])){
			\pg_prepare($this->getLink(), $stKey, $sql);
		}
		$q = \pg_execute($this->getLink(), $stKey, $values);
		return $q;
	}
	public function queryCount($query){
		$values = [];
		$sql = $this->_getSql($query, $values, true);
		$stKey = $sql;
		if (!isset(self::$_prepared[$stKey])){
			\pg_prepare($this->getLink(), $stKey, $sql);
		}
		$q = \pg_execute($this->getLink(), $stKey, $values);
		$r = \pg_fetch_assoc($q);
		return \reset($r);
	}
	public function getSql($query){
		$values = [];
		return $this->_getSql($query, $values);
	}
	private function _getSql($query, &$values, $count = false, $delete = false){
		$storage = $query->getStorage();
		$tableName = $storage->getTableName();
		$sa = $query->getWhat();
		$n = 0;
		$values = [];
		$sql = $delete?'DELETE':'SELECT ';
		if (!$delete){
			if ($count){
				$sql .= 'COUNT(*)';
			}else{
				if (!count($sa)){
					$sql .= '*';
				}else{
					//$sql .= implode(',', );
				}
			}
		}
		$sql .= ' FROM '.$tableName;
		$wa = $query->getWhere();
		if (count($wa)){
			$sql .= ' WHERE ';
			$sqla = [];
			foreach ($wa as $c){
				$sqla[] = $this->_condition($c, $n, $values);
			}
			$sql .= \implode(' AND ', $sqla);
		}
		if (!$count){
			$oa = $query->getOrder();
			if (count($oa)){
				$sql .= ' ORDER BY ';
				$sqla = [];
				foreach ($oa as $o){
					$sqla[] = $this->_order($o, $n, $values);
				}
				$sql .= \implode(', ', $sqla);
			}
		}
//		var_dump($sql, $values);
		return $sql;
	}
}
