<?php
/**
 * @license GPL-3.0
 *
 * Modified by Team Caseproof using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Prli\GroundLevel\Container\Contracts;

use Prli\GroundLevel\Container\Container;

interface ContainerAwareness
{
    /**
     * Retrieves a container.
     *
     * @return Container
     */
    public function getContainer(): Container;

    /**
     * Sets a container.
     *
     * @param  Container $container The container.
     * @return ContainerAwareness
     */
    public function setContainer(Container $container): ContainerAwareness;
}
