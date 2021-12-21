<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\ClientHandler\Extension;

use TDengine\Resource;
use Yurun\TDEngine\Orm\Contract\IQueryResult;

class QueryResult implements IQueryResult
{
    /**
     * @var \TDengine\Resource
     */
    protected $result;

    public function __construct(Resource $result)
    {
        $this->result = $result;
    }

    public function getData(): array
    {
        return $this->result->fetch();
    }

    public function getColumns(): array
    {
        return $this->result->fetchFields();
    }

    public function affectedRows(): int
    {
        return $this->result->affectedRows();
    }
}
