<?php

namespace Gielfeldt\Lock;

interface LockServiceInterface
{
    public function load($identifier);

    public function loadCurrent($name);

    public function isLocked($name);

    public function acquire($name);

    public function release($identifier);

    public function bind($identifier, $eventName, callable $callback);
}
