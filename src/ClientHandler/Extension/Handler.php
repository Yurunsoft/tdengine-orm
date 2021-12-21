<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\ClientHandler\Extension;

use Swoole\Coroutine;
use TDengine\Connection;
use Yurun\TDEngine\Orm\ClientHandler\IClientHandler;
use Yurun\TDEngine\Orm\Contract\IQueryResult;
use Yurun\TDEngine\TDEngineManager;

class Handler implements IClientHandler
{
    /**
     * @var bool
     */
    private $haveSwoole;

    /**
     * @var Connection|null
     */
    private $connection;

    public function __construct()
    {
        $this->haveSwoole = class_exists(Coroutine::class);
    }

    private function getConnection(?string $clientName = null): Connection
    {
        if (!($this->haveSwoole && Coroutine::getCid() >= 0) && $this->connection)
        {
            return $this->connection;
        }
        $config = TDEngineManager::getClientConfig($clientName);
        if (!$config)
        {
            throw new \RuntimeException(sprintf('Client %s config does not found', $clientName));
        }
        $db = $config->getDb();
        $connection = new Connection($config->getHost(), $config->getPort(), $config->getUser(), $config->getPassword(), '' === $db ? null : $db);
        $connection->connect();

        return $this->connection = $connection;
    }

    /**
     * 查询.
     */
    public function query(string $sql, ?string $clientName = null): IQueryResult
    {
        return new QueryResult($this->getConnection($clientName)->query($sql));
    }
}
