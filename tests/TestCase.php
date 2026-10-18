<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Safety guard: tests run migrate:fresh, which erases every table.
     * They may only run against a database whose name ends in "_test"
     * (set in phpunit.xml). If that setting is missing, stop before
     * anything can touch the real development database.
     */
    protected function setUp(): void
    {
        $database = (string) ($_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: '');

        if (! str_ends_with($database, '_test')) {
            throw new RuntimeException(
                'Refusing to run tests: DB_DATABASE must end in "_test" (see phpunit.xml). Current value: "'.$database.'".'
            );
        }

        parent::setUp();

        // The public layout uses @vite(); tests do not need compiled assets.
        $this->withoutVite();
    }
}