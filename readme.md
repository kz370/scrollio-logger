### Scollio Logger

**Scollio Logger** is a modern, database-backed logging package for Laravel that includes a built-in dashboard for managing and filtering your application's logs. It is PSR-3 compliant and supports all standard log levels.

-----

### Features 🚀

* **Database-backed logging:** Stores logs in a dedicated database table.
* **Interactive Dashboard:** A responsive web interface with support for light and dark themes.
* **Advanced Filtering:** Easily filter logs by level, channel, date range, and location.
* **Live Updates:** The dashboard can automatically refresh to show new log entries in real-time.
* **Highly Configurable:** Offers configurable route prefixes, dashboard titles, log retention policies, and environment-based configuration.
* **Performance Optimized:** Includes route filtering and sensitive data protection to minimize performance impact.
* **Request Logging Middleware:** A dedicated middleware can be enabled to automatically log every incoming request.
* **PSR-3 Compliant:** Supports all standard log levels: `emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, and `debug`.

---

### Table of Contents

* [Requirements](#requirements)
* [Installation](#installation)
* [Publishing Assets](#publishing-assets)
* [Configuration](#configuration)
* [Environment Configuration](#environment-configuration)
* [Basic Usage](#basic-usage)
* [Global Request Logging Middleware](#global-request-logging-middleware)
* [Dashboard](#dashboard)
* [Screenshots](#screenshots)
* [Troubleshooting](#troubleshooting)
* [License](#license)

---


### Requirements

  - PHP 8.0 or higher
  - Laravel 9.0 or higher
  - MySQL, PostgreSQL, SQLite, or SQL Server database

-----

### Installation

1.  **Install the package via Composer:**

    ```bash
    composer require kz370/scollio-logger
    ```

    If using a local path repository for development, update your `composer.json` file:

    ```json
    "repositories": [
        {
            "type": "path",
            "url": "../packages/scollio-logger"
        }
    ]
    ```

    Then, require the package using the dev-master alias:

    ```bash
    composer require kz370/scollio-logger:@dev
    ```

-----

### Publishing Assets

Publish the configuration and migration files with the following commands. The package views are already loaded by default.

```bash
php artisan vendor:publish --tag=scollio-logger-config
php artisan vendor:publish --tag=scollio-logger-migrations
```

After publishing the migrations, run them to create the `scollio_logs` database table:

```bash
php artisan migrate
```

-----

### Configuration

The primary configuration is handled in the `config/scollio-logger.php` file, which is published to your application's `config` directory. This file is the central place to customize the package's behavior.

#### Core Settings

  - `enabled`: A boolean to globally **enable or disable** the entire logging package.
  - `title`: The title displayed on the log dashboard page. By default, it uses your `APP_NAME`.
  - `table`: The name of the database table used to store log entries.
  - `retention_days`: Sets the number of days after which log entries will be automatically deleted. If `null`, logs are kept indefinitely. You can schedule a command to run this cleanup.
  - `channels`: An array of default log channels that can be filtered on the dashboard.
  - `levels`: An array of log levels supported by the package. This is used for filtering on the dashboard.
  - `theme_support`: A boolean that enables or disables the theme switcher on the dashboard.

#### Dashboard Configuration

The `dashboard` array controls the web interface's behavior.

  - `enabled`: A boolean that enables or disables the log dashboard routes completely.
  - `route`: The URL path to access the dashboard (default: `/scollio-logs/dashboard`).
  - `prefix`: The route prefix for all dashboard-related routes (e.g., `show`, `delete`, `clear`).
  - `middleware`: An array of middleware that will be applied to the dashboard routes. You can use this to protect the dashboard with authentication, authorization, or other middleware.
  - `pagination`: The number of log entries to display per page on the dashboard.
  - `theme`: The default dashboard theme (`light`, `dark`, or `auto`).

#### Middleware Configuration

The `middleware` array configures the `GlobalRequestLogger` middleware.

  - `request_logging.enabled`: A boolean to globally **enable or disable** the request logging middleware. If `true`, it will be pushed onto the global middleware stack.
  - `request_logging.log_api` & `log_web`: Booleans to specify whether the middleware should log API requests (`api/*`) or web requests.
  - `request_logging.log_payload` & `log_headers`: Booleans to control logging of the request body data and headers.
  - `request_logging.skip_routes`: An array of route patterns to ignore for logging. This is a performance optimization for routes like debug bars or package dashboards.
  - `request_logging.sensitive_keys`: An array of keys to filter from the request payload to prevent logging of sensitive data.

#### Colors Configuration

The `colors` array maps each log level to a set of Tailwind CSS classes, which are used to style the log entries on the dashboard.

-----

### Environment Configuration

Most settings can be overridden using environment variables. Add these to your `.env` file to customize the Scollio Logger's behavior without modifying the config file directly.

```dotenv
# Core Logger Settings
SCOLLIO_LOGGER_ENABLED=true
SCOLLIO_LOGGER_TITLE="${APP_NAME} Logs"
SCOLLIO_LOGGER_TABLE=scollio_logs
SCOLLIO_LOGGER_RETENTION_DAYS=30

# Dashboard Configuration
SCOLLIO_LOGGER_DASHBOARD_ENABLED=true
SCOLLIO_LOGGER_DASHBOARD_ROUTE=scollio-logs/dashboard
SCOLLIO_LOGGER_DASHBOARD_PREFIX=scollio-logs
SCOLLIO_LOGGER_DASHBOARD_MIDDLEWARE="web,auth"
SCOLLIO_LOGGER_DASHBOARD_PAGINATION=15
SCOLLIO_LOGGER_DASHBOARD_THEME=auto

# Request Logging Middleware
SCOLLIO_REQUEST_LOGGING_ENABLED=true
SCOLLIO_LOGGER_LOG_API=true
SCOLLIO_LOGGER_LOG_WEB=true
SCOLLIO_LOGGER_LOG_PAYLOAD=true
SCOLLIO_LOGGER_LOG_HEADERS=false

# Theme Support
SCOLLIO_LOGGER_THEME_SUPPORT=true
```

| Variable | Description | Default | Type |
|----------|-------------|---------|------|
| `SCOLLIO_LOGGER_ENABLED` | Enable/disable the entire logging package | `true` | Boolean |
| `SCOLLIO_LOGGER_TITLE` | Dashboard page title | `APP_NAME` | String |
| `SCOLLIO_LOGGER_RETENTION_DAYS` | Auto-delete logs after X days (null = keep forever) | `30` | Integer/null |
| `SCOLLIO_LOGGER_DASHBOARD_ENABLED` | Enable web dashboard | `true` | Boolean |
| `SCOLLIO_LOGGER_DASHBOARD_ROUTE` | Dashboard URL path | `scollio-logs/dashboard` | String |
| `SCOLLIO_REQUEST_LOGGING_ENABLED` | Enable automatic request logging | `true` | Boolean |
| `SCOLLIO_LOGGER_THEME_SUPPORT` | Enable theme switcher | `true` | Boolean |

-----

### Basic Usage

You can use the logger via a dedicated **Facade** or the **service container**.

#### Using the Facade

The Facade provides simple, clean access to the logger.

```php
use Scollio\Facades\Logger;

// Log a simple informational message
Logger::info('User logged in', ['user_id' => 5]);

// Log an error with a custom location and channel
Logger::error('Payment failed', ['order_id' => 123], 'OrderController::processPayment', 'payments');
```

The logger automatically captures the file, line number, IP address, user agent, and user ID of the authenticated user.

#### Using the Service Container

You can also resolve the logger from Laravel's service container:

```php
$logger = app('scollio-logger');
$logger->warning('API endpoint returned a 500 status', ['endpoint' => '/api/v1/data']);
```

-----

### Global Request Logging Middleware

The `GlobalRequestLogger` middleware is included to automatically log incoming requests to your application. Enable this middleware through the `SCOLLIO_REQUEST_LOGGING_ENABLED` environment variable.

The middleware includes logic to prevent sensitive data from being logged by filtering common sensitive keys like `password` and `token`. It also allows you to define routes that should be skipped for performance optimization.

-----

### Dashboard

Access the log dashboard at the URL configured in your environment (default: `/scollio-logs/dashboard`). You can customize this route and middleware in the [Environment Configuration](https://www.google.com/search?q=%23environment-configuration) section.

The dashboard includes a powerful filter system for managing your application logs effectively.

-----


### Screenshots

<div style="display: flex; gap: 20px; justify-content: center;">
<img src="dashboard-screenshot.jpeg" alt="Scollio Logger Dashboard" width="45%">
<img src="single-log-screenshot.png" alt="Single Log Entry View" width="45%">
</div>
<br>

-----

### Troubleshooting

**Dashboard not accessible:**

  - Ensure `SCOLLIO_LOGGER_DASHBOARD_ENABLED=true` in your `.env` file.
  - Check that your middleware configuration allows access to the dashboard routes.

**Logs not appearing:**

  - Verify `SCOLLIO_LOGGER_ENABLED=true` in your environment configuration.
  - Run `php artisan migrate` to ensure the database table exists.
  - Check your database connection.

**Performance issues:**

  - Use the `skip_routes` configuration to exclude high-traffic routes from logging.
  - Consider setting up a scheduled task to clean old logs based on `retention_days`.

-----

### License

This package is open-source software licensed under the [MIT license](https://www.google.com/search?q=LICENSE).