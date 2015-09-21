<?php

namespace Gielfeldt\Lock\Storage;

use Gielfeldt\Lock;

class File extends Lock\LockStorageAbstract
{
    protected $path;

    public function __construct($path = null)
    {
        $this->path = $path ? $path : sys_get_temp_dir();
    }

    public function loadByName($name)
    {
        $data = @file_get_contents($this->path . "/metadata.$name");
        if (!$data) {
            return false;
        }
        $lockMetadata = json_decode($data);
        $data = @file_get_contents($this->path . "/lock." . $lockMetadata->identifier);
        if (!$data) {
            return false;
        }
        $lockData = json_decode($data);
        return (array) $lockData;
    }

    public function loadByIdentifier($identifier)
    {
        $data = @file_get_contents($this->path . "/lock." . $identifier);
        if (!$data) {
            return false;
        }
        $lockData = json_decode($data);
        return (array) $lockData;
    }

    public function insert(Lock\LockItemInterface $lock)
    {
        $fh = @fopen($this->path . "/metadata." . $lock->getName(), 'x');
        if (!$fh) {
            return false;
        }
        $lock->setIdentifier(uniqid());
        fwrite($fh, json_encode([
            'identifier' => $lock->getIdentifier()
        ]));
        $file = $this->path . "/lock." . $lock->getIdentifier();
        $result = @file_put_contents($file, json_encode([
            'identifier' => $lock->getIdentifier(),
            'name' => $lock->getName(),
            'expires' => $lock->getExpires(),
            'owner' => $lock->getOwner(),
        ]));

        if ($result === false) {
            @unlink($file);
            @unlink($this->path . "/metadata." . $lock->getName());
        }
        @fclose($fh);
        return $result !== false;
    }

    public function update(Lock\LockItemInterface $lock)
    {
        $file = $this->path . "/lock." . $lock->getIdentifier();
        if (!file_exists($file)) {
            return false;
        }
        $result = file_put_contents($file, json_encode([
            'identifier' => $lock->getIdentifier(),
            'name' => $lock->getName(),
            'expires' => $lock->getExpires(),
            'owner' => $lock->getOwner(),
        ]));
        return $result !== false;
    }

    public function delete($identifier)
    {
        $lockfile = $this->path . "/lock." . $identifier;
        $data = @file_get_contents($lockfile);
        if (!$data) {
            return false;
        }
        $lockData = json_decode($data);

        $file = $this->path . "/metadata." . $lockData->name;
        $released = false;
        if ($fh = @fopen($file, 'r')) {
            if (@flock($fh, LOCK_EX)) {
                @unlink($lockfile);
                @unlink($file);
                $released = true;
            }
            @fclose($fh);
        }
        return $released;
    }
}
