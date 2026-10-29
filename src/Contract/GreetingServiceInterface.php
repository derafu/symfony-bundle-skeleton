<?php

declare(strict_types=1);

/**
 * Derafu: Symfony Bundle Skeleton - Template of a Derafu Symfony bundle.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

namespace Derafu\ProjectBundle\Contract;

/**
 * Provides the greeting configured by the consuming application.
 *
 * Consumers depend on this contract rather than the service implementation.
 */
interface GreetingServiceInterface
{
    /**
     * Returns the configured greeting unchanged.
     *
     * @return string The application's message, without formatting or translation.
     */
    public function greet(): string;
}
