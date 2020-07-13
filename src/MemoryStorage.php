<?php


namespace MaximalTestWork;


use MaximalTestWork\Contracts\StorageInterface;

class MemoryStorage implements StorageInterface {
    private const KEY_EXPIRES_AT = 'et';
    private const KEY_VALUE      = 'val';
    /** @var array */
    private $container = [];



    public function containsKey(string $key): bool {
        return array_key_exists($key, $this->container);
    }



    function create(string $key, string $value) {
        $this->container[$key] = [
            self::KEY_VALUE      => $value,
            self::KEY_EXPIRES_AT => null
        ];
    }



    function createUntil(string $key, string $value, int $expiresAt) {
        $this->container[$key] = [
            self::KEY_VALUE      => $value,
            self::KEY_EXPIRES_AT => $expiresAt
        ];
    }



    function isValid(string $key): bool {
        $now  = time();
        $item = $this->container[$key] ?? null;
        if ($item === null)
            return false;
        if ($item[self::KEY_EXPIRES_AT] === null)
            return true;

        return $item[self::KEY_EXPIRES_AT] > $now;
    }



    function read(string $key): string {
        if (!$this->containsKey($key))
            throw new NotFoundException("Key '{$key}' not found");
        if (!$this->isValid($key))
            throw new ValueExpiredException("Value for key '{$key}' is expired");

        return $this->container[$key][self::KEY_VALUE];
    }



    function remove(string $key) {
        unset($this->container[$key]);
    }
}
