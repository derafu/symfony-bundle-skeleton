<?php

declare(strict_types=1);

/**
 * Derafu: Symfony Bundle Skeleton - Template of a Derafu Symfony bundle.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\TestsProjectBundle;

use Derafu\ProjectBundle\Service\GreetingService;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Tests the actual service YAML separately from bundle configuration parsing.
 * The consuming application supplies its parameter; package wiring is retained.
 * This YAML declares no tags, so tag-consumption tests are not applicable.
 */
#[CoversNothing]
final class ServicesTest extends TestCase
{
    private function loadContainer(): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('derafu_project.message', 'A greeting from the consumer.');
        (new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 2) . '/resources/config')))
            ->load('project-bundle-services.yaml');

        return $container;
    }

    public function testTheConfigurationKeepsItsServiceAndAliasPrivate(): void
    {
        $container = $this->loadContainer();

        self::assertFalse($container->getDefinition('derafu_project.greeting')->isPublic());
        self::assertFalse($container->getAlias(GreetingService::class)->isPublic());
    }

    public function testTheServiceAndAliasResolveToTheSameConfiguredInstance(): void
    {
        $container = $this->loadContainer();
        // Keep all declared services and aliases reachable during compilation.
        foreach ($container->getDefinitions() as $definition) {
            $definition->setPublic(true);
        }
        foreach ($container->getAliases() as $alias) {
            $alias->setPublic(true);
        }
        $container->compile();
        $service = $container->get('derafu_project.greeting');
        self::assertInstanceOf(GreetingService::class, $service);
        self::assertSame($service, $container->get(GreetingService::class));
        self::assertSame('A greeting from the consumer.', $service->greet());
    }
}
