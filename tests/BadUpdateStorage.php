<?php

namespace Gielfeldt\Lock\Test;

use Gielfeldt\Lock;

class BadUpdateStorage implements Lock\LockStorageInterface
{
    protected $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function loadByName($name)
    {
        return $this->source->loadByName($name);
    }

    public function loadByIdentifier($identifier)
    {
        return $this->source->loadByIdentifier($identifier);
    }

    public function insert(Lock\LockItemInterface $lock)
    {
        return $this->source->insert($lock);
    }

    public function update(Lock\LockItemInterface $lock)
    {
        return false;
    }

    public function delete($identifier)
    {
        return $this->source->delete($identifier);
    }

    public function garbageCollect()
    {
        return $this->source->garbageCollect();
    }

    public function cleanUp()
    {
        return $this->source->cleanUp();
    }
}
