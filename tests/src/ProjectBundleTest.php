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

use Derafu\ProjectBundle\ProjectBundle;
use Derafu\ProjectBundle\Service\GreetingService;
use Derafu\TestsProjectBundle\Fixture\GreetingConsumer;
use Derafu\TestsProjectBundle\Fixture\Kernel;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Boots the registered bundle in a real FrameworkBundle application.
 */
#[CoversClass(ProjectBundle::class)]
#[CoversClass(GreetingService::class)]
final class ProjectBundleTest extends TestCase
{
    private string $project;

    /** @var array<array-key, mixed> */
    private array $environment;

    /** @var array<array-key, mixed> */
    private array $server;

    private string|false $shellVerbosity;

    protected function setUp(): void
    {
        $this->environment = $_ENV;
        $this->server = $_SERVER;
        $this->shellVerbosity = getenv('SHELL_VERBOSITY');
        $this->project = sys_get_temp_dir() . '/derafu-project-bundle-' . bin2hex(random_bytes(8));
        mkdir($this->project);
    }

    protected function tearDown(): void
    {
        try {
            (new Filesystem())->remove($this->project);
        } finally {
            // Symfony's debug kernel sets SHELL_VERBOSITY. Restore both PHP
            // superglobals and the process environment after the application.
            $_ENV = $this->environment;
            $_SERVER = $this->server;
            putenv($this->shellVerbosity === false ? 'SHELL_VERBOSITY' : 'SHELL_VERBOSITY=' . $this->shellVerbosity);
        }
    }

    public function testTheBundleHasTheExpectedRootPathAndConfigurationAlias(): void
    {
        $bundle = new ProjectBundle();
        self::assertSame(dirname(__DIR__, 2), $bundle->getPath());
        $extension = $bundle->getContainerExtension();
        self::assertNotNull($extension);
        self::assertSame('derafu_project', $extension->getAlias());
        self::assertSame($extension, $bundle->getContainerExtension());
    }

    public function testTheApplicationBootsWithTheDefaultConfiguration(): void
    {
        $kernel = new Kernel($this->project);
        try {
            $kernel->boot();
            self::assertInstanceOf(ProjectBundle::class, $kernel->getBundle('ProjectBundle'));
            $service = $kernel->getContainer()->get('test.greeting');
            self::assertInstanceOf(GreetingService::class, $service);
            self::assertSame('Hello from the project bundle.', $service->greet());
            self::assertSame('Hello from the project bundle.', $kernel->getContainer()->getParameter('derafu.project.message'));
        } finally {
            $kernel->shutdown();
        }
    }

    public function testTheApplicationConfigurationReachesTheService(): void
    {
        $kernel = new Kernel($this->project, ['message' => 'A custom greeting.']);
        try {
            $kernel->boot();
            $service = $kernel->getContainer()->get('test.greeting');
            self::assertInstanceOf(GreetingService::class, $service);
            self::assertSame('A custom greeting.', $service->greet());
            self::assertSame('A custom greeting.', $kernel->getContainer()->getParameter('derafu.project.message'));
        } finally {
            $kernel->shutdown();
        }
    }

    public function testAnApplicationConsumerReceivesThePrivateServiceByInterface(): void
    {
        $kernel = new Kernel($this->project, ['message' => 'A greeting from the consumer.']);
        try {
            $kernel->boot();
            $consumer = $kernel->getContainer()->get(GreetingConsumer::class);
            self::assertInstanceOf(GreetingConsumer::class, $consumer);
            self::assertSame('A greeting from the consumer.', $consumer->render());
        } finally {
            $kernel->shutdown();
        }
    }

    /** @return iterable<string, array{array<string, mixed>}> */
    public static function invalidConfiguration(): iterable
    {
        yield 'empty message' => [['message' => '']];
        yield 'null message' => [['message' => null]];
        yield 'integer message' => [['message' => 12]];
        yield 'array message' => [['message' => []]];
        yield 'boolean message' => [['message' => false]];
        yield 'unknown option' => [['unknown' => 'value']];
    }

    /** @param array<string, mixed> $config */
    #[DataProvider('invalidConfiguration')]
    public function testInvalidConfigurationPreventsApplicationBoot(array $config): void
    {
        $kernel = new Kernel($this->project, $config);
        try {
            $this->expectException(InvalidConfigurationException::class);
            $kernel->boot();
        } finally {
            $kernel->shutdown();
        }
    }
}
