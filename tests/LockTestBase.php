<?php

namespace Gielfeldt\Lock\Test;

use Gielfeldt\Lock;

/**
 * @covers \Gielfeldt\Lock\LockService
 * @covers \Gielfeldt\Lock\LockEventHandler
 * @covers \Gielfeldt\Lock\LockItem
 * @covers \Gielfeldt\Lock\LockItemFactory
 * @covers \Gielfeldt\Lock\LockItemAbstract
 * @covers \Gielfeldt\Lock\LockStorageAbstract
 */
abstract class LockTestBase extends \PHPUnit_Framework_TestCase
{
    public static $testVariable;
    protected $service1;
    protected $service2;

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
        $this->service1 = new Lock\LockService([
            'storage' => $storage,
        ]);
        $this->service2 = new Lock\LockService([
            'storage' => $storage,
        ]);
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

    /**
     * Test acquire().
     */
    public function testRenew()
    {
        $lifetime = 30;
        $start = microtime(true) + $lifetime;
        $lock1 = $this->service1->acquire('test1', $lifetime);
        $end = microtime(true) + $lifetime;
        $this->assertTrue($lock1 instanceof Lock\LockItemInterface);
        $this->assertTrue($start <= $lock1->getExpires() && $lock1->getExpires() <= $end);

        $lifetime = 30;
        $start = microtime(true) + $lifetime;
        $lock2 = $this->service1->acquire('test1', $lifetime);
        $end = microtime(true) + $lifetime;
        $this->assertTrue($lock2 instanceof Lock\LockItemInterface);
        $this->assertEquals($lock1->getIdentifier(), $lock2->getIdentifier());
        $this->assertTrue($start <= $lock2->getExpires() && $lock2->getExpires() <= $end);
    }

    public function testExpire()
    {
        $lifetime = 0;
        $lock1 = $this->service1->acquire('test1', $lifetime);
        $this->assertTrue($lock1 instanceof Lock\LockItemInterface);

        $lifetime = 30;
        $lock2 = $this->service2->acquire('test1', $lifetime);
        $this->assertTrue($lock2 instanceof Lock\LockItemInterface);

        $lifetime = 30;
        $lock3 = $this->service1->acquire('test1', $lifetime);
        $this->assertFalse($lock3);
    }

    public function testEventAdd()
    {
        self::$testVariable = 0;
        $lock = $this->service1->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);
        $lock->bind('release', [get_class($this), 'release']);
        $this->assertEquals(self::$testVariable, 0);
        $lock->release();
        $this->assertEquals(self::$testVariable, 1);

        self::$testVariable = 0;
        $lock = $this->service1->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);
        $lock->bind('release', [get_class($this), 'release']);
        $lock->bind('release', [get_class($this), 'release']);
        $this->assertEquals(self::$testVariable, 0);
        $lock->release();
        $this->assertEquals(self::$testVariable, 2);
    }

    public function testEventRemove()
    {
        self::$testVariable = 0;
        $lock = $this->service1->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);
        $id1 = $lock->bind('release', [get_class($this), 'release']);
        $id2 = $lock->bind('release', [get_class($this), 'release']);
        $lock->unBind('release', $id1);
        $this->assertEquals(self::$testVariable, 0);
        $lock->release();
        $this->assertEquals(self::$testVariable, 1);
    }

    public function testEventClear()
    {
        self::$testVariable = 0;
        $lock = $this->service1->acquire('test1');
        $this->assertTrue($lock instanceof Lock\LockItemInterface);
        $lock->bind('release', [get_class($this), 'release']);
        $lock->bind('release', [get_class($this), 'release']);
        $lock->clearBind('release');
        $this->assertEquals(self::$testVariable, 0);
        $lock->release();
        $this->assertEquals(self::$testVariable, 0);
    }

    public function testStorage()
    {
        $this->service1->getStorage()->garbageCollect();
        $this->service1->getStorage()->cleanUp();
    }

    public function testAutoRelease()
    {
        self::$testVariable = 0;
        $scopedLock = function ($self) {
            $lock = $self->service1->acquire('test1');
            $self->assertTrue($lock instanceof Lock\LockItemInterface);
            $lock->bind('release', [get_class($self), 'release']);
            $lock->setAutoRelease(true);
        };
        $scopedLock($this);
        $this->assertEquals(self::$testVariable, 1);

        self::$testVariable = 0;
        $scopedLock = function ($self) {
            $lock = $self->service1->acquire('test1');
            $self->assertTrue($lock instanceof Lock\LockItemInterface);
            $lock->bind('release', [get_class($self), 'release']);
            $lock->setAutoRelease(true);
            return $lock;
        };
        $lock = $scopedLock($this);
        $this->assertEquals($lock->getAutoRelease(), true);
        $this->assertEquals(self::$testVariable, 0);
        $lock->release();
        $this->assertEquals(self::$testVariable, 1);

        self::$testVariable = 0;
        $scopedLock = function ($self) {
            $lock = $self->service1->acquire('test1');
            $self->assertTrue($lock instanceof Lock\LockItemInterface);
            $lock->bind('release', [get_class($self), 'release']);
            $lock->setAutoRelease(false);
            return $lock;
        };
        $lock = $scopedLock($this);
        $this->assertEquals($lock->getAutoRelease(), false);
        $this->assertEquals(self::$testVariable, 0);
        $lock->release();
        $this->assertEquals(self::$testVariable, 1);
    }
}
