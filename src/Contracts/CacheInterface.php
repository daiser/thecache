<?php


namespace MaximalTestWork\Contracts;


interface CacheInterface {
    function contains(string $key): bool;

    function get(string $key): ?string;

    function put(string $key, string $value, int $duration = 0);

    function remove(string $key);
}
