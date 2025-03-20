<?php

/**
 * @author Aaron Francis <aaron@tryhardstudios.com>
 *
 * @link https://aaronfrancis.com
 * @link https://x.com/aarondfrancis
 */

namespace SoloTerm\Dumps\Console\Commands;

use Illuminate\Console\Command;

class DumpTestOnly extends Command
{
    protected $signature = 'solo:dump-test-only {--uuid=} {--loop}';

    protected $description = 'A test command';

    public function __construct()
    {
        parent::__construct();
        $this->setHidden();
    }

    public function handle()
    {
        if ($this->option('uuid')) {
            dump($this->option('uuid'));
        }

        if ($this->option('loop')) {
            for ($i = 1; $i <= 5; $i++) {
                dump("Loop $i");
                sleep(1);
            }
        }
    }
}
