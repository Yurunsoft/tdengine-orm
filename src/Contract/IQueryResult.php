<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Contract;

interface IQueryResult
{
    public function getData(): array;

    public function getColumns(): array;

    public function affectedRows(): int;
}
