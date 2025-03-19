<?php

/**
 * @author Aaron Francis <aaron@tryhardstudios.com>
 *
 * @link https://aaronfrancis.com
 * @link https://x.com/aarondfrancis
 */

namespace SoloTerm\Dumps\Providers;

use Illuminate\Support\ServiceProvider;
use SoloTerm\Dumps\Console\Commands\Dumps;
use SoloTerm\Dumps\Support\CustomDumper;

class DumpServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        CustomDumper::register($this->app->basePath(), $this->app['config']->get('view.compiled'));

        if ($this->app->runningInConsole()) {
            $this->commands([
                Dumps::class
            ]);
        }
    }
}
