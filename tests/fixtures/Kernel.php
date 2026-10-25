<?php

declare(strict_types=1);

/**
 * Derafu: Symfony Bundle Skeleton - Template of a Derafu Symfony bundle.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsProjectBundle\Fixture;

use Derafu\ProjectBundle\ProjectBundle;
use Derafu\ProjectBundle\Service\GreetingService;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

/**
 * Real consuming application used to boot the bundle in integration tests.
 */
final class Kernel extends BaseKernel
{
    /**
     * @param string $projectDirectory Isolated directory for this application.
     * @param array<string, mixed> $bundleConfig Application bundle options.
     */
    public function __construct(
        private readonly string $projectDirectory,
        private readonly array $bundleConfig = [],
    ) {
        parent::__construct('test', true);
    }

    /**
     * {@inheritDoc}
     *
     * @return iterable<BundleInterface>
     */
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new ProjectBundle();
    }

    /**
     * Registers genuine application requirements and a public test consumer.
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', ['secret' => 'bundle-test-secret']);
            $container->loadFromExtension('derafu_project', $this->bundleConfig);
            // The package's own service and alias remain private. Only this
            // application alias makes them reachable for the integration test.
            $container->setAlias('test.greeting', GreetingService::class)->setPublic(true);
        });
    }

    /**
     * Returns the isolated application's project directory.
     */
    public function getProjectDir(): string
    {
        return $this->projectDirectory;
    }

    /**
     * Keeps compiled containers inside the isolated application.
     */
    public function getCacheDir(): string
    {
        return $this->projectDirectory . '/var/cache';
    }

    /**
     * Keeps logs inside the isolated application.
     */
    public function getLogDir(): string
    {
        return $this->projectDirectory . '/var/log';
    }
}
