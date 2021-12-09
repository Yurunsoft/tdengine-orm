<?php

declare(strict_types=1);

namespace Yurun\TDEngine\Orm\Test;

use PHPUnit\Framework\TestCase;
use Yurun\TDEngine\Orm\Test\Model\DeviceLogModel;
use Yurun\TDEngine\TDEngineManager;

class ModelTest extends TestCase
{
    public function testCreateSuperTable(): void
    {
        DeviceLogModel::createSuperTable();
        $this->assertTrue(true);
    }

    public function testCreateTable(): void
    {
        $client = TDEngineManager::getClient();

        $table = 'device_' . md5(uniqid('', true));
        $deviceId = md5(uniqid('', true));
        DeviceLogModel::createTable($table, [$deviceId]);
        $this->assertTableExists($table);
        $client->sql('DROP TABLE device.' . $table);

        $table = 'device_' . md5(uniqid('', true));
        $deviceId = md5(uniqid('', true));
        DeviceLogModel::createTable($table, ['deviceId' => $deviceId]);
        $this->assertTableExists($table);
        $client->sql('DROP TABLE device.' . $table);

        $table = 'device_' . md5(uniqid('', true));
        $deviceId = md5(uniqid('', true));
        DeviceLogModel::createTable($table, ['device_id' => $deviceId]);
        $this->assertTableExists($table);
        $client->sql('DROP TABLE device.' . $table);
    }

    public function testInsert(): void
    {
        $table = 'device_insert';
        $record = new DeviceLogModel([], $table);
        $record->time = (int) (microtime(true) * 1000);
        $record->deviceId = '00000001';
        $record->voltage = 1.23;
        $record->electricCurrent = 4.56;
        $record->insert();
        $this->assertTrue(true);
    }

    public function testBatchInsert(): void
    {
        $table1 = 'device_batch_insert_1';
        $records = [];
        $record = new DeviceLogModel([], $table1);
        $record->time = (int) (microtime(true) * 1000);
        $record->deviceId = '00000001';
        $record->voltage = 1.23;
        $record->electricCurrent = 4.56;
        $records[] = $record;

        usleep(1000);
        $table2 = 'device_batch_insert_2';
        $records[] = new DeviceLogModel([
            'time'            => (int) (microtime(true) * 1000),
            'deviceId'        => '00000002',
            'voltage'         => 1.1,
            'electricCurrent' => 2.2,
        ], $table2);
        DeviceLogModel::batchInsert($records);

        $this->assertTrue(true);
    }

    private function assertTableExists(string $tableName): void
    {
        $result = TDEngineManager::getClient()->sql('show device.tables');
        foreach ($result->getData() as $row)
        {
            if ($tableName === $row['table_name'])
            {
                $this->assertTrue(true);

                return;
            }
        }
        $this->assertTrue(false);
    }
}
