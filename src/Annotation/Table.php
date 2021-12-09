<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Table
{
    /**
     * 客户端名称.
     *
     * @var string|null
     */
    public $client;

    /**
     * 表名称.
     *
     * @var string
     */
    public $name;

    /**
     * 数据库名.
     *
     * @var string
     */
    public $database;

    /**
     * 是否为超级表.
     *
     * @var bool
     */
    public $super = true;
}
