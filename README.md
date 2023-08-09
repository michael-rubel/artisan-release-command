# Artisan Release Command
[![tests](https://github.com/michael-rubel/artisan-release-command/actions/workflows/tests.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/tests.yml)
[![infection](https://github.com/michael-rubel/artisan-release-command/actions/workflows/infection.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/infection.yml)
[![backward-compat](https://github.com/michael-rubel/artisan-release-command/actions/workflows/backward-compat.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/backward-compat.yml)
[![phpstan](https://github.com/michael-rubel/artisan-release-command/actions/workflows/phpstan.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/phpstan.yml)

Artisan command to create a release.

### Prerequisites

- `git`
- GitHub CLI
- Access to a version file (Linux permissions).

---

The package requires PHP `8.1` or higher and Laravel `10.8` or higher.

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

## Installation
Install the package using composer:
```bash
composer require michael-rubel/artisan-release-command
```

## Usage
The command will bump version based on the [SemVer 2.0](https://semver.org/) type you'll provide to the command:

```php
php artisan release {type}
```

It will pick the latest release you have in the versioning file and bump it a step further.

### Examples
```php
php artisan release major
```
```php
php artisan release minor
```
```php
php artisan release patch
```

Publish the config if you need to customize the command:
```bash
php artisan vendor:publish --tag="artisan-release-config"
```

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
