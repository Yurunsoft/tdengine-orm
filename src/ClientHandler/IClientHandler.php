<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\ClientHandler;

use Yurun\TDEngine\Orm\Contract\IQueryResult;

interface IClientHandler
{
    /**
     * 查询.
     */
    public function query(string $sql, ?string $name = null): IQueryResult;
}
