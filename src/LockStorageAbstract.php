<?php

namespace Gielfeldt\Lock;

abstract class LockStorageAbstract implements LockStorageInterface
{
    public function garbageCollect()
    {
    }

    public function cleanUp()
    {
    }
}
