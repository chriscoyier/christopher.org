<?php
/**
 * @license GPL-3.0
 *
 * Modified by Team Caseproof using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Prli\GroundLevel\InProductNotifications\Services;

use Prli\GroundLevel\Container\Container;
use Prli\GroundLevel\Container\Contracts\LoadableDependency;
use Prli\GroundLevel\Container\Service;
use Prli\GroundLevel\InProductNotifications\Service as IPNService;
use Prli\GroundLevel\Support\Concerns\Hookable;
use Prli\GroundLevel\Support\Models\Hook;

abstract class ScheduledService extends Service implements LoadableDependency
{
    use Hookable;

    /**
     * The cron recurrence interval.
     *
     * @var string
     */
    protected string $recurrence = 'daily';

    /**
     * Retrieves the hook name for the event action.
     *
     * @return string
     */
    abstract protected function eventName(): string;

    /**
     * Performs the event.
     */
    abstract protected function performEvent(): void;

    /**
     * Configures the hooks for the service.
     *
     * @return array<int, Hook>
     */
    protected function configureHooks(): array
    {
        return [
            new Hook(
                Hook::TYPE_ACTION,
                'init',
                [$this, 'schedule']
            ),
            new Hook(
                Hook::TYPE_ACTION,
                $this->eventHookName(),
                [$this, 'performEvent']
            ),
        ];
    }

    /**
     * Retrieves the hook name for the fetch action.
     *
     * @return string The hook name, eg mepr_ipn_remote_fetch.
     */
    protected function eventHookName(): string
    {
        return $this->container->get(IPNService::class)->prefixId(
            $this->eventName()
        );
    }

    /**
     * Load service dependencies.
     *
     * @param \Prli\GroundLevel\Container\Container $container The container.
     */
    public function load(Container $container): void
    {
        $this->addHooks();
    }

    /**
     * Schedules the fetch cron job.
     */
    public function schedule(): void
    {
        $hook = $this->eventHookName();
        if (! wp_next_scheduled($hook)) {
            wp_schedule_event(time(), $this->recurrence, $hook);
        }
    }
}
