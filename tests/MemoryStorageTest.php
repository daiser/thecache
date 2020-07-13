<?php

namespace MaximalTestWork\Tests;

use MaximalTestWork\MemoryStorage;

class MemoryStorageTest extends AbstractStorageTest {
    protected function setUp(): void {
        $this->storage = new MemoryStorage();
    }
}
