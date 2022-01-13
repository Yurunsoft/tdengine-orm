<?php

declare(strict_types=1);

use Yurun\TDEngine\Orm\Test\TestUtil;
use Yurun\TDEngine\TDEngineManager;

require dirname(__DIR__) . '/vendor/autoload.php';

// init
date_default_timezone_set('Asia/Shanghai');
TDEngineManager::setClientConfig('test', TestUtil::getClientConfig());
TDEngineManager::setClientConfig('test-extension', TestUtil::getExtensionClientConfig());
TDEngineManager::setDefaultClientName('test');
TDEngineManager::getClient()->sql('CREATE DATABASE IF NOT EXISTS device KEEP 365 DAYS 10 BLOCKS 6 UPDATE 1');
