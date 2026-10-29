<?php

declare(strict_types=1);

/**
 * Derafu: Symfony Bundle Skeleton - Template of a Derafu Symfony bundle.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\ProjectBundle\Service;

use Derafu\ProjectBundle\Contract\GreetingServiceInterface;

/**
 * Example service whose message comes from the bundle configuration.
 */
final readonly class GreetingService implements GreetingServiceInterface
{
    /**
     * @param string $message Message configured by the consuming application.
     */
    public function __construct(private string $message)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function greet(): string
    {
        return $this->message;
    }
}
