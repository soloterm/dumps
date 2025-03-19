<?php

/**
 * @author Aaron Francis <aaron@tryhardstudios.com>
 *
 * @link https://aaronfrancis.com
 * @link https://x.com/aarondfrancis
 */

namespace SoloTerm\Dumps\Tests\Integration;

use Orchestra\Testbench\TestCase;
use SoloTerm\Dumps\Providers\DumpServiceProvider;
use SoloTerm\Dumps\Tests\Support\DumpTestServiceProvider;

use function Orchestra\Testbench\package_path;

abstract class Base extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            DumpServiceProvider::class,
            DumpTestServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        $this->afterApplicationCreated(function () {
            touch(storage_path('logs/laravel.log'));
            @symlink(
                package_path('vendor', 'bin', 'testbench'),
                package_path() . '/artisan',
            );
        });

        parent::setUp();
    }
}
