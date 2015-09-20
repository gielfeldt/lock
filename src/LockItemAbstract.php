<?php

namespace Gielfeldt\Lock;

use Gielfeldt\ShutdownHandler\ShutdownHandler;

abstract class LockItemAbstract implements LockItemInterface
{
    protected $identifier;
    protected $name;
    protected $expires;
    protected $owner;

    protected $autoRelease = false;

    protected $service;
    protected $shutdown;

    public function __construct($service)
    {
        $this->service = $service;
        $this->shutdown = new ShutdownHandler([get_class($this), 'shutdown'], [
            $service,
            &$this->identifier,
            &$this->autoRelease,
        ]);
    }

    public function __destruct()
    {
        $this->shutdown->run();
    }

    public static function shutdown($service, $identifier, $autoRelease)
    {
        if ($autoRelease) {
            $service->release($identifier);
        }
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getExpires()
    {
        return $this->expires;
    }

    public function setExpires($expires)
    {
        $this->expires = $expires;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    public function getAutoRelease()
    {
        return $this->autoRelease;
    }

    public function setAutoRelease($autoRelease)
    {
        $this->autoRelease = $autoRelease;
    }

    public function release()
    {
        return $this->service->release($this->getIdentifier());
    }

    public function bind($eventName, callable $callback)
    {
        return $this->service->bind($this->getIdentifier(), $eventName, $callback);
    }
}
