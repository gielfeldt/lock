<?php

namespace Gielfeldt\Lock\Example;

require 'vendor/autoload.php';

use Gielfeldt\Lock;

$lockService = new Lock\LockService([
    'storage' => new Lock\Storage\Memory(),
]);

print "'mylock' is locked: " . $lockService->isLocked('mylock') . "\n";
print "Locking 'mylock'\n";

$lock = $lockService->acquire('mylock');
print "'mylock' is locked: " . $lockService->isLocked('mylock') . "\n";

$lock->bind('release', function ($lock) {
    print "RELEASE EVENT 2: " . $lock->getName() . "\n";
});

$lock->release();
print "'mylock' is locked: " . $lockService->isLocked('mylock') . "\n";
