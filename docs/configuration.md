---
title: Configuration
description: Configure the Dumps package for your Laravel application.
---

# Configuration

Dumps works out of the box with sensible defaults. Most users don't need any configuration. However, you can customize the dump server address if needed.

## Default Configuration

By default, Dumps uses:

```
tcp://127.0.0.1:9984
```

This localhost address and port work for the vast majority of development environments.

## Publishing the Config File

If you need to change the default settings, publish the configuration file:

```bash
php artisan vendor:publish --tag=solo-config
```

This creates `config/solo.php`:

```php
<?php

return [
    /*
     * If you run the solo:dumps command, Solo will start a server to receive
     * the dumps. This is the address. You probably don't need to change
     * this unless the default is already taken for some reason.
     */
    'dump_server_host' => env('SOLO_DUMP_SERVER_HOST', 'tcp://127.0.0.1:9984'),
];
```

## Configuration Options

### dump_server_host

The TCP address where the dump server listens for incoming dumps.

**Default:** `tcp://127.0.0.1:9984`

**Format:** `tcp://host:port`

```php
'dump_server_host' => env('SOLO_DUMP_SERVER_HOST', 'tcp://127.0.0.1:9984'),
```

## Using Environment Variables

Configure via `.env` for environment-specific settings:

```env
SOLO_DUMP_SERVER_HOST=tcp://127.0.0.1:9985
```

This is useful when:

- The default port conflicts with another service
- You need different settings per environment
- You're running multiple Laravel applications simultaneously

## Common Scenarios

### Port Conflict

If port 9984 is already in use:

```env
# .env
SOLO_DUMP_SERVER_HOST=tcp://127.0.0.1:9985
```

Or in the config file:

```php
'dump_server_host' => 'tcp://127.0.0.1:9985',
```

### Multiple Applications

When running multiple Laravel applications, give each a unique port:

**App 1 (.env):**
```env
SOLO_DUMP_SERVER_HOST=tcp://127.0.0.1:9984
```

**App 2 (.env):**
```env
SOLO_DUMP_SERVER_HOST=tcp://127.0.0.1:9985
```

**App 3 (.env):**
```env
SOLO_DUMP_SERVER_HOST=tcp://127.0.0.1:9986
```

Then start separate dump servers for each application.

### Docker/Container Environments

In containerized environments, you might need to bind to `0.0.0.0`:

```env
SOLO_DUMP_SERVER_HOST=tcp://0.0.0.0:9984
```

This allows connections from other containers in the same network.

## Configuration Caching

If you've cached your Laravel configuration:

```bash
php artisan config:cache
```

Remember to clear the cache after changing dump settings:

```bash
php artisan config:clear
```

## Verifying Configuration

Check the current configuration:

```bash
php artisan tinker
>>> config('solo.dump_server_host')
"tcp://127.0.0.1:9984"
```

Or start the dump server to see the address:

```bash
php artisan solo:dumps
# Listening for dumps on tcp://127.0.0.1:9984
```

## No Configuration Needed

For most development setups, you don't need to configure anything. The defaults work well for:

- Local development on macOS, Linux, or Windows (WSL)
- Standard Laravel Valet/Herd setups
- Laravel Sail (Docker-based development)
- Single-application development

Only customize if you encounter port conflicts or have specific networking requirements.

## Next Steps

- [Solo Integration](solo-integration) - Use Dumps with Solo for Laravel
- [How It Works](how-it-works) - Understand the technical implementation
