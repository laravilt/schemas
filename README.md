![Schemas](./arts/screenshot.jpg)

# Schemas Plugin for Laravilt

[![Latest Stable Version](https://poser.pugx.org/laravilt/schemas/version.svg)](https://packagist.org/packages/laravilt/schemas)
[![License](https://poser.pugx.org/laravilt/schemas/license.svg)](https://packagist.org/packages/laravilt/schemas)
[![Downloads](https://poser.pugx.org/laravilt/schemas/d/total.svg)](https://packagist.org/packages/laravilt/schemas)
[![Dependabot Updates](https://github.com/laravilt/schemas/actions/workflows/dependabot/dependabot-updates/badge.svg)](https://github.com/laravilt/schemas/actions/workflows/dependabot/dependabot-updates)
[![PHP Code Styling](https://github.com/laravilt/schemas/actions/workflows/fix-php-code-styling.yml/badge.svg)](https://github.com/laravilt/schemas/actions/workflows/fix-php-code-styling.yml)
[![Tests](https://github.com/laravilt/schemas/actions/workflows/tests.yml/badge.svg)](https://github.com/laravilt/schemas/actions/workflows/tests.yml)

Ready-to-use grid system, sections, and layouts designed to support a wide range of layout configurations. Managed by a PHP backend and Laravel, this solution offers high-end customization and is fully compatible with FilamentPHP v4, providing a flexible and powerful foundation for building complex, responsive web layouts.

## Installation

You can install the plugin via composer:

```bash
composer require laravilt/schemas
```

The package will automatically register its service provider which handles all Laravel-specific functionality (views, migrations, config, etc.).

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag="schemas-config"
```

## Assets

Publish the plugin assets:

```bash
php artisan vendor:publish --tag="schemas-assets"
```

## Testing

```bash
composer test
```

## Code Style

```bash
composer format
```

## Static Analysis

```bash
composer analyse
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
