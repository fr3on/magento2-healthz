<?php
namespace Fr3on\Healthz\Model\Check;

trait TimeMeasurementTrait
{
    /**
     * Calculate duration in milliseconds.
     *
     * @param int $startHrTime
     * @return int
     */
    protected function getDurationMs(int $startHrTime): int
    {
        return (int)((hrtime(true) - $startHrTime) / 1_000_000);
    }
}
