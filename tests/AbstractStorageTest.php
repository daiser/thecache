<?php

namespace MaximalTestWork\Tests;

use MaximalTestWork\Contracts\StorageInterface;
use MaximalTestWork\NotFoundException;
use MaximalTestWork\ValueExpiredException;
use PHPUnit\Framework\TestCase;

abstract class AbstractStorageTest extends TestCase {
    /** @var StorageInterface */
    protected $storage;



    function testContainsKey() {
        $this->assertFalse($this->storage->containsKey('key1'));
        $this->assertFalse($this->storage->containsKey('key2'));
        $this->storage->create('key1', 'value1');
        $this->assertTrue($this->storage->containsKey('key1'));
        $this->assertFalse($this->storage->containsKey('key2'));
    }



    function testCreate() {
        $this->assertFalse($this->storage->containsKey('key'));
        $this->storage->create('key', 'value');
        $this->assertTrue($this->storage->containsKey('key'));
    }



    function testIsValid() {
        $this->storage->createUntil('key', 'value', 100);
        $this->assertTrue($this->storage->containsKey('key'));
        $this->assertFalse($this->storage->isValid('key'));

        $this->storage->create('key1', 'value1');
        $this->assertTrue($this->storage->containsKey('key1'));
        $this->assertTrue($this->storage->isValid('key1'));
    }



    function testRemove() {
        $this->assertFalse($this->storage->containsKey('key'));
        $this->storage->create('key', 'value');
        $this->assertTrue($this->storage->containsKey('key'));
        $this->storage->remove('key');
        $this->assertFalse($this->storage->containsKey('key'));
    }



    function testRead() {
        $this->storage->create('key', 'value');
        $this->assertEquals('value', $this->storage->read('key'));
    }



    function testReadExpired() {
        $this->expectException(ValueExpiredException::class);
        $this->storage->createUntil('key', 'value', 100);
        $this->storage->read('key');
    }



    function testReadUnknown() {
        $this->expectException(NotFoundException::class);
        $this->storage->read('key');
    }
}
