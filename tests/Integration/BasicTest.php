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
        $output = '';
        $process = Process::start('php vendor/bin/testbench solo:dumps', function ($type, $buffer) use (&$output) {
            $output .= $buffer;
        });

        sleep(1);
        dump($uuid = Str::uuid()->toString());
        sleep(1);

        $process->stop();

        // Right server
        $this->assertStringContainsString('tcp://127.0.0.1:9984', $output);
        // Right output
        $this->assertStringContainsString($uuid, $output);
        // Right context
        $this->assertStringContainsString('BasicTest.php:27', $output);
    }
}
