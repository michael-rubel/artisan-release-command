<?php

namespace MichaelRubel\ArtisanRelease\Tests;

use MichaelRubel\ArtisanRelease\ArtisanReleaseServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ArtisanReleaseServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('testing');
    }
}
