<?php
/**
 * @license GPL-3.0
 *
 * Modified by Team Caseproof using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Prli\GroundLevel\Container;

use Prli\Psr\Container\ContainerExceptionInterface;
use Exception as BaseException;

class Exception extends BaseException implements ContainerExceptionInterface
{
}
