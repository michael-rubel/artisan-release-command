<?php

namespace MichaelRubel\ArtisanRelease\Tests;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use MichaelRubel\ArtisanRelease\ReleaseCommand;

class ReleaseCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Process::preventStrayProcesses();

        $this->versionFilePath = __DIR__ . '/Fakes/Service.php';

        config([
            'artisan-release.branch' => 'main',
            'artisan-release.version_class' => 'MichaelRubel\ArtisanRelease\Tests\Fakes\Service',
            'artisan-release.version_file' => $this->versionFilePath,
        ]);
    }

    public function testCanPublishMajorRelease()
    {
        Process::fake();

        $this->artisan(ReleaseCommand::class, ['type' => 'major'])
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully released the v1.0.0 version of the app.');

        $this->validateAndRevertVersionFile('1.0.0');

        Process::assertRan("gh release create 1.0.0 --title 'v1.0.0' --generate-notes");
    }

    public function testCanPublishMinorRelease()
    {
        Process::fake();

        $this->artisan(ReleaseCommand::class, ['type' => 'minor'])
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully released the v0.1.0 version of the app.');

        $this->validateAndRevertVersionFile('0.1.0');

        Process::assertRan("gh release create 0.1.0 --title 'v0.1.0' --generate-notes");
    }

    public function testCanPublishPatchRelease()
    {
        Process::fake();

        $this->artisan(ReleaseCommand::class, ['type' => 'patch'])
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully released the v0.0.3 version of the app.');

        $this->validateAndRevertVersionFile('0.0.3');

        Process::assertRan("gh release create 0.0.3 --title 'v0.0.3' --generate-notes");
    }

    public function testCanPublishMajorBetaRelease()
    {
        Process::fake();

        $this->artisan(ReleaseCommand::class, ['type' => 'major', '--beta' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully released the v1.0.0-beta version of the app.');

        $this->validateAndRevertVersionFile('1.0.0-beta');

        Process::assertRan("gh release create 1.0.0-beta --title 'v1.0.0-beta' --generate-notes --prerelease");
    }

    public function testCanPublishMinorBetaRelease()
    {
        Process::fake();

        $this->artisan(ReleaseCommand::class, ['type' => 'minor', '--beta' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully released the v0.1.0-beta version of the app.');

        $this->validateAndRevertVersionFile('0.1.0-beta');

        Process::assertRan("gh release create 0.1.0-beta --title 'v0.1.0-beta' --generate-notes --prerelease");
    }

    public function testCanPublishPatchBetaRelease()
    {
        Process::fake();

        $this->artisan(ReleaseCommand::class, ['type' => 'patch', '--beta' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully released the v0.0.3-beta version of the app.');

        $this->validateAndRevertVersionFile('0.0.3-beta');

        Process::assertRan("gh release create 0.0.3-beta --title 'v0.0.3-beta' --generate-notes --prerelease");
    }

    public function testFailedGitSwitchCommand()
    {
        Process::fake([
            'git switch main' => Process::result(
                errorOutput: 'Switch to main failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan(ReleaseCommand::class)
            ->assertFailed()
            ->expectsOutputToContain('Unable to switch to main or pull: Switch to main failed');
    }

    public function testFailedGitPullCommand()
    {
        Process::fake([
            'git switch main' => Process::result(),
            'git pull origin main' => Process::result(
                errorOutput: 'Git pull failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan(ReleaseCommand::class)
            ->assertFailed()
            ->expectsOutputToContain('Unable to switch to main or pull: Git pull failed');
    }

    public function testFailedGitAddCommand()
    {
        Process::fake([
            'git switch *' => Process::result(),
            'git pull *' => Process::result(),
            'git add *' => Process::result(
                errorOutput: 'Git add failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan(ReleaseCommand::class)
            ->assertFailed()
            ->expectsOutputToContain('Unable to push changes to the remote repository: Git add failed');

        $this->validateAndRevertVersionFile();
    }

    public function testFailedGitCommitCommand()
    {
        Process::fake([
            'git switch *' => Process::result(),
            'git pull *' => Process::result(),
            'git add *' => Process::result(),
            'git commit *' => Process::result(
                errorOutput: 'Git commit failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan(ReleaseCommand::class)
            ->assertFailed()
            ->expectsOutputToContain('Unable to push changes to the remote repository: Git commit failed');

        $this->validateAndRevertVersionFile();
    }

    public function testFailedGitPushCommand()
    {
        Process::fake([
            'git switch *' => Process::result(),
            'git pull *' => Process::result(),
            'git add *' => Process::result(),
            'git commit *' => Process::result(),
            'git push *' => Process::result(
                errorOutput: 'Git push failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan(ReleaseCommand::class)
            ->assertFailed()
            ->expectsOutputToContain('Unable to push changes to the remote repository: Git push failed');

        $this->validateAndRevertVersionFile();
    }

    public function testFailedGhReleaseCommand()
    {
        Process::fake([
            'git *' => Process::result(),
            'gh release create *' => Process::result(
                errorOutput: 'Gh release failed',
                exitCode: 1,
            ),
        ]);

        $this->artisan(ReleaseCommand::class)
            ->assertFailed()
            ->expectsOutputToContain('Unable to create a release using GitHub CLI: Gh release failed');

        $this->validateAndRevertVersionFile();
    }

    public function testDoesntAcceptWrongTypes()
    {
        $this->artisan(ReleaseCommand::class, ['type' => 'wrong'])
            ->assertFailed()
            ->expectsOutputToContain('Invalid version. Allowed types: major, minor, patch');
    }

    public function testCanUseDifferentVersionConstName()
    {
        Process::fake();

        config([
            'artisan-release.version_const' => 'APP_VERSION',
        ]);

        $this->artisan(ReleaseCommand::class, ['type' => 'minor'])
            ->assertSuccessful()
            ->expectsOutputToContain('Successfully released the v0.1.0 version of the app.');

        $this->validateAndRevertVersionFile('0.1.0');
    }

    protected function validateAndRevertVersionFile($wantedVersion = '0.0.3')
    {
        $this->versionFile = File::get($this->versionFilePath);

        $this->assertStringContainsString($wantedVersion, $this->versionFile);

        $updatedVersionFileContent = preg_replace(
            pattern: "/VERSION = '[0-9]+\.[0-9]+\.[0-9]+(-beta)?';/",
            replacement: "VERSION = '0.0.2';",
            subject: $this->versionFile,
        );

        File::put($this->versionFilePath, $updatedVersionFileContent);
    }
}
