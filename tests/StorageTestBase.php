<?php

namespace Gielfeldt\Lock\Test;

use Gielfeldt\Lock;

include_once __DIR__ . '/BadUpdateStorage.php';

/**
 * @covers \Gielfeldt\Lock\LockStorageAbstract
 */
abstract class StorageTestBase extends \PHPUnit_Framework_TestCase
{
    protected $service;

    abstract protected function getStorage();

    /**
     * Release counter.
     */
    public static function release()
    {
        self::$testVariable++;
    }

    public function setup()
    {
        parent::setup();
        $storage = $this->getStorage();
        $this->service = new Lock\LockService([
            'storage' => $storage,
        ]);
    }

    /**
     * Test insert().
     */
    public function testInsert()
    {
        $lock = $this->service->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);

        $lock->setName('test2');
        $lock->setIdentifier(null);
        $result = $this->service->getStorage()->insert($lock);
        $this->assertTrue($result !== false);

        $lock->setIdentifier(null);
        $result = $this->service->getStorage()->insert($lock);
        $this->assertFalse($result);
    }

    /**
     * Test update().
     */
    public function testUpdate()
    {
        $lock = $this->service->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);

        $lock->setName('test2');
        $result = $this->service->getStorage()->update($lock);
        $this->assertTrue($result);

        $lock->setIdentifier($lock->getIdentifier() . '-nope');
        $result = $this->service->getStorage()->update($lock);
        $this->assertFalse($result);
    }

    /**
     * Test delete().
     */
    public function testDelete()
    {
        $lock = $this->service->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);

        $result = $this->service->getStorage()->delete($lock->getIdentifier());
        $this->assertTrue($result);

        $result = $this->service->getStorage()->delete($lock->getIdentifier());
        $this->assertFalse($result);
    }

    public function testBadUpdate()
    {
        $lock = $this->service->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);

        $this->service->setStorage(new BadUpdateStorage($this->service->getStorage()));

        $result = $this->service->acquire('test1');
        $this->assertFalse($result);
    }
}
