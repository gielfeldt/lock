<?php

namespace Gielfeldt\Lock;

class LockEventHandler implements LockEventHandlerInterface
{
    protected $events = array();

    public function add($identifier, $eventName, callable $callback)
    {
        $eventId = uniqid();
        $this->events[$identifier][$eventName][$eventId] = $callback;
        return $eventId;
    }

    public function remove($identifier, $eventName, $eventId)
    {
        unset($this->events[$identifier][$eventName][$eventId]);
    }

    public function clear($identifier, $eventName)
    {
        unset($this->events[$identifier][$eventName]);
    }

    public function flush($service, LockItemInterface $lock, $eventName)
    {
        $identifier = $lock->getIdentifier();
        if (isset($this->events[$identifier][$eventName])) {
            foreach ($this->events[$identifier][$eventName] as $eventId => $callback) {
                unset($this->events[$identifier][$eventName][$eventId]);
                $callback($lock);
            }
        }
    }
}
