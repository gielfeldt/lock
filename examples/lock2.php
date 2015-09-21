<?php

namespace Gielfeldt\Lock\Example;

require 'vendor/autoload.php';

use Gielfeldt\Lock;

$lockService = new Lock\LockService([
    'storage' => new Lock\Storage\Memory(),
]);

$lock1 = $lockService->acquire('mylock');
$lock2 = $lockService->acquire('mylock');
$lock3 = $lockService->acquire('mylock2');

$lock1->setAutoRelease(false);
$lock2->setAutoRelease(false);
#$lock3->setAutoRelease(false);
$lock1->bind('release', function ($lock) {
    print "RELEASE EVENT 1: " . $lock->getName() . "( " . $lock->getIdentifier() . ")\n";
});
$lock2->bind('release', function ($lock) {
    print "RELEASE EVENT 2: " . $lock->getName() . "( " . $lock->getIdentifier() . ")\n";
});
$lock3->bind('release', function ($lock) {
    print "RELEASE EVENT 3: " . $lock->getName() . "( " . $lock->getIdentifier() . ")\n";
});

#$lock1->release();
#$lock2->release();
#$lock3->release();
