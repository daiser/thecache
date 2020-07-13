<?php


namespace MaximalTestWork\Contracts;


use MaximalTestWork\NotFoundException;
use MaximalTestWork\ValueExpiredException;

interface StorageInterface {
    function containsKey(string $key): bool;



    function create(string $key, string $value);



    function createUntil(string $key, string $value, int $expiresAt);



    function isValid(string $key): bool;



    /**
     * @param string $key
     *
     * @return string
     * @throws ValueExpiredException
     * @throws NotFoundException
     */
    function read(string $key): string;



    function remove(string $key);
}
