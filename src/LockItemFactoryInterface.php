<?php

namespace Gielfeldt\Lock;

interface LockItemFactoryInterface
{
    public function factory(LockServiceInterface $service);
}
