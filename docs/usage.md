---
title: Usage
description: Learn how to use the dump() function with the Dumps package for effective debugging.
---

# Usage

Dumps intercepts Laravel's `dump()` function and redirects output to a dedicated terminal. This guide covers effective debugging patterns and best practices.

## Basic Usage

### Starting the Dump Server

Open a terminal and start the dump server:

```bash
php artisan solo:dumps
```

You'll see:

```
Listening for dumps on tcp://127.0.0.1:9984
```

Keep this terminal open while developing. All `dump()` calls will appear here.

### Using dump()

Use `dump()` anywhere in your application:

```php
// Simple values
dump('Hello, world!');
dump(42);
dump(true);

// Arrays
dump(['name' => 'John', 'email' => 'john@example.com']);

// Objects
dump($user);
dump($request);

// Multiple values
dump($user, $request, $response);
```

Each dump shows the source location:

```
app/Http/Controllers/UserController.php:24
"Hello, world!"
```

## Common Debugging Patterns

### API Debugging

Debug API endpoints without breaking JSON responses:

```php
public function store(Request $request)
{
    dump('=== Creating Order ===');
    dump($request->validated());

    $order = Order::create($request->validated());

    dump('Order created:', $order->id);

    return response()->json($order, 201);
}
```

Your API returns clean JSON while all debug output goes to the dump server.

### Request Inspection

Inspect incoming requests:

```php
dump([
    'method' => $request->method(),
    'path' => $request->path(),
    'query' => $request->query(),
    'input' => $request->all(),
    'headers' => $request->headers->all(),
    'user' => $request->user()?->id,
]);
```

### Database Query Debugging

Debug Eloquent queries:

```php
// Dump the query before execution
dump(User::where('active', true)->toSql());

// Dump results
$users = User::where('active', true)->get();
dump($users->count() . ' active users found');
dump($users->pluck('email'));
```

### Job Debugging

Debug queued jobs without losing output:

```php
class ProcessPayment implements ShouldQueue
{
    public function handle()
    {
        dump('Processing payment for order: ' . $this->order->id);
        dump('Amount: $' . $this->order->total);

        // Process payment...

        dump('Payment completed successfully');
    }
}
```

### Middleware Debugging

Debug middleware without affecting responses:

```php
class LogRequests
{
    public function handle($request, $next)
    {
        dump('>>> Incoming: ' . $request->method() . ' ' . $request->path());

        $response = $next($request);

        dump('<<< Response: ' . $response->status());

        return $response;
    }
}
```

### Event Debugging

Debug event listeners:

```php
class SendOrderNotification
{
    public function handle(OrderCreated $event)
    {
        dump('OrderCreated event received');
        dump('Order ID: ' . $event->order->id);
        dump('Customer: ' . $event->order->customer->email);

        // Send notification...
    }
}
```

## Labeling Dumps

Use strings to label your dumps for clarity:

```php
dump('=== USER DATA ===');
dump($user);

dump('=== BEFORE TRANSFORM ===');
dump($data);

$transformed = $this->transform($data);

dump('=== AFTER TRANSFORM ===');
dump($transformed);
```

Or use associative arrays:

```php
dump([
    'user' => $user,
    'permissions' => $user->permissions,
    'roles' => $user->roles,
]);
```

## Conditional Debugging

### Debug Specific Conditions

```php
if ($order->total > 1000) {
    dump('High-value order detected', $order);
}
```

### Debug First N Iterations

```php
foreach ($users as $index => $user) {
    if ($index < 3) {
        dump("Processing user {$index}", $user->email);
    }

    // Process user...
}
```

### Environment-Based Debugging

```php
if (app()->environment('local')) {
    dump('Debug info only visible locally', $sensitiveData);
}
```

## dump() vs dd()

| Function | Behavior |
|----------|----------|
| `dump()` | Outputs and continues execution |
| `dd()` | Outputs and stops execution (die) |

`dd()` halts execution immediately, so it outputs in the original context (browser, CLI) rather than the dump server. Use `dump()` when you want to continue execution and see output in the dump server.

```php
// Shows in dump server, continues execution
dump($user);
dump($order);
return $response;

// Shows in browser/CLI, stops here
dd($user);  // Never reaches the next line
dump($order);
```

## Cleaning Up

Remember to remove debugging statements before committing:

```bash
# Find dump() calls in your code
grep -r "dump(" app/
```

Or use your IDE's search functionality to find and remove debug statements.

## Tips

### Keep the Server Running

Leave `php artisan solo:dumps` running in a dedicated terminal or tmux/screen session during development.

### Use Descriptive Labels

Instead of:

```php
dump($x);
dump($y);
```

Use:

```php
dump('Input value:', $x);
dump('Calculated result:', $y);
```

### Dump Collections Smartly

For large collections, dump counts or samples:

```php
$users = User::all();

// Instead of dumping all users
dump('User count: ' . $users->count());
dump('First 3 users:', $users->take(3));
dump('Emails:', $users->pluck('email'));
```

### Debug Without Breaking Tests

Dumps in your application code won't break your test output—they go to the dump server (if running) or fall back silently.

## Next Steps

- [Configuration](configuration) - Customize the dump server settings
- [Solo Integration](solo-integration) - Use Dumps with Solo for Laravel
