<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;

interface CheckInterface
{
    /**
     * Unique machine identifier for this check.
     * Used as the key in JSON response.
     * Example: 'database_read', 'cache', 'maintenance_mode'
     */
    public function getName(): string;

    /**
     * Whether this check failing should mark the overall
     * readiness probe as failed.
     */
    public function isCritical(): bool;

    /**
     * Execute the check. Must never throw — catch all exceptions
     * internally and return a failed CheckResult instead.
     * Must respect the timeout configured in module config.
     */
    public function run(): CheckResult;
}
