<p align="center">
    <picture>
      <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/soloterm/solo/refs/heads/main/art/solo_logo_dark.png">
      <source media="(prefers-color-scheme: light)" srcset="https://raw.githubusercontent.com/soloterm/solo/refs/heads/main/art/solo_logo_light.png">
      <img alt="Dumps for Laravel" src="https://raw.githubusercontent.com/soloterm/solo/refs/heads/main/art/solo_logo_light.png" style="max-width: 80%; height: auto;">
    </picture>
</p>

<h3 align="center">Intercept dump() calls from your Laravel application</h3>

---

# Dumps for Laravel

## About

Dumps for Laravel is a package that intercepts and collects `dump()` calls from your Laravel application and displays
them in a dedicated terminal window. This eliminates the need to clutter your browser or API responses with debug
output.

This package is especially useful when:

- You're working with APIs where you can't see dumps in the browser
- You want to keep your browser console clean
- You want to centralize all your debugging output in one place
- You need source file information for your dumps


## Installation

1. Require the package:

```shell
composer require soloterm/dumps --dev
```

The package will automatically register the service provider.

## Usage

Simply run:

```shell
php artisan solo:dumps
```

This will start a server that intercepts all `dump()` calls from your Laravel application. The command will keep running
and display any dumps in real-time.

Now when you use `dump()` anywhere in your application, the output will be sent to this terminal window instead of your
browser or API response.

### Features

- Intercepts all `dump()` calls from your Laravel application
- Shows the exact file and line number where the dump was called
- Formats the output using Laravel's CLI dumper for better readability
- Works with APIs, background jobs, and other contexts where dumps are normally hard to see
- Preserves all the functionality of Laravel's dump helper

### Works with Solo for Laravel

If you're using [Solo for Laravel](https://github.com/soloterm/solo), you can add the dumps command to your
configuration:

```php
// config/solo.php

'commands' => [
    // ... other commands
    'Dumps' => 'php artisan solo:dumps',
],
```

## Configuration

You can configure the dump server host in your `config/solo.php` file:

```php
// config/solo.php

return [
    // ... other configurations
    'dump_server_host' => 'tcp://127.0.0.1:9984',
];
```

## How It Works

The package works by:

1. Registering a custom var dumper handler that captures dump calls
2. Resolving the source file and line number of the dump
3. Sending the dump data to a server running in a separate process
4. Displaying the formatted dump with source information in the terminal

## FAQ

#### Does this work with dd()?

No, this package only intercepts `dump()` calls. The `dd()` function will still halt execution as normal.

#### Can I use this with APIs?

Yes! This is one of the main benefits. Your API responses will remain clean while all dumps go to the dedicated terminal
window.

#### Will this affect my application in production?

The package is designed to be used in development only. It's recommended to install it with the `--dev` flag to ensure
it's not included in production.

## Support

This is free! If you want to support me:

- Sponsor my open source work: [aaronfrancis.com/backstage](https://aaronfrancis.com/backstage)
- Check out my courses:
    - [Mastering Postgres](https://masteringpostgres.com)
    - [High Performance SQLite](https://highperformancesqlite.com)
    - [Screencasting](https://screencasting.com)
- Help spread the word about things I make

## Credits

Dumps for Laravel was developed by Aaron Francis. If you like it, please let me know!

- Twitter: https://twitter.com/aarondfrancis
- Website: https://aaronfrancis.com
- YouTube: https://youtube.com/@aarondfrancis
- GitHub: https://github.com/aarondfrancis