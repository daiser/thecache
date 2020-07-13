<?php


namespace MaximalTestWork\Tests;


use MaximalTestWork\FileStorage;

class FileStorageTest extends AbstractStorageTest {
    private $path;



    protected function setUp(): void {
        $this->path = (sys_get_temp_dir() ?? '.') . '/the-cache-ut';
        mkdir($this->path);
        $this->storage = new FileStorage($this->path);
    }



    protected function tearDown(): void {
        array_map('unlink', glob($this->path . '/*'));
        rmdir($this->path);
    }
}
