<?php

namespace Gielfeldt\Lock\Storage;

use Gielfeldt\Lock;

class Memory extends Lock\LockStorageAbstract
{
    protected $locks = array();

    protected $locksIndex = array();

    public function loadByName($name)
    {
        if (!isset($this->locksIndex[$name])) {
            return false;
        }
        $identifier = $this->locksIndex[$name];
        if (!isset($this->locks[$identifier])) {
            return false;
        }
        return $this->locks[$identifier];
    }

    public function loadByIdentifier($identifier)
    {
        if (!isset($this->locks[$identifier])) {
            return false;
        }
        return $this->locks[$identifier];
    }

    public function insert(Lock\LockItemInterface $lock)
    {
        if (isset($this->locksIndex[$lock->getName()])) {
            return false;
        }
        $lock->setIdentifier(uniqid());
        $this->locks[$lock->getIdentifier()] = [
            'identifier' => $lock->getIdentifier(),
            'name' => $lock->getName(),
            'expires' => $lock->getExpires(),
            'owner' => $lock->getOwner(),
        ];
        $this->locksIndex[$lock->getName()] = $lock->getIdentifier();
        return true;
    }

    public function update(Lock\LockItemInterface $lock)
    {
        if (!isset($this->locks[$lock->getIdentifier()])) {
            return false;
        }
        $this->locks[$lock->getIdentifier()] = [
            'identifier' => $lock->getIdentifier(),
            'name' => $lock->getName(),
            'expires' => $lock->getExpires(),
            'owner' => $lock->getOwner(),
        ];
        return true;
    }

    public function delete($identifier)
    {
        if (!isset($this->locks[$identifier])) {
            return false;
        }
        $name = $this->locks[$identifier]['name'];
        unset($this->locks[$identifier]);
        unset($this->locksIndex[$name]);
        return true;
    }
}
