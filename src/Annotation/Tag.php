<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Annotation;

/**
 * @Annotation
 *
 * @Target("PROPERTY")
 */
class Tag
{
    /**
     * 名称.
     *
     * @var string
     */
    public $name;

    /**
     * 数据类型.
     *
     * @var string
     */
    public $type;

    /**
     * 数据长度.
     *
     * @var int
     */
    public $length = 0;
}
