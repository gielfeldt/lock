<?php

namespace Gielfeldt\Lock\Test;

use Gielfeldt\Lock;

include_once __DIR__ . '/LockTestBase.php';

/**
 * @covers \Gielfeldt\Lock\LockService
 * @covers \Gielfeldt\Lock\LockEventHandler
 * @covers \Gielfeldt\Lock\LockItem
 * @covers \Gielfeldt\Lock\LockItemFactory
 * @covers \Gielfeldt\Lock\LockItemAbstract
 * @covers \Gielfeldt\Lock\LockStorageAbstract
 * @covers \Gielfeldt\Lock\Storage\File
 */
class LockFileTest extends LockTestBase
{
    protected $path;

    public function __construct()
    {
        parent::__construct();
        $this->path = sys_get_temp_dir() . '/' . uniqid();
        mkdir($this->path);
    }

    protected function getStorage()
    {
        return new Lock\Storage\File($this->path);
    }

    public function testMissingLock()
    {
        $lock1 = $this->service1->acquire('lock1');
        $this->assertTrue($lock1 instanceof Lock\LockItemInterface);
        unlink($this->path . '/lock.' . $lock1->getIdentifier());
        $lock2 = $this->service1->loadCurrent('lock1');
        $this->assertFalse($lock2);
    }

    public function testFileCreation()
    {
        $service = new Lock\LockService([
            'storageHandler' => new Lock\Storage\File($this->path . '/does/not/exist'),
        ]);
        $lock1 = $service->acquire('lock1');
        $this->assertFalse($lock1 instanceof Lock\LockItemInterface);
    }
}
