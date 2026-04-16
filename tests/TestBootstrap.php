<?php

declare(strict_types=1);

use Shopware\Core\TestBootstrapper;

$projectDir = __DIR__ . '/../../../../';

if (is_readable($projectDir . 'vendor/autoload.php')) {
    require $projectDir . 'vendor/autoload.php';
}

$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'test';

return (new TestBootstrapper())
    ->setProjectDir($projectDir)
    ->setForceInstall(false)
    ->addActivePlugins('FibProductLabel')
    ->bootstrap();
