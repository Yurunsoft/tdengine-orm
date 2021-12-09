# tdengine-orm

[![Latest Version](https://poser.pugx.org/yurunsoft/tdengine-orm/v/stable)](https://packagist.org/packages/yurunsoft/tdengine-orm)
![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/Yurunsoft/tdengine-orm/ci/dev)
[![Php Version](https://img.shields.io/badge/php-%3E=7.0-brightgreen.svg)](https://secure.php.net/)
[![License](https://img.shields.io/github/license/Yurunsoft/tdengine-orm.svg)](https://github.com/Yurunsoft/tdengine-orm/blob/master/LICENSE)

## 简介

基于 [tdengine-restful-connector](https://github.com/Yurunsoft/tdengine-restful-connector) 开发的 TDEngine ORM。

支持创建超级表、创建表、批量插入数据。

此项目支持在 PHP >= 7.0 的项目中使用。

支持在 ThinkPHP、Laravel、[Swoole](https://github.com/swoole/swoole-src)、[imi](https://github.com/imiphp/imi) 等项目中使用

在 Swoole 环境中支持协程化，不会阻塞！

技术支持 QQ 群: 17916227[![点击加群](https://pub.idqqimg.com/wpa/images/group.png "点击加群")](https://jq.qq.com/?_wv=1027&k=5wXf4Zq)，如有问题可以及时解答和修复。

## 安装

`composer require yurunsoft/tdengine-orm`

## 使用

**使用连接管理器：**

```php
// 增加名称为 test 的连接配置
\Yurun\TDEngine\TDEngineManager::setClientConfig('test', new \Yurun\TDEngine\ClientConfig([
    // 'host'            => '127.0.0.1',
    // 'hostName'        => '',
    // 'port'            => 6041,
    // 'user'            => 'root',
    // 'password'        => 'taosdata',
    // 'ssl'             => false,
    // 'timestampFormat' => \Yurun\TDEngine\Constants\TimeStampFormat::LOCAL_STRING,
    // 'keepAlive'       => true,
]));
// 设置默认数据库为test
\Yurun\TDEngine\TDEngineManager::setDefaultClientName('test');
// 获取客户端对象（\Yurun\TDEngine\Client）
$client = \Yurun\TDEngine\TDEngineManager::getClient();
```

**定义模型：**

```php
<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Test\Model;

use Yurun\TDEngine\Orm\Annotation\Field;
use Yurun\TDEngine\Orm\Annotation\Table;
use Yurun\TDEngine\Orm\Annotation\Tag;
use Yurun\TDEngine\Orm\BaseModel;
use Yurun\TDEngine\Orm\Enum\DataType;

/**
 * @Table(name="device_log", database="device")
 */
class DeviceLogModel extends BaseModel
{
    /**
     * @Field(type=DataType::TIMESTAMP)
     *
     * @var int
     */
    public $time;

    /**
     * @Tag(type=DataType::NCHAR, length=32, name="device_id")
     *
     * @var string
     */
    public $deviceId;

    /**
     * @Field(type=DataType::FLOAT)
     *
     * @var float
     */
    public $voltage;

    /**
     * @Field(type=DataType::FLOAT, name="electric_current")
     *
     * @var float
     */
    public $electricCurrent;
}
```

**创建超级表：**

```php
DeviceLogModel::createSuperTable();
```

**创建表：**

```php
$table = '表名';
$deviceId = '00000001'; // 这是 TAGS
DeviceLogModel::createTable($table, [$deviceId]);
```

**插入数据：**

```php
$record = new DeviceLogModel([
    // 初始化模型数据
], '表名');
// $record->xxx = xxx; // 设置一些字段值
$record->insert();
```

**批量插入数据：**

```php
$record1 = new DeviceLogModel([
    // 初始化模型数据
], '表名1');
$record2 = new DeviceLogModel([
    // 初始化模型数据
], '表名2');
DeviceLogModel::batchInsert([$record1, $record2]);
```
