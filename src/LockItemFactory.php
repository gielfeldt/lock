<?php

namespace Gielfeldt\Lock;

class LockItemFactory implements LockItemFactoryInterface
{
    public function factory(LockServiceInterface $service)
    {
        return new LockItem($service);
    }
}
