<?php

namespace Gielfeldt\Lock;

interface LockItemInterface
{
    public function getIdentifier();

    public function setIdentifier($identifier);

    public function getName();

    public function setName($name);

    public function getExpires();

    public function setExpires($expires);

    public function getOwner();

    public function setOwner($owner);

    public function getAutoRelease();

    public function setAutoRelease($autoRelease);
}
