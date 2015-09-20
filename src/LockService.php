<?php

namespace Gielfeldt\Lock;

class LockService implements LockServiceInterface
{
    protected $options;
    protected $owner;

    public function __construct(array $options)
    {
        if (!isset($options['itemFactory'])) {
            $options['itemFactory'] = new LockItemFactory();
        }
        if (!isset($options['events'])) {
            $options['events'] = new LockEventHandler();
        }

        $this->options = $options;
        $this->owner = uniqid();
    }

    public function load($identifier)
    {
        if ($data = $this->options['storage']->loadByIdentifier($identifier)) {
            return $this->factory($data);
        }
        return false;
    }

    public function loadCurrent($name)
    {
        if ($data = $this->options['storage']->loadByName($name)) {
            return $this->factory($data);
        }
        return false;
    }

    public function isLocked($name)
    {
        return $this->loadCurrent($name) ? true : false;
    }

    public function acquire($name, $lifetime = 30)
    {
        $expires = microtime(true) + $lifetime;
        $lock = $this->loadCurrent($name);
        if ($lock) {
            if ($lock->getOwner() == $this->owner) {
                // We own this lock. Update expiration.
                $lock->setExpires($expires);
                if (!$this->options['storage']->update($lock)) {
                    // Could not update lock.
                    return false;
                }
                $check = $this->loadCurrent($name);
                if ($check->getIdentifier() === $lock->getIdentifier()) {
                    // Lock is proper. Let's use it.
                    $lock->setAutoRelease(true);
                    return $lock;
                }
            } else {
                // Not ours. Has it expired?
                if ($lock->getExpires() < microtime(true)) {
                    // Release it, so that we may re-acquire it.
                    $this->release($lock);
                } else {
                    // Nope, the we cannot acquire lock.
                    return false;
                }
            }
        }
        $lock = $this->options['itemFactory']->factory($this);
        $lock->setName($name);
        $lock->setOwner($this->owner);
        $lock->setExpires($expires);
        if ($this->options['storage']->insert($lock)) {
            $lock->setAutoRelease(true);
            return $lock;
        } else {
            return false;
        }
    }

    public function release($identifier)
    {
        $lock = $this->load($identifier);
        if ($lock && $this->options['storage']->delete($identifier)) {
            $this->options['events']->flush($this, $lock, 'release');
        }
    }

    public function bind($identifier, $eventName, callable $callback)
    {
        $this->options['events']->add($identifier, $eventName, $callback);
    }

    protected function factory($data)
    {
        $lock = $this->options['itemFactory']->factory($this);
        $lock->setIdentifier($data['identifier']);
        $lock->setName($data['name']);
        $lock->setOwner($data['owner']);
        $lock->setExpires($data['expires']);
        $lock->setAutoRelease(false);
        return $lock;
    }
}
