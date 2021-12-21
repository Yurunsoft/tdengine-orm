<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\ClientHandler\Restful;

use Yurun\TDEngine\Action\Sql\SqlResult;
use Yurun\TDEngine\Orm\Contract\IQueryResult;

class QueryResult implements IQueryResult
{
    /**
     * @var SqlResult
     */
    protected $result;

    public function __construct(SqlResult $result)
    {
        $this->result = $result;
    }

    public function getData(): array
    {
        return $this->result->getData();
    }

    public function getColumns(): array
    {
        $result = [];
        foreach ($this->result->getColumns() as $column)
        {
            $result[] = [
                'name'   => $column->getName(),
                'type'   => $column->getType(),
                'bytes'  => $column->getLength(),
            ];
        }

        return $result;
    }

    public function affectedRows(): int
    {
        return $this->result->getRows();
    }
}
