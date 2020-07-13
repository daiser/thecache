<?php


namespace MaximalTestWork;


use MaximalTestWork\Contracts\CacheInterface;
use MaximalTestWork\Contracts\StorageInterface;

class Cache implements CacheInterface {
    /** @var StorageInterface */
    private $storage;



    function contains(string $key): bool {
        return $this->storage->containsKey($key);
    }



    function get(string $key): ?string {
        try {
            return $this->storage->read($key);
        }
        catch (NotFoundException | ValueExpiredException $ignored) {
            return null;
        }
    }



    function put(string $key, string $value, int $duration = 0) {
        $this->storage->remove($key);
        if ($duration === 0) {
            $this->storage->create($key, $value);
        }
        else {
            $this->storage->createUntil($key, $value, time() + $duration);
        }
    }



    function remove(string $key) {
        $this->storage->remove($key);
    }
}
