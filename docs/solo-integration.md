---
title: Solo Integration
description: Use Dumps as a dedicated tab in Solo for Laravel's development dashboard.
---

# Solo Integration

Dumps integrates seamlessly with [Solo for Laravel](https://github.com/soloterm/solo), appearing as a dedicated tab in your development dashboard. This provides a unified view of all your development tools in one terminal interface.

## What is Solo?

Solo is a Laravel package that combines multiple development commands (Vite, logs, queues, tests) into a single tabbed TUI (Terminal User Interface). Instead of managing multiple terminal windows, you run everything in one place.

## Setting Up Dumps in Solo

### Install Both Packages

```bash
composer require soloterm/solo --dev
composer require soloterm/dumps --dev
```

### Publish Solo Configuration

```bash
php artisan vendor:publish --tag=solo-config
```

This creates `config/solo.php` with Dumps pre-configured:

```php
'commands' => [
    'About' => 'php artisan solo:about',
    'Logs' => EnhancedTailCommand::file(storage_path('logs/laravel.log')),
    'Vite' => 'npm run dev',

    // Dumps is configured as a lazy command
    'Dumps' => Command::from('php artisan solo:dumps')->lazy(),
    // ... other commands
],
```

### Start Solo

```bash
php artisan solo
```

Solo launches with all your configured tabs. The Dumps tab is available but doesn't start automatically (it's configured as lazy).

## Using Dumps in Solo

### Switching to the Dumps Tab

Use keyboard shortcuts to navigate:

- Press the number key corresponding to the Dumps tab (e.g., `5`)
- Or use arrow keys to navigate between tabs
- Or use `Ctrl+D` if configured (varies by keybinding preset)

### Starting the Dump Server

Since Dumps is configured as a "lazy" command, it doesn't start when Solo starts. To activate it:

1. Switch to the Dumps tab
2. Press `Enter` or `Space` to start the server

You'll see:

```
Listening for dumps on tcp://127.0.0.1:9984
```

### Viewing Dumps

Once the server is running, any `dump()` calls in your application will appear in this tab:

```
app/Http/Controllers/UserController.php:24
App\Models\User {#1234
  id: 1,
  name: "John Doe",
  email: "john@example.com",
}
```

## Configuration in Solo

### Default Setup

Dumps is pre-configured in Solo's default config:

```php
'Dumps' => Command::from('php artisan solo:dumps')->lazy(),
```

The `->lazy()` modifier means:

- The tab appears in Solo's interface
- The command doesn't auto-start when Solo launches
- You must manually start it when needed
- This saves resources if you don't always need dump output

### Auto-Starting Dumps

If you always want Dumps running, remove the `->lazy()` modifier:

```php
'Dumps' => 'php artisan solo:dumps',
```

Now Dumps starts automatically with Solo.

### Renaming the Tab

Change the array key to customize the tab name:

```php
'Debug Output' => Command::from('php artisan solo:dumps')->lazy(),
```

### Reordering Tabs

Tabs appear in the order defined in the config:

```php
'commands' => [
    'Logs' => EnhancedTailCommand::file(storage_path('logs/laravel.log')),
    'Dumps' => Command::from('php artisan solo:dumps')->lazy(),  // Now second
    'Vite' => 'npm run dev',
    // ...
],
```

## Workflow Example

A typical development session with Solo and Dumps:

1. **Start Solo:**
   ```bash
   php artisan solo
   ```

2. **Work on your application** - Vite, logs, and other auto-start commands are already running

3. **Need to debug something?**
   - Switch to the Dumps tab
   - Press `Enter` to start the dump server
   - Add `dump()` calls to your code
   - See output immediately in the Dumps tab

4. **Switch between tabs** to monitor logs, restart Vite, run tests, etc.

5. **All in one terminal** - no window juggling

## Shared Configuration

Both Solo and Dumps use the same config key for the server address:

```php
// config/solo.php
'dump_server_host' => env('SOLO_DUMP_SERVER_HOST', 'tcp://127.0.0.1:9984'),
```

This ensures the dump server and the dump sender are always on the same address.

## Benefits of Solo Integration

| Standalone | With Solo |
|------------|-----------|
| Dedicated terminal window | Tab in unified dashboard |
| Manual window management | Keyboard navigation |
| Separate from other tools | Alongside logs, Vite, queues |
| Start/stop with artisan | Start/stop with key press |

## Troubleshooting

### Dumps Tab Not Appearing

Ensure Dumps is in your Solo config:

```php
// config/solo.php
'commands' => [
    // ...
    'Dumps' => Command::from('php artisan solo:dumps')->lazy(),
],
```

### Server Not Starting

1. Switch to the Dumps tab
2. Make sure to press `Enter` or `Space` to start (lazy commands don't auto-start)
3. Check for port conflicts if it fails

### Dumps Not Appearing in Tab

1. Verify the server shows "Listening for dumps on tcp://..."
2. Check both Solo and your app are using the same dump server address
3. Make sure you're using `dump()` not `dd()`

## Next Steps

- [How It Works](how-it-works) - Understand the technical implementation
- [Configuration](configuration) - Customize the dump server settings
