<?php

/**
 * @author Aaron Francis <aaron@tryhardstudios.com>
 *
 * @link https://aaronfrancis.com
 * @link https://x.com/aarondfrancis
 */

namespace SoloTerm\Dumps\Tests\Integration;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;

class BasicTest extends Base
{
    #[Test]
    public function basic_test_only()
    {
        $process = Process::start('php vendor/bin/testbench solo:dumps');

        sleep(1);
        dump($uuid = Str::uuid()->toString());

        sleep(1);
        $process->signal(SIGTERM);

        $process->wait();

        // Right server
        $this->assertStringContainsString('tcp://127.0.0.1:9984', $process->output());
        // Right output
        $this->assertStringContainsString($uuid, $process->output());
        // Right context
        $this->assertStringContainsString('BasicTest.php:24', $process->output());

        $uuid = Str::uuid()->toString();
        $process = Process::start("php vendor/bin/testbench solo:dump-test-only --uuid=$uuid");
        $process->wait();

        // Output should have been restored
        $this->assertStringContainsString($uuid, $process->output());
    }

    #[Test]
    public function start_and_stop_test()
    {
        $server = Process::start('php vendor/bin/testbench solo:dumps');
        // Wait for the server to start up
        sleep(1);

        // Start the loop command
        $loop = Process::start('php vendor/bin/testbench solo:dump-test-only --loop');
        sleep(2);

        // Kill the dump server after a few seconds
        $server->signal(SIGTERM);
        $loop->wait();

        // The server should contain 1 & 2, at the very least.
        $this->assertStringContainsString('Loop 1', $server->output());
        $this->assertStringContainsString('Loop 2', $server->output());

        // 3 could go either way due to timing, but we must ensure it exists.
        $this->assertStringContainsString('Loop 3', $server->output() . $loop->output());

        // 4 & 5 should go to the loop now that the server is dead.
        $this->assertStringContainsString('Loop 4', $loop->output());
        $this->assertStringContainsString('Loop 5', $loop->output());
    }
}
