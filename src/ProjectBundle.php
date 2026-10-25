<?php

declare(strict_types=1);

/**
 * Derafu: Symfony Bundle Skeleton - Template of a Derafu Symfony bundle.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\ProjectBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Example bundle with semantic configuration and explicit service wiring.
 *
 * Rename the bundle, namespace, alias and parameter/service prefixes together
 * when creating a bundle from this template.
 */
final class ProjectBundle extends AbstractBundle
{
    protected string $extensionAlias = 'derafu_project';

    /**
     * Defines the configuration accepted under `derafu_project`.
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->stringNode('message')
                    ->defaultValue('Hello from the project bundle.')
                    ->cannotBeEmpty()
                ->end()
            ->end()
        ;
    }

    /**
     * Loads the bundle's services using Symfony's validated configuration.
     *
     * The message type is enforced by configure() before this hook is called.
     *
     * @param array{message: string} $config Validated bundle configuration.
     */
    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->import('../resources/config/symfony-project-bundle-services.yaml');
        $configurator->parameters()->set('derafu_project.message', $config['message']);
    }
}
