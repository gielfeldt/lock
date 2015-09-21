<?php

namespace Gielfeldt\Lock\Test;

use Gielfeldt\Lock;

include_once __DIR__ . '/StorageTestBase.php';

/**
 * @covers \Gielfeldt\Lock\LockService
 * @covers \Gielfeldt\Lock\LockEventHandler
 * @covers \Gielfeldt\Lock\LockItem
 * @covers \Gielfeldt\Lock\LockItemFactory
 * @covers \Gielfeldt\Lock\LockItemAbstract
 * @covers \Gielfeldt\Lock\LockStorageAbstract
 * @covers \Gielfeldt\Lock\Storage\File
 */
class StorageFileTest extends StorageTestBase
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
}
