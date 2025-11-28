# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Solo Dumps is a Laravel package that intercepts `dump()` calls and redirects them to a dedicated terminal window via a TCP server. This keeps browser/API responses clean while centralizing debug output.

## Commands

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test suite
./vendor/bin/phpunit --testsuite=unit
./vendor/bin/phpunit --testsuite=integration

# Run single test
./vendor/bin/phpunit --filter testMethodName

# Start the dump server (for manual testing)
php vendor/bin/testbench solo:dumps
```

## Architecture

### Data Flow

```text
dump() call in app
    ↓
CustomDumper (VarDumper handler)
    ↓ resolves source file/line
    ↓ clones var with dumpSource context
ServerDumper → TCP socket (127.0.0.1:9984)
    ↓
DumpServer in `solo:dumps` command
    ↓ extracts dumpSource from context
CliDumper → terminal output with source info
```

### Key Components

- **CustomDumper** (`src/Support/CustomDumper.php`): Registers a custom VarDumper handler that:
  - Resolves dump source (file:line) before sending
  - Checks if dump server port is open
  - Falls back to original handler if server unavailable

- **Dumps Command** (`src/Console/Commands/Dumps.php`): Runs the TCP server that receives and displays dumps with source information

### Fallback Behavior

When the dump server isn't running, CustomDumper falls back to the original dump handler. The `portOpen()` check prevents drops on first dump (ServerDumper's built-in fallback only kicks in after first failure).

## Configuration

Host configured via `config/solo.php`:
```php
'dump_server_host' => 'tcp://127.0.0.1:9984'
```

## Testing Notes

Integration tests spawn real processes to test the server/client interaction. Tests use Orchestra Testbench with process helpers.
