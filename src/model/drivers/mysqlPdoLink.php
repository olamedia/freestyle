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
//;charset=UTF8
class mysqlPdoLink extends link{
	private static $_prepared = [];
	protected $_dsn;
	protected $_username = null;
	protected $_password = null;
	protected $_options = [];
	protected $_link = null;

	public function __construct($dsn, $username = null, $password = null, $options = []){
		parent::__construct();
		$this->_dsn = $dsn;
		$this->_username = $username;
		$this->_password = $password;
		$this->_options = $options;
	}
	public static function create($dsn, $username = null, $password = null, $options = []){
		return new self($dsn, $username, $password, $options);
	}
	public function connect(){
		if (null === $this->_link){
			try{
				$this->_link = new \PDO($this->_dsn, $this->_username, $this->_password, $this->_options);
				$this->_link->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}catch(\PDOException $e){
				throw $e;
			}
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
				$valueSubs[] = 'NULL';
			}else{
				$valueSubs[] = '?';//'$'.(++$n);
				$values[] = $v;
			}
		}
		foreach ($aa as $k){
			if (!isset($aa[$k])){
				$keys[] = $k;
				$valueSubs[] = 'NULL';
			}
		}
		$tableName = $storage->getTableName();
		$stKey = 'insert/'.$tableName.'/'.\implode(',', $keys);
		$sql = 'INSERT INTO '.$tableName.' ('.\implode(',', $keys).') VALUES ('.\implode(', ', $valueSubs).')';
		$stmt = $this->getLink()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
		if ($stmt->execute($values)){
			if (count($aa)){
				$inc = \reset($aa);
				$model[$inc] = $this->_link->lastInsertId();
			}
			$this->_setModelSaved($model);
		}
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
			$seta[] = $k.' = ?';//.(++$n);
			$values[] = $a[$k];
		}
		$sql .= ' SET '.\implode(', ', $seta);
		$wa = [];
		foreach ($pka as $k){
			$wa[] = $k.' = ?';//.(++$n);
			$values[] = $a[$k];
		}
		$sql .= ' WHERE '.\implode(' AND ', $wa);
		$stmt = $this->getLink()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
		if ($stmt->execute($values)){
			$this->_setModelSaved($model);
		}
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
			$sql .= '?';//.(++$n);
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
		return $result->fetch(\PDO::FETCH_ASSOC);
	}
	public function query($query){
		$values = [];
		$sql = $this->_getSql($query, $values);
		$stKey = $sql;
		$stmt = $this->getLink()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
		if ($stmt->execute($values)){
			return $stmt;
		}
		return null;
	}
	public function queryCount($query){
		$values = [];
		$sql = $this->_getSql($query, $values, true);
		$stKey = $sql;
		$stmt = $this->getLink()->prepare($sql, [\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY]);
		if ($stmt->execute($values)){
			$result = $stmt;
			$a = $stmt->fetch(\PDO::FETCH_ASSOC);
			return \reset($a);
		}
		return null;
	}
	public function getSql($query){
		$values = [];
		return $this->_getSql($query, $values);
	}
	private function _getSql($query, &$values, $count = false){
		$storage = $query->getStorage();
		$tableName = $storage->getTableName();
		$sa = $query->getWhat();
		$n = 0;
		$values = [];
		$sql = 'SELECT ';
		if ($count){
			$sql .= 'COUNT(*)';
		}else{
			if (!count($sa)){
				$sql .= '*';
			}else{
				//$sql .= implode(',', );
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
