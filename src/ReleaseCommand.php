<?php

declare(strict_types=1);

namespace MichaelRubel\ArtisanRelease;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class ReleaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'release {type=patch : major, minor, or patch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bumps the version and creates a release.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');

        // Validate a release type.
        $validTypes = ['major', 'minor', 'patch'];

        if (! in_array($type, $validTypes)) {
            $this->components->error('Invalid version. Allowed types: ' . implode(', ', $validTypes));

            return Command::INVALID;
        }

        // Determine the branch we want to work on.
        $branch = config('artisan-release.branch', default: 'main');

        // Switch to the branch.
        $git = Process::pipe([
            "git switch $branch",
            "git pull origin $branch",
        ]);

        if ($git->failed()) {
            $this->components->error("Unable to switch to $branch or pull: " . $git->errorOutput());

            return Command::FAILURE;
        }

        // Create a SemVer 2.0 version.
        $versionClass = config('artisan-release.version_class', default: 'App\Service');

        $currentVersion = $versionClass::VERSION;

        [$major, $minor, $patch] = explode('.', $currentVersion);

        if ($type === 'major') {
            $major++;
            $minor = 0;
            $patch = 0;
        } elseif ($type === 'minor') {
            $minor++;
            $patch = 0;
        } else {
            $patch++;
        }

        $newVersion = "$major.$minor.$patch";

        // Update version constant in the version file.
        $versionFilePath = config('artisan-release.version_file', default: app_path('Service.php'));

        $versionFileContent = File::get($versionFilePath);

        $updatedVersionFileContent = preg_replace(
            pattern: "/const VERSION = '[0-9]+\.[0-9]+\.[0-9]+';/",
            replacement: "const VERSION = '$newVersion';",
            subject: $versionFileContent,
        ) ?? $versionFileContent;

        File::put($versionFilePath, $updatedVersionFileContent);

        // Commit and push changes.
        $git = Process::pipe([
            "git add $versionFilePath",
            "git commit -m 'Bump version to $newVersion'",
            "git push origin $branch",
        ]);

        if ($git->failed()) {
            $this->components->error('Unable to push changes to the remote repository: ' . $git->errorOutput());

            return Command::FAILURE;
        }

        // Create a release.
        $gh = Process::run("gh release create $newVersion --title 'v$newVersion' --generate-notes");

        if ($gh->failed()) {
            $this->components->error('Unable to create a release using GitHub CLI: ' . $gh->errorOutput());

            return Command::FAILURE;
        }

        // Finish the command.
        $this->components->info("Successfully released the v$newVersion version of the app.");

        return Command::SUCCESS;
    }
}
