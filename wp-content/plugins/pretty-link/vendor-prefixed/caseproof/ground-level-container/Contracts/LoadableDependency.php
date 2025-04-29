<?php
/**
 * @license GPL-3.0
 *
 * Modified by Team Caseproof using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Prli\GroundLevel\Container\Contracts;

use Prli\GroundLevel\Container\Container;

interface LoadableDependency
{
    /**
     * Loads the dependency.
     *
     * This method is called automatically when the dependency is instantiated.
     *
     * @param Container $container The container.
     */
    public function load(Container $container): void;
}
