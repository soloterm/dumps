---
title: Installation
description: How to install and set up the Dumps package in your Laravel application.
---

# Installation

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.1 or higher |
| Laravel | 10, 11, or 12 |

Dumps is a pure PHP package with no additional system dependencies.

## Install via Composer

Install as a development dependency:

```bash
composer require soloterm/dumps --dev
```

The `--dev` flag ensures Dumps is only installed in development environments, keeping your production deployments lean.

## Automatic Setup

Dumps uses Laravel's package auto-discovery, so it's ready to use immediately after installation. No manual service provider registration required.

The package automatically:

1. Registers the `DumpServiceProvider`
2. Intercepts all `dump()` calls in your application
3. Registers the `solo:dumps` Artisan command

## Verify Installation

Start the dump server:

```bash
php artisan solo:dumps
```

You should see:

```
Dump server listening on tcp://127.0.0.1:9984
```

In another terminal, create a test route or use Tinker:

```bash
php artisan tinker
>>> dump(['test' => 'data']);
```

The dump should appear in your dump server terminal.

## Publish Configuration (Optional)

If you need to customize the dump server host or port:

```bash
php artisan vendor:publish --tag=solo-config
```

This creates `config/solo.php` where you can configure the server address:

```php
return [
    'dump_server_host' => env('SOLO_DUMP_SERVER_HOST', 'tcp://127.0.0.1:9984'),
];
```

Most users don't need to change this—the default port works well for local development.

## Uninstalling

To remove Dumps:

```bash
composer remove soloterm/dumps
```

If you published the config file:

```bash
rm config/solo.php
```

## Troubleshooting

### "Command not found: solo:dumps"

Clear the application cache:

```bash
php artisan cache:clear
composer dump-autoload
```

### Dumps not appearing in server

1. Ensure the dump server is running (`php artisan solo:dumps`)
2. Check that the port isn't blocked by a firewall
3. Verify no other service is using port 9984

### Port already in use

Another process is using port 9984. Either stop that process or configure a different port:

```env
SOLO_DUMP_SERVER_HOST=tcp://127.0.0.1:9985
```

## Next Steps

- [Usage](usage) - Learn how to use Dumps effectively
- [Configuration](configuration) - Customize Dumps for your needs
- [Solo Integration](solo-integration) - Use Dumps with Solo
