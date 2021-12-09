<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Test\Model;

use Yurun\TDEngine\Orm\Annotation\Field;
use Yurun\TDEngine\Orm\Annotation\Table;
use Yurun\TDEngine\Orm\Annotation\Tag;
use Yurun\TDEngine\Orm\BaseModel;
use Yurun\TDEngine\Orm\Enum\DataType;

/**
 * @Table(name="device_log", database="device")
 */
class DeviceLogModel extends BaseModel
{
    /**
     * @Field(type=DataType::TIMESTAMP)
     *
     * @var int
     */
    public $time;

    /**
     * @Tag(type=DataType::NCHAR, length=32, name="device_id")
     *
     * @var string
     */
    public $deviceId;

    /**
     * @Field(type=DataType::FLOAT)
     *
     * @var float
     */
    public $voltage;

    /**
     * @Field(type=DataType::FLOAT, name="electric_current")
     *
     * @var float
     */
    public $electricCurrent;
}
