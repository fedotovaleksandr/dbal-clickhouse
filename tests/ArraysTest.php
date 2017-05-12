<?php

namespace FOD\DBALClickHouse\Tests;


use FOD\DBALClickHouse\Connection;
use FOD\DBALClickHouse\Types\ArrayType;
use PHPUnit\Framework\TestCase;

class ArraysTest extends TestCase
{
    /** @var  Connection */
    protected $connection;

    public function setUp()
    {
        $this->connection = CreateConnectionTest::createConnection();
        ArrayType::registerArrayTypes($this->connection->getDatabasePlatform());
        try {
            $this->connection->exec('DROP TABLE test_array_table');
        } catch (\Exception $exception) {

        }
    }

    public function testArrayInt8()
    {
        $this->createTempTable('array(int8)');
        $this->connection->insert('test_array_table', ['arr' => [1, 2, 3, 4, 5, 6, 7, 8]]);
        $this->assertEquals(['arr' => [1, 2, 3, 4, 5, 6, 7, 8]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayInt16()
    {
        $this->createTempTable('array(int16)');
        $this->connection->insert('test_array_table', ['arr' => [100, 2000, 30000]]);
        $this->assertEquals(['arr' => [100, 2000, 30000]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayInt32()
    {
        $this->createTempTable('array(int32)');
        $this->connection->insert('test_array_table', ['arr' => [1000000, 2000000000]]);
        $this->assertEquals(['arr' => [1000000, 2000000000]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayInt64()
    {
        $this->createTempTable('array(int64)');
        $this->connection->insert('test_array_table', ['arr' => [200000000000, 3000000000000000000]]);
        $this->assertEquals(['arr' => [200000000000, 3000000000000000000]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayUInt8()
    {
        $this->createTempTable('array(uint8)');
        $this->connection->insert('test_array_table', ['arr' => [1, 2, 3, 4, 5, 6, 7, 8]]);
        $this->assertEquals(['arr' => [1, 2, 3, 4, 5, 6, 7, 8]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayUInt16()
    {
        $this->createTempTable('array(uint16)');
        $this->connection->insert('test_array_table', ['arr' => [100, 2000, 30000]]);
        $this->assertEquals(['arr' => [100, 2000, 30000]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayUInt32()
    {
        $this->createTempTable('array(uint32)');
        $this->connection->insert('test_array_table', ['arr' => [1000000, 2000000000]]);
        $this->assertEquals(['arr' => [1000000, 2000000000]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayUInt64()
    {
        $this->createTempTable('array(uint64)');
        $this->connection->insert('test_array_table', ['arr' => [200000000000, 3000000000000000000]]);
        $this->assertEquals(['arr' => [200000000000, 3000000000000000000]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayFloat32()
    {
        $this->createTempTable('array(float32)');
        $this->connection->insert('test_array_table', ['arr' => [1.5, 10.5]]);
        $this->assertEquals(['arr' => [1.5, 10.5]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayFloat64()
    {
        $this->createTempTable('array(float64)');
        $this->connection->insert('test_array_table', ['arr' => [100.512, 10000.5814]]);
        $this->assertEquals(['arr' => [100.512, 10000.5814]], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayString()
    {
        $this->createTempTable('array(string)');
        $this->connection->insert('test_array_table', ['arr' => ['foo', 'bar']]);
        $this->assertEquals(['arr' => ['foo', 'bar']], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayDatetime()
    {
        $dateTimeArray = [(new \DateTime('2000-01-01'))->format('Y-m-d H:i:s'), (new \DateTime('2017-05-05'))->format('Y-m-d H:i:s')];
        $this->createTempTable('array(datetime)');
        $this->connection->insert('test_array_table', ['arr' => $dateTimeArray]);
        $this->assertEquals(['arr' => $dateTimeArray], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    public function testArrayDate()
    {
        $datesArray = [(new \DateTime('2000-01-01'))->format('Y-m-d'), (new \DateTime('2017-05-05'))->format('Y-m-d')];
        $this->createTempTable('array(date)');
        $this->connection->insert('test_array_table', ['arr' => $datesArray]);
        $this->assertEquals(['arr' => $datesArray], current($this->connection->fetchAll('SELECT arr FROM test_array_table')));
        $this->connection->exec('DROP TABLE test_array_table');
    }

    protected function createTempTable($arrayType)
    {
        $fromSchema = $this->connection->getSchemaManager()->createSchema();
        $toSchema = clone $fromSchema;
        if ($toSchema->hasTable('test_array_table') || $fromSchema->hasTable('test_array_table')) {
            $this->connection->exec('DROP TABLE test_array_table');
        }
        $newTable = $toSchema->createTable('test_array_table');

        $newTable->addColumn('arr', $arrayType);
        $newTable->addOption('engine', 'Memory');

        $generatedSQL = implode(';', $fromSchema->getMigrateToSql($toSchema, $this->connection->getDatabasePlatform()));

        $this->connection->exec($generatedSQL);
    }
}