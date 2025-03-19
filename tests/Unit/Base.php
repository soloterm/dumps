<?php

/**
 * @author Aaron Francis <aaron@tryhardstudios.com>
 *
 * @link https://aaronfrancis.com
 * @link https://x.com/aarondfrancis
 */

namespace SoloTerm\Dumps\Tests\Unit;

use Orchestra\Testbench\TestCase;
use SoloTerm\Dumps\Providers\DumpServiceProvider;
use SoloTerm\Dumps\Tests\Support\DumpTestServiceProvider;

abstract class Base extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetup($app) {}

    protected function getPackageProviders($app)
    {
        return [
            DumpServiceProvider::class,
            DumpTestServiceProvider::class,
        ];
    }
}
