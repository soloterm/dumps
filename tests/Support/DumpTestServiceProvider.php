<?php

/**
 * @author Aaron Francis <aaron@tryhardstudios.com>
 *
 * @link https://aaronfrancis.com
 * @link https://x.com/aarondfrancis
 */

namespace SoloTerm\Dumps\Tests\Support;

use Illuminate\Support\ServiceProvider;
use SoloTerm\Dumps\Console\Commands\DumpTestOnly;

class DumpTestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DumpTestOnly::class,
            ]);
        }
    }
}
