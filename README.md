# Artisan Release Command
[![tests](https://github.com/michael-rubel/artisan-release-command/actions/workflows/tests.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/tests.yml)
[![infection](https://github.com/michael-rubel/artisan-release-command/actions/workflows/infection.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/infection.yml)
[![backward-compat](https://github.com/michael-rubel/artisan-release-command/actions/workflows/backward-compat.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/backward-compat.yml)
[![phpstan](https://github.com/michael-rubel/artisan-release-command/actions/workflows/phpstan.yml/badge.svg)](https://github.com/michael-rubel/artisan-release-command/actions/workflows/phpstan.yml)

Artisan command to create a GitHub release of your code.

### Prerequisites

- GitHub Actions
- GitHub CLI
- Access to a version file (Linux permissions).

---

The package requires PHP `8.1` or higher and Laravel `10.8` or higher.

## #StandWithUkraine
[![SWUbanner](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner2-direct.svg)](https://github.com/vshymanskyy/StandWithUkraine/blob/main/docs/README.md)

## Installation
Install the package using composer:
```bash
composer require michael-rubel/artisan-release-command --dev
```

Publish the config if you need to customize the command:
```bash
php artisan vendor:publish --tag="artisan-release-command-config"
```

## Usage
Setup the GitHub Actions workflow trigger with the following parameters:

```yaml
on:
  push:
    tags:
      - '[0-9]+.[0-9]+.[0-9]+'
```

Or alternatively:
```yaml
on:
  release:
    types:
      - created
```

Create a versioning file, for example:
```php
<?php

namespace App;

abstract class Service
{
    /**
     * App version.
     */
    final public const VERSION = '0.0.1';
}
```

Note: the version file should always contain a `VERSION` constant for the command to work. You can configure the constant name in the configuration file if you want to name it for example "APP_VERSION"

The command will bump version based on the [SemVer 2.0](https://semver.org/) type you'll provide.

```php
php artisan release {type}
```

It will pick the latest release you have in the versioning file, bump it a step further and push the change to the remote repository using your current Git setup. Make sure you're authorized to perform basic operations on your repository.

### For example:
```php
php artisan release major
```

Available options: `major`, `minor`, `patch`

Default value: `patch`

After the version is pushed, it will create a release using GitHub CLI and provide auto-generated notes.

### Beta release

Creates the pre-release based on the given version type:

```php
php artisan release major --beta
```

If you had 0.0.1 in your version file, after this command it will be 1.0.0-beta, and the release will be marked as "pre-release" on GitHub (deployment won't be triggered if you use `[0-9]+.[0-9]+.[0-9]+` pattern in GitHub Actions).

## Testing
```bash
composer test
```

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
