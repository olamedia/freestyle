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
 
$autoload = [
	'freestyle\\model' => 'model.php',
	
	'freestyle\\property' => 'property.php',
	'freestyle\\stringProperty' => 'properties/stringProperty.php',
	'freestyle\\creationTimestampProperty' => 'properties/creationTimestampProperty.php',
	'freestyle\\modificationTimestampProperty' => 'properties/modificationTimestampProperty.php',
	
	'freestyle\\keyMap' => 'keyMap.php',
	'freestyle\\propertyMap' => 'propertyMap.php',
	
	'freestyle\\modelStorage' => 'modelStorage.php',
	'freestyle\\field' => 'field.php',
	'freestyle\\condition' => 'condition.php',
	
	'freestyle\\query' => 'query.php',
	'freestyle\\result' => 'result.php',
	'freestyle\\resultIterator' => 'resultIterator.php',
	
	'freestyle\\reflection' => 'reflection.php',
	
	'freestyle\\link' => 'link.php',
	'freestyle\\pgLink' => 'drivers/pgLink.php',
	'freestyle\\mysqlPdoLink' => 'drivers/mysqlPdoLink.php',
];
