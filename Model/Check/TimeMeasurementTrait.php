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
    protected function ms(int $startHrTime): int
    {
        return (int)((hrtime(true) - $startHrTime) / 1_000_000);
    }
}
