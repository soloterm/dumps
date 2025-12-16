---
title: Introduction
description: Intercept dump() calls from your Laravel application and display them in a dedicated terminal.
---

# Dumps

Dumps is a Laravel package that intercepts `dump()` calls from your application and redirects them to a dedicated terminal window. This keeps your browser responses and API outputs clean while centralizing all debugging output in one place.

## The Problem

Laravel's `dump()` function is invaluable for debugging, but it has limitations:

1. **API responses**: Dumps pollute JSON responses, breaking your API clients
2. **Background jobs**: Dumps in queued jobs disappear into log files or nowhere
3. **Livewire/AJAX**: Dumps can break JavaScript responses
4. **Cluttered output**: Multiple dumps scatter across your browser window

```php
// In an API controller
public function show(User $user)
{
    dump($user);  // This breaks your JSON response!
    dump($user->orders);

    return response()->json($user);
}
```

## The Solution

Dumps redirects all `dump()` output to a separate terminal:

```
┌─────────────────────────────────────────────────────────────┐
│ Terminal: php artisan solo:dumps                            │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ app/Http/Controllers/UserController.php:24                  │
│ App\Models\User {#1234                                      │
│   id: 1,                                                    │
│   name: "John Doe",                                         │
│   email: "john@example.com",                                │
│ }                                                           │
│                                                             │
│ app/Http/Controllers/UserController.php:25                  │
│ Illuminate\Database\Eloquent\Collection {#5678             │
│   ...                                                       │
│ }                                                           │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

Your API response remains clean:

```json
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com"
}
```

## Key Features

### Clean Output

Dumps never appear in your HTTP responses, JSON output, or rendered HTML. Your application behaves exactly as it would without any `dump()` calls.

### Source Tracking

Every dump shows the exact file and line number where it was called:

```
app/Http/Controllers/UserController.php:24
App\Models\User {#1234
  ...
}
```

No more guessing which dump is which.

### Graceful Fallback

If the dump server isn't running, dumps fall back to normal behavior. Your application never breaks—it just works.

### Works Everywhere

Dumps work in contexts where traditional `dump()` fails:

- API endpoints
- Background jobs (queues)
- Artisan commands
- Livewire components
- Event listeners
- Middleware

### Solo Integration

Dumps integrates seamlessly with Solo, appearing as a dedicated tab in your development dashboard.

## Quick Start

Install via Composer:

```bash
composer require soloterm/dumps --dev
```

Start the dump server:

```bash
php artisan solo:dumps
```

Use `dump()` anywhere in your application:

```php
dump($user);
dump(['debug' => 'data']);
dump($request->all());
```

All output appears in your dump server terminal.

## Example Use Cases

### Debugging APIs

```php
public function store(Request $request)
{
    dump($request->validated());  // See input without breaking response

    $order = Order::create($request->validated());

    dump($order);  // Inspect created model

    return response()->json($order, 201);
}
```

### Debugging Jobs

```php
class ProcessPayment implements ShouldQueue
{
    public function handle()
    {
        dump($this->order);  // Visible in dump server
        dump('Processing payment...');

        // Process payment...

        dump('Payment complete!');
    }
}
```

### Debugging Middleware

```php
class LogRequests
{
    public function handle($request, $next)
    {
        dump([
            'method' => $request->method(),
            'path' => $request->path(),
            'user' => $request->user()?->id,
        ]);

        return $next($request);
    }
}
```

## What About dd()?

The `dd()` function (dump and die) halts execution immediately, so it bypasses the dump server. Use `dump()` when you want to see output without stopping your application.

If you need to stop execution, `dd()` still works normally—it just outputs in the original context rather than the dump server.

## Next Steps

- [Installation](installation) - Set up Dumps in your project
- [Usage](usage) - Learn all the ways to use Dumps
- [Solo Integration](solo-integration) - Use Dumps with Solo for Laravel
