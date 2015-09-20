<?php

namespace Gielfeldt\Lock\Test;

use Gielfeldt\Lock;

/**
 * @covers \Gielfeldt\Lock\LockService
 * @covers \Gielfeldt\Lock\LockItem
 * @covers \Gielfeldt\Lock\LockEventHandler
 * @covers \Gielfeldt\Lock\Storage\Memory
 */
class LockTest extends \PHPUnit_Framework_TestCase
{
    public static $testVariable;
    protected $service1;
    protected $service2;

    public function __construct()
    {
        parent::__construct();
        $lockStorage = new Lock\Storage\Memory();
        $this->service1 = new Lock\LockService([
            'storage' => $lockStorage,
        ]);
        $this->service2 = new Lock\LockService([
            'storage' => $lockStorage,
        ]);
    }

    /**
     * Release counter.
     */
    public static function release()
    {
        self::$testVariable++;
    }

    /**
     * Test acquire().
     */
    public function testAcquire()
    {
        $lock = $this->service1->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);
    }

    /**
     * Test isLocked().
     */
    public function testIsLocked()
    {
        $lock = $this->service1->acquire('test1');

        $isLocked = $this->service1->isLocked('test1');
        $this->assertTrue($isLocked);

        $isLocked = $this->service2->isLocked('test1');
        $this->assertTrue($isLocked);
    }

    /**
     * Test
     */
    public function testRelease()
    {
        $lock = $this->service1->acquire('test1');
        $lock->release();

        $isLocked = $this->service1->isLocked('test1');
        $this->assertFalse($isLocked);
    }
}
