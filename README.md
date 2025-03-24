# Uniilara Server

[![CI](https://github.com/rainsonma/uniilara-server/actions/workflows/ci.yml/badge.svg)](https://github.com/rainson/uniilara-server/actions)
[![Latest Stable Version](https://img.shields.io/packagist/v/your-vendor/your-package.svg)](https://packagist.org/packages/your-vendor/your-package)
[![Unstable Version](https://img.shields.io/packagist/vpre/uniilara/server.svg)](https://packagist.org/packages/uniilara/server)
[![Downloads](https://img.shields.io/packagist/dt/uniilara/server.svg)](https://packagist.org/packages/uniilara/server)
[![License](https://img.shields.io/packagist/l/uniilara/server.svg)](https://packagist.org/packages/uniilara/server)

---

A Laravel style package based on [Workerman](https://github.com/walkor/workerman) TCPConneciton for fast, asychronous request handling.

---

## Features

- Simple command to start the server
- Converts Workerman http requests to Symfony and Laravel-compatible requests

---

## Installation

```bash
composer require ...
```

---

## Usage

Start the server:
```bash
php artisan app start
```

Stop and restart:
```bash
php artisan app stop
php artisan app restart
```

---

## Testing

Run PHPUnit tests:
```bash
vendor/bin/phpunit tests
```

## License:

MIT License. See [LICENSE](LICENSE) file for details.