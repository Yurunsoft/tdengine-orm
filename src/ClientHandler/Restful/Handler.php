<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\ClientHandler\Restful;

use Yurun\TDEngine\Orm\ClientHandler\IClientHandler;
use Yurun\TDEngine\Orm\Contract\IQueryResult;
use Yurun\TDEngine\TDEngineManager;

class Handler implements IClientHandler
{
    /**
     * 查询.
     */
    public function query(string $sql, ?string $name = null): IQueryResult
    {
        return new QueryResult(TDEngineManager::getClient($name)->sql($sql));
    }
}
