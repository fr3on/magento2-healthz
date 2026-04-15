<?php
namespace Fr3on\Healthz\Model\Check;

trait TimeMeasurementTrait
{
    /**
     * Calculate duration in milliseconds with micro-precision.
     *
     * @param int $startHrTime
     * @return float
     */
    protected function getDurationMs(int $startHrTime): float
    {
        return (float)round((hrtime(true) - $startHrTime) / 1_000_000, 3);
    }
}
