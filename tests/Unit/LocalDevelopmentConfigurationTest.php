<?php

use PHPUnit\Framework\Assert;

test('local development configuration does not require Herd', function () {
    $projectRoot = dirname(__DIR__, 2);
    $composerContents = file_get_contents($projectRoot.'/composer.json');
    $packageContents = file_get_contents($projectRoot.'/package.json');
    $environment = file_get_contents($projectRoot.'/.env.example');
    $agentInstructions = file_get_contents($projectRoot.'/AGENTS.md');

    Assert::assertIsString($composerContents);
    Assert::assertIsString($packageContents);
    Assert::assertIsString($environment);
    Assert::assertIsString($agentInstructions);

    $composer = json_decode($composerContents, true, flags: JSON_THROW_ON_ERROR);
    $package = json_decode($packageContents, true, flags: JSON_THROW_ON_ERROR);

    Assert::assertIsArray($composer);
    Assert::assertIsArray($package);

    $composerScripts = $composer['scripts'] ?? null;
    $packageDevDependencies = $package['devDependencies'] ?? null;

    Assert::assertIsArray($composerScripts);
    Assert::assertIsArray($packageDevDependencies);

    $developmentScript = $composerScripts['dev'] ?? null;
    $setupScript = $composerScripts['setup'] ?? null;
    $analysisScript = $composerScripts['analyse'] ?? null;
    $ciScript = $composerScripts['ci:check'] ?? null;

    Assert::assertIsArray($developmentScript);
    Assert::assertIsArray($setupScript);
    Assert::assertIsArray($analysisScript);
    Assert::assertIsArray($ciScript);

    /** @var list<string> $developmentScript */
    $developmentCommand = implode(' ', $developmentScript);

    /** @var list<string> $setupScript */
    $setupCommand = implode(' ', $setupScript);

    /** @var list<string> $analysisScript */
    $analysisCommand = implode(' ', $analysisScript);

    Assert::assertStringContainsString('php artisan serve', strtolower($developmentCommand));
    Assert::assertStringNotContainsString('herd', strtolower($developmentCommand));
    Assert::assertStringContainsString('database/database.sqlite', $setupCommand);
    Assert::assertStringContainsString('phpstan analyse', $analysisCommand);
    Assert::assertStringContainsString('--memory-limit=1G', $analysisCommand);
    Assert::assertContains('@analyse', $ciScript);
    Assert::assertArrayHasKey('concurrently', $packageDevDependencies);
    Assert::assertStringContainsString('APP_URL=http://127.0.0.1:8000', $environment);
    Assert::assertStringContainsString('DB_CONNECTION=sqlite', $environment);
    Assert::assertStringContainsString('Laravel Herd is not required or assumed for this application.', $agentInstructions);
});
