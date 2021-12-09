<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Meta;

class MetaManager
{
    /**
     * 元数据集合.
     *
     * @var \Yurun\TDEngine\Orm\Meta\Meta[]
     */
    private static $metas = [];

    private function __construct()
    {
    }

    /**
     * 获取模型元数据.
     *
     * @return \Yurun\TDEngine\Orm\Meta\Meta
     */
    public static function get(string $className): Meta
    {
        if (isset(self::$metas[$className]))
        {
            return self::$metas[$className];
        }

        return self::$metas[$className] = new Meta($className);
    }
}
