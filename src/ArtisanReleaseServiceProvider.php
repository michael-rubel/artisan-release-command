<?php

declare(strict_types=1);

namespace MichaelRubel\ArtisanRelease;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ArtisanReleaseServiceProvider extends PackageServiceProvider
{
    /**
     * Configure the package.
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('artisan-release-command')
            ->hasConfigFile('artisan-release')
            ->hasCommand(ReleaseCommand::class);
    }
}
