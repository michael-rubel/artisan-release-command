# Laravel Package Template
[![tests](https://github.com/michael-rubel/laravel-package-template/actions/workflows/tests.yml/badge.svg)](https://github.com/michael-rubel/laravel-package-template/actions/workflows/tests.yml)
[![infection](https://github.com/michael-rubel/laravel-package-template/actions/workflows/infection.yml/badge.svg)](https://github.com/michael-rubel/laravel-package-template/actions/workflows/infection.yml)
[![backward-compat](https://github.com/michael-rubel/laravel-package-template/actions/workflows/backward-compat.yml/badge.svg)](https://github.com/michael-rubel/laravel-package-template/actions/workflows/backward-compat.yml)
[![phpstan](https://github.com/michael-rubel/laravel-package-template/actions/workflows/phpstan.yml/badge.svg)](https://github.com/michael-rubel/laravel-package-template/actions/workflows/phpstan.yml)

It's a ready-to-use template for Laravel packages.

### What's inside
- Skeleton with Service Provider and configuration file
- `Laravel Package Tools` by Spatie for easier package configuration
- Ready-to-use GitHub Action scripts for testing & code quality checks

Fulfill or change it the way you like.

---

The package requires PHP `8.x` and Laravel `9.x`.

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

## Installation
Install the package using composer:
```bash
composer require michael-rubel/laravel-package-template
```

## Usage
```php
// Your description.
```

Publish the config:
```bash
php artisan vendor:publish --tag="package-template-config"
```

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
