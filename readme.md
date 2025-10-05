# 🧠 Scollio Logger

**Scollio Logger** is a modern, database-backed logging package for Laravel with a beautiful built-in dashboard for managing and filtering your application's logs.  
It’s **PSR-3 compliant**, supports all standard log levels, and is optimized for both performance and flexibility.

---

## ✨ Features

- **Database-Backed Logging** — Store logs in a dedicated database table for structured analysis.
- **Interactive Dashboard** — A responsive, theme-aware web interface (light & dark mode).
- **Advanced Filtering** — Filter logs by level, channel, date range, or location.
- **Live Updates** — Automatically refreshes with new log entries in real-time.
- **Request Logging Middleware** — Captures incoming HTTP requests with controller, action, and timing details.
- **Exception Logging** — Logs both uncaught and manually caught exceptions with full context.
- **Detailed Request Context** — Includes controller names, file paths, line numbers, query params, and more.
- **Highly Configurable** — Customize routes, titles, retention, and environment behavior.
- **Performance Optimized** — Includes sensitive data filtering and route exclusion.
- **PSR-3 Compliant** — Supports `emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug`.

---

## 📚 Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Publishing Assets](#publishing-assets)
- [Configuration](#configuration)
- [Environment Configuration](#environment-configuration)
- [Basic Usage](#basic-usage)
- [Request Logging](#request-logging)
- [Exception Logging](#exception-logging)
- [Dashboard](#dashboard)
- [Screenshots](#screenshots)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## 🧩 Requirements

- **PHP** ≥ 8.0  
- **Laravel** ≥ 9.0  
- **Database:** MySQL, PostgreSQL, SQLite, or SQL Server

---

## ⚙️ Installation

### 1. Install via Composer

```bash
composer require kz370/scollio-logger
```

### 2. For local development (optional)

Update `composer.json`:

```json
"repositories": [
    {
        "type": "path",
        "url": "../packages/scollio-logger"
    }
]
```

Then install:

```bash
composer require kz370/scollio-logger:@dev
```

---

## 🏗️ Publishing Assets

Publish configuration and migrations:

```bash
php artisan vendor:publish --tag=scollio-logger-config
php artisan vendor:publish --tag=scollio-logger-migrations
```

Then run migrations:

```bash
php artisan migrate
```

---

## ⚙️ Configuration

All settings are available in `config/scollio-logger.php`.

### Core Settings

| Key | Description |
|-----|-------------|
| `enabled` | Enable or disable the logging package globally |
| `title` | Dashboard title (defaults to `APP_NAME`) |
| `table` | Database table name for logs |
| `retention_days` | Auto-delete logs after X days (or `null` to keep indefinitely) |
| `channels` | Default log channels |
| `levels` | Supported log levels |
| `theme_support` | Enable light/dark theme switcher |

### Dashboard

| Key | Description |
|-----|-------------|
| `enabled` | Enable or disable dashboard routes |
| `route` | Dashboard URL (default: `/scollio-logs/dashboard`) |
| `prefix` | Route prefix for dashboard |
| `middleware` | Middleware stack for dashboard routes |
| `pagination` | Logs per page |
| `theme` | Default theme: `light`, `dark`, or `auto` |

### Request Logging

| Key | Description |
|-----|-------------|
| `enabled` | Enable request logging |
| `log_api` | Log API (`api/*`) routes |
| `log_web` | Log web routes |
| `log_payload` | Include request payload |
| `log_headers` | Include request headers |
| `skip_routes` | Routes to exclude |
| `sensitive_keys` | Filtered request data keys |

### Exception Logging

| Key | Description |
|-----|-------------|
| `enabled` | Enable exception logging |
| `log_api_exceptions` | Log API exceptions |
| `log_web_exceptions` | Log web exceptions |
| `log_stack_trace` | Include stack traces |
| `log_request_data` | Include request data |
| `channel` | Channel name (default: `exception_logging`) |
| `skip_routes` | Routes to skip |
| `only_routes` | Only log specified routes |
| `ignore_exceptions` | Exceptions to ignore |
| `only_exceptions` | Only log specified exceptions |
| `exception_levels` | Map exception types to levels |
| `sensitive_keys` | Keys to hide in logged data |

---

## 🌍 Environment Configuration

Example `.env` configuration:

```env
# Core
SCOLLIO_LOGGER_ENABLED=true
SCOLLIO_LOGGER_RETENTION_DAYS=30

# Dashboard
SCOLLIO_LOGGER_DASHBOARD_ENABLED=true
SCOLLIO_LOGGER_DASHBOARD_ROUTE=scollio-logs/dashboard
SCOLLIO_LOGGER_DASHBOARD_PAGINATION=15
SCOLLIO_LOGGER_DASHBOARD_THEME=auto

# Request Logging
SCOLLIO_REQUEST_LOGGING_ENABLED=true
SCOLLIO_REQUEST_LOGGING_API=true
SCOLLIO_REQUEST_LOGGING_WEB=false
SCOLLIO_REQUEST_LOGGING_PAYLOAD=false
SCOLLIO_REQUEST_LOGGING_HEADERS=false

# Exception Logging
SCOLLIO_EXCEPTION_LOGGING_ENABLED=true
SCOLLIO_EXCEPTION_LOGGING_API=true
SCOLLIO_EXCEPTION_LOGGING_WEB=true
SCOLLIO_EXCEPTION_LOGGING_STACK_TRACE=true
SCOLLIO_EXCEPTION_LOGGING_REQUEST_DATA=true
SCOLLIO_EXCEPTION_LOGGING_CHANNEL=exception_logging

# Theme
SCOLLIO_LOGGER_THEME_SUPPORT=true
```

---

## 🧠 Basic Usage

### Using the Facade

```php
use Scollio\Facades\Logger;

Logger::info('User logged in', ['user_id' => 5]);
Logger::error('Payment failed', ['order_id' => 123], 'OrderController::processPayment', 'payments');
```

### Using the Service Container

```php
$logger = app('scollio-logger');
$logger->warning('API endpoint returned a 500 status', ['endpoint' => '/api/v1/data']);
```

---

## 🛰️ Request Logging

The `GlobalRequestLogger` middleware automatically logs all incoming HTTP requests.

### Example Log

```json
{
  "type": "api",
  "method": "GET",
  "url": "http://example.com/api/users/123",
  "controller_action": "App\\Http\\Controllers\\UserController@show",
  "status_code": 200,
  "execution_time": "125.45ms"
}
```

### Enable Request Logging

```env
SCOLLIO_REQUEST_LOGGING_ENABLED=true
SCOLLIO_REQUEST_LOGGING_API=true
SCOLLIO_REQUEST_LOGGING_WEB=false
```

---

## ⚡ Exception Logging

### 1. Automatic (Uncaught Exceptions)

Enable in `.env`:

```env
SCOLLIO_EXCEPTION_LOGGING_ENABLED=true
```

note:
this doesnt work with try/catch blocks to make it work you have to use the ScollioException Support Class

### 2. Manual (Caught Exceptions)

```php
use Scollio\Support\ScollioException;

try {
    // ...
} catch (\Exception $e) {
    ScollioException::log($e);
}
```

### Multiple Exception Types

```php
try {
    // ...
} catch (ModelNotFoundException $e) {
    ScollioException::log($e);
} catch (ValidationException $e) {
    ScollioException::log($e);
} catch (\Exception $e) {
    ScollioException::log($e);
}
```

---

## 🖥️ Dashboard

Visit your dashboard at:  
➡️ **`/scollio-logs/dashboard`**

### Key Features

- Filter logs by level, channel, date, and search term  
- Sort ascending/descending  
- View detailed log info & stack traces  
- Delete individual or all logs  
- Live refresh  
- Dark/light mode toggle  
- Fully responsive

---

## 🖼️ Screenshots

<p align="center">
  <img src="https://raw.githubusercontent.com/kz370/scrollio-logger/refs/heads/main/dashboard-screenshot.jpeg" alt="Scollio Logger Dashboard" width="48%">
  <img src="https://raw.githubusercontent.com/kz370/scrollio-logger/refs/heads/main/single-log-screenshot.png" alt="Single Log Entry View" width="48%">
</p>

---

## 🧩 Troubleshooting

| Issue | Possible Fix |
|--------|---------------|
| **Dashboard not accessible** | Check `SCOLLIO_LOGGER_DASHBOARD_ENABLED=true` |
| **Logs not appearing** | Run migrations and ensure `SCOLLIO_LOGGER_ENABLED=true` |
| **Request logs missing** | Clear config cache: `php artisan config:clear` |
| **Exception logs missing** | Use `ScollioException::log($e)` for manual logging |
| **Performance issues** | Exclude routes or disable payload/header logging |

---

## 📜 License

**Scollio Logger** is open-source software licensed under the [MIT License](LICENSE).

---

> 🧩 _Built for Laravel developers who need smarter, structured, and searchable application logging._
