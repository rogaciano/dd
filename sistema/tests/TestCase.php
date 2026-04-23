<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        if ($this->shouldSkipForMissingSqliteDriver()) {
            $this->markTestSkipped('pdo_sqlite nao esta disponivel no PHP atual. Rode os testes no container da aplicacao.');
        }

        parent::setUp();
    }

    private function shouldSkipForMissingSqliteDriver(): bool
    {
        $testConnection = $_ENV['DB_CONNECTION'] ?? getenv('DB_CONNECTION');

        return in_array(RefreshDatabase::class, class_uses_recursive(static::class), true)
            && $testConnection === 'sqlite'
            && ! extension_loaded('pdo_sqlite');
    }
}
