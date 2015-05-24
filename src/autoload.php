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

// This three should not be overloaded
require_once(__DIR__.'/loader/autoloadManager.php');
require_once(__DIR__.'/loader/classMapLoader.php');
require_once(__DIR__.'/loader/modulesLoader.php');

modulesLoader::create(__DIR__, 'freestyle');




