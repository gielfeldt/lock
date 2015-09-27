<?php

namespace Gielfeldt\Lock;

class LockEventHandler implements LockEventHandlerInterface
{
    protected $events = array();

    public function add($eventName, $identifier, callable $callback)
    {
        $eventId = uniqid();
        $this->events[$eventName][$identifier][$eventId] = $callback;
        return $eventId;
    }

    public function remove($eventName, $identifier, $eventId)
    {
        unset($this->events[$eventName][$identifier][$eventId]);
    }

    public function clear($eventName, $identifier)
    {
        unset($this->events[$eventName][$identifier]);
    }

    public function flush($service, LockItemInterface $lock, $eventName)
    {
        $identifier = $lock->getIdentifier();
        if (isset($this->events[$eventName][$identifier])) {
            foreach ($this->events[$eventName][$identifier] as $eventId => $callback) {
                unset($this->events[$eventName][$identifier][$eventId]);
                $callback($lock);
            }
        }
    }

    public function getEvents($eventName)
    {
        return isset($this->events[$eventName]) ? $this->events[$eventName] : [];
    }
}
