<?php

namespace Gielfeldt\Lock;

interface LockEventHandlerInterface
{
    public function add($identifier, $eventName, callable $callback);

    public function remove($identifier, $eventName, $eventId);

    public function clear($identifier, $eventName);

    public function flush($service, LockItemInterface $lock, $eventName);
}
