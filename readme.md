Got it 🚀 — here’s an updated **README.md** for your `scollio-logger` package that includes:

* Normal installation & usage instructions
* Seeder section (for testing only)
* Screenshot section placeholder

---

````markdown
# Scollio Logger

**Scollio Logger** is a modern, database-backed logging package for Laravel (8.x, 9.x, 10.x, 11.x) with a built-in dashboard for managing and filtering logs.  
It is PSR-3 compliant and supports all standard log levels.

---

## Features

- 📦 Database-backed logging  
- 🎨 Modern responsive dashboard (light & dark theme support)  
- ✅ PSR-3 compliant (`emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug`)  
- 🛠 Configurable middleware, routes, and retention policies  
- 🔍 Dashboard filters (level, date range, channel, location, etc.)  
- 🚀 Works with Laravel 8 → 12 (version-agnostic, tested up through Laravel 12)

---

## Installation

1. Add the package (local path or Packagist, once published):

```bash
composer require kz370/scollio-logger
````

If using locally, update `composer.json`:

```json
"repositories": [
  {
    "type": "path",
    "url": "../packages/scollio-logger"
  }
]
```

Then:

```bash
composer require kz370/scollio-logger:@dev
```

---

## Publish Config, Migrations, and Views

```bash
php artisan vendor:publish --tag=scollio-logger-config
php artisan vendor:publish --tag=scollio-logger-migrations
php artisan vendor:publish --tag=scollio-logger-views
```

Run the migration:

```bash
php artisan migrate
```

---

## Usage

You can use the logger via the **facade** or **service container**:

```php
// Using facade
ScollioLogger::error('Payment failed', 'OrderController::processPayment', ['order_id' => 123]);

// Using container
app('scollio-logger')->info('User logged in', 'AuthController::login', ['user_id' => 5]);
```

---

## Dashboard

After installation, access the dashboard at:

```
/scollio-logs/dashboard
```

You can configure the route prefix and middleware in `config/scollio-logger.php`.

---

## Seeder (Testing Only)

For demo/testing purposes, this package includes a **seeder** that generates 100 random log entries.

Run it with:

```bash
php artisan db:seed --class=ScollioLoggerSeeder
```

⚠️ This is **only for testing/demo** and should **not** be used in production.

---

## Screenshots

📊 *Add your screenshots here to show the dashboard UI (light and dark mode, filtering, etc.)*

Example placeholders:

* Dashboard main view
* Filtering logs by level
* Viewing log details

---

## License

This package is open-source software licensed under the [MIT license](LICENSE).

