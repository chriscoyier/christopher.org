<?php
/**
 * @license GPL-3.0
 *
 * Modified by Team Caseproof using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Prli\GroundLevel\Container\Concerns;

use Prli\GroundLevel\Container\Container;

trait HasStaticContainer
{
    /**
     * The static container instance.
     *
     * @var Container
     */
    protected static Container $container;

    /**
     * Retrieves a container.
     *
     * @return Container
     */
    public static function getContainer(): Container
    {
        return static::$container;
    }

    /**
     * Sets a container.
     *
     * @param Container $container The container.
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }
}
