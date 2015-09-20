# Shutdown Handler

[![Build Status](https://scrutinizer-ci.com/g/gielfeldt/lock/badges/build.png?b=master)][8]
[![Test Coverage](https://codeclimate.com/github/gielfeldt/lock/badges/coverage.svg)][3]
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gielfeldt/lock/badges/quality-score.png?b=master)][7]
[![Code Climate](https://codeclimate.com/github/gielfeldt/lock/badges/gpa.svg)][5]

[![Latest Stable Version](https://poser.pugx.org/gielfeldt/lock/v/stable.svg)][1]
[![Latest Unstable Version](https://poser.pugx.org/gielfeldt/lock/v/unstable.svg)][1]
[![Dependency Status](https://www.versioneye.com/user/projects/55ff0c17601dd9001c000058/badge.svg?style=flat)][11]
[![License](https://poser.pugx.org/gielfeldt/lock/license.svg)][4]
[![Total Downloads](https://poser.pugx.org/gielfeldt/lock/downloads.svg)][1]

[![Documentation Status](https://readthedocs.org/projects/lock/badge/?version=stable)][12]
[![Documentation Status](https://readthedocs.org/projects/lock/badge/?version=latest)][12]

## Installation

To install the Lock library in your project using Composer, first add the following to your `composer.json`
config file.
```javascript
{
    "require": {
        "gielfeldt/lock": "~1.0"
    }
}
```

Then run Composer's install or update commands to complete installation. Please visit the [Composer homepage][6] for
more information about how to use Composer.

### Lock

This lock handler ...

#### Motivation

1. Robust locks that ensures release upon exit
2. Attach event handlers on a lock release.

#### Example 1 - using Lock library

```php
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
```
For more examples see the examples/ folder.

#### Features

* Use arbitrary storage backends for locks
* Persist locks across scripts
* Ensure release of locks on end-of-scope
* Attach custom event handlers on lock release

#### Caveats

1. Lots probably.



[1]:  https://packagist.org/packages/gielfeldt/lock
[2]:  https://circleci.com/gh/gielfeldt/lock
[3]:  https://codeclimate.com/github/gielfeldt/lock/coverage
[4]:  https://github.com/gielfeldt/lock/blob/master/LICENSE.md
[5]:  https://codeclimate.com/github/gielfeldt/lock
[6]:  http://getcomposer.org
[7]:  https://scrutinizer-ci.com/g/gielfeldt/lock/?branch=master
[8]:  https://scrutinizer-ci.com/g/gielfeldt/lock/build-status/master
[9]:  https://coveralls.io/github/gielfeldt/lock
[10]: https://travis-ci.org/gielfeldt/lock
[11]: https://www.versioneye.com/user/projects/55ff0c17601dd9001c000058
[12]: https://readthedocs.org/projects/lock/?badge=latest
