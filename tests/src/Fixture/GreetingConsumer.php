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

use Derafu\ProjectBundle\Contract\GreetingServiceInterface;

/**
 * An application service that consumes the bundle through its public contract.
 */
final readonly class GreetingConsumer
{
    public function __construct(private GreetingServiceInterface $greeting)
    {
    }

    public function render(): string
    {
        return $this->greeting->greet();
    }
}
