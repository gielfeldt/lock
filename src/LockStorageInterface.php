<?php

namespace Gielfeldt\Lock;

interface LockStorageInterface
{
    public function loadByName($name);

    public function loadByIdentifier($identifier);

    public function insert(LockItemInterface $lock);

    public function update(LockItemInterface $lock);

    public function delete($identifier);

    public function garbageCollect();

    public function cleanUp();
}
