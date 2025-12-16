---
title: How It Works
description: Technical details of how Dumps intercepts and redirects dump() calls.
---

# How It Works

This page explains the technical implementation of Dumps—how it intercepts `dump()` calls and redirects them to a dedicated server.

## Architecture Overview

Dumps consists of two main components:

1. **CustomDumper** - Intercepts `dump()` calls and sends them to the server
2. **Dump Server** - Receives and displays dump output

```
┌─────────────────────┐         ┌─────────────────────┐
│   Your Application  │         │   Dump Server       │
│                     │   TCP   │   (solo:dumps)      │
│   dump($user) ─────────────────────> Display output │
│                     │         │                     │
└─────────────────────┘         └─────────────────────┘
```

## The Interception Layer

### VarDumper Handler

Laravel uses Symfony's VarDumper component for `dump()`. Dumps intercepts this by registering a custom handler:

```php
// Simplified from CustomDumper.php
VarDumper::setHandler(function (mixed $var) use ($cloner, $server) {
    // Clone the variable for safe transmission
    $data = $cloner->cloneVar($var)->withContext([
        'dumpSource' => $this->resolveDumpSource()
    ]);

    // Send to server
    $server->dump($data);
});
```

### Source Resolution

Each dump includes its source location (file and line number):

```
app/Http/Controllers/UserController.php:24
```

Dumps uses Laravel's `CliDumper` to resolve the source:

```php
$data = $cloner->cloneVar($var)->withContext([
    'dumpSource' => $this->resolveDumpSource()
]);
```

This source information is attached to the dump data and transmitted to the server, so you always know where each dump originated.

## The Server Protocol

### Symfony DumpServer

Dumps uses Symfony's DumpServer—a TCP server that receives serialized dump data:

```php
// In the solo:dumps command
$server = new DumpServer('tcp://127.0.0.1:9984');
$server->start();

$server->listen(function (Data $data) use ($dumper) {
    // Extract source from context
    $source = Arr::get($data->getContext(), 'dumpSource');

    // Display with source location
    $dumper->dumpWithSource($data);
});
```

### Data Transmission

The flow for each `dump()` call:

1. **Clone**: VarCloner creates a serializable representation
2. **Context**: Source location is attached
3. **Serialize**: Data is serialized for TCP transmission
4. **Transmit**: Sent to the TCP server
5. **Receive**: Server deserializes the data
6. **Display**: CliDumper renders with colors and formatting

## Graceful Fallback

### Port Checking

Before sending to the server, Dumps checks if the port is open:

```php
protected function portOpen(): bool
{
    $parts = parse_url(static::dumpServerHost());
    $fp = @fsockopen($parts['host'], $parts['port'], $errno, $errstr, 0.1);

    if ($fp) {
        fclose($fp);
        return true;
    }

    return false;
}
```

### Fallback Behavior

If the server isn't running:

```php
$response = $this->portOpen() ? $server->dump($data) : 'server_dump_failed';

if ($response === 'server_dump_failed') {
    // Fall back to original handler (browser/CLI output)
    if (is_callable($original)) {
        $original($var);
    } else {
        print_r($var);
    }
}
```

This ensures:

- **Server running**: Dumps go to the dedicated terminal
- **Server not running**: Dumps appear normally (browser, CLI)
- **Your app never breaks**: No exceptions from missing server

## Service Provider Registration

### Auto-Discovery

Dumps uses Laravel's package auto-discovery. The service provider registers automatically:

```json
// composer.json
"extra": {
    "laravel": {
        "providers": [
            "SoloTerm\\Dumps\\Providers\\DumpServiceProvider"
        ]
    }
}
```

### Boot Sequence

On application boot:

```php
class DumpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register the custom dumper (intercepts all dump() calls)
        CustomDumper::register(
            $this->app->basePath(),
            $this->app['config']->get('view.compiled')
        );

        // Register artisan command
        if ($this->app->runningInConsole()) {
            $this->commands([Dumps::class]);
        }
    }
}
```

## Data Types and Cloning

### VarCloner

The VarCloner creates serializable representations of PHP values:

```php
$cloner = new VarCloner;
$cloner->addCasters(ReflectionCaster::UNSET_CLOSURE_FILE_INFO);
```

This handles:

- **Scalars**: strings, integers, floats, booleans
- **Arrays**: including nested structures
- **Objects**: Eloquent models, collections, any class
- **Resources**: file handles, connections (with limitations)
- **Closures**: Anonymous functions (without file info)

### Casters

Casters transform objects for better display. Laravel's CliDumper includes casters for:

- Eloquent models (shows attributes cleanly)
- Collections (formatted iteration)
- Carbon dates (readable format)
- And many more

## Why TCP?

Dumps uses TCP sockets rather than file-based or shared memory approaches:

| Approach | Pros | Cons |
|----------|------|------|
| TCP Socket | Works across processes, real-time | Needs open port |
| File-based | Simple | Polling delay, file locking |
| Shared Memory | Fast | Complex, limited compatibility |

TCP provides:

- **Real-time display**: No polling delay
- **Process isolation**: Web requests, CLI, jobs all work
- **Simple protocol**: Standard socket communication
- **Cross-context**: Works from any execution context

## Performance Considerations

### Minimal Overhead

When the server is running:

- Port check: ~0.1ms (cached connection attempt)
- Cloning: Proportional to data size
- Transmission: Fast over localhost

When the server isn't running:

- Port check: ~0.1ms (fast failure)
- Fallback: Original behavior, no extra overhead

### Production Safety

Dumps is designed as a dev dependency:

```bash
composer require soloterm/dumps --dev
```

In production:

- Package isn't installed
- No interception occurs
- Zero overhead

## Component Summary

| Component | Purpose |
|-----------|---------|
| `DumpServiceProvider` | Registers CustomDumper and artisan command |
| `CustomDumper` | Intercepts dump(), sends to server with source |
| `Dumps` (command) | TCP server that receives and displays dumps |
| `VarCloner` | Creates serializable data representations |
| `ServerDumper` | TCP client that sends to dump server |
| `CliDumper` | Renders dump output with colors and formatting |

## Sequence Diagram

```
Application                CustomDumper              DumpServer
    │                          │                         │
    │  dump($user)             │                         │
    │─────────────────────────>│                         │
    │                          │                         │
    │                          │  clone($user)           │
    │                          │  attach source          │
    │                          │  check port open        │
    │                          │                         │
    │                          │  TCP: send data         │
    │                          │────────────────────────>│
    │                          │                         │
    │                          │                         │  deserialize
    │                          │                         │  extract source
    │                          │                         │  render output
    │                          │                         │
    │  (continues execution)   │                         │  Display:
    │<─────────────────────────│                         │  UserController:24
    │                          │                         │  User { ... }
```

## Further Reading

- [Symfony VarDumper Component](https://symfony.com/doc/current/components/var_dumper.html)
- [Symfony Dump Server](https://symfony.com/doc/current/components/var_dumper.html#the-dump-server)
- [Laravel CliDumper](https://github.com/laravel/framework/blob/master/src/Illuminate/Foundation/Console/CliDumper.php)
