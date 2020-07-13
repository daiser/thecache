<?php

namespace MaximalTestWork\Tests;

use MaximalTestWork\Cache;
use MaximalTestWork\FileStorage;
use MaximalTestWork\MemoryStorage;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase {
    private $path;



    protected function setUp(): void {
        $this->path = (sys_get_temp_dir() ?? '.') . '/the-cache-ut';
        mkdir($this->path);
    }



    protected function tearDown(): void {
        array_map('unlink', glob($this->path . '/*'));
        rmdir($this->path);
    }



    function testDefaultStorage() {
        $cache = new Cache();

        $this->assertFalse($cache->contains('key'));
        $cache->put('key', 'value');
        $this->assertTrue($cache->contains('key'));
        $this->assertEquals('value', $cache->get('key'));
    }



    function testFileStorage() {
        $cache = new Cache(new FileStorage($this->path));

        $this->assertFalse($cache->contains('key'));
        $cache->put('key', 'value');
        $this->assertTrue($cache->contains('key'));
        $this->assertEquals('value', $cache->get('key'));
    }



    function testMemoryStorage() {
        $cache = new Cache(new MemoryStorage());

        $this->assertFalse($cache->contains('key'));
        $cache->put('key', 'value');
        $this->assertTrue($cache->contains('key'));
        $this->assertEquals('value', $cache->get('key'));
    }
}
