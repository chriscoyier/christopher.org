<?php
/**
 * @license GPL-3.0
 *
 * Modified by Team Caseproof using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Prli\GroundLevel\Container\Contracts;

use Prli\GroundLevel\Container\Container;

interface ConfiguresParameters
{
    /**
     * Configures the dependency's parameters.
     *
     * If a default parameter already exists on the container, it will not be overwritten,
     * otherwise it will be added to the container using the default value.
     *
     * @param \Prli\GroundLevel\Container\Container $container The container.
     */
    public function configureParameters(Container $container): void;

    /**
     * Returns a key=>value list of default parameters.
     *
     * @return array
     */
    public function getDefaultParameters(): array;
}
