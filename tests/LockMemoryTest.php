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
 * @covers \Gielfeldt\Lock\Storage\Memory
 */
class LockMemoryTest extends LockTestBase
{
    protected function getStorage()
    {
        return new Lock\Storage\Memory();
    }
}
