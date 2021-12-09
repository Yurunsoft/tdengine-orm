<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Enum;

/**
 * 数据类型.
 */
class DataType
{
    public const TIMESTAMP = 'TIMESTAMP';

    public const INT = 'INT';

    public const BIGINT = 'BIGINT';

    public const FLOAT = 'FLOAT';

    public const DOUBLE = 'DOUBLE';

    public const BINARY = 'BINARY';

    public const SMALLINT = 'SMALLINT';

    public const TINYINT = 'TINYINT';

    public const BOOL = 'BOOL';

    public const NCHAR = 'NCHAR';

    private function __construct()
    {
    }
}
