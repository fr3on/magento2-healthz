<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Fr3on\Healthz\Model\Config;

class DiskSpaceCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly Config $config
    ) {}

    public function getName(): string
    {
        return 'disk_space';
    }

    public function isCritical(): bool
    {
        return false;
    }

    public function run(): CheckResult
    {
        $start = hrtime(true);
        $path = BP . '/var';
        
        if (!is_dir($path)) {
            return CheckResult::warn('disk_space check: var/ directory not found', $this->ms($start));
        }

        $freeBytes = disk_free_space($path);
        if ($freeBytes === false) {
            return CheckResult::warn('disk_free_space() returned false', $this->ms($start));
        }

        $thresholdGb = $this->config->getDiskThresholdGb();
        $thresholdBytes = $thresholdGb * 1024 * 1024 * 1024;
        $freeGb = round($freeBytes / (1024 ** 3), 1);
        
        $metadata = [
            'path' => 'var/',
            'free_gb' => $freeGb,
            'threshold_gb' => $thresholdGb
        ];

        if ($freeBytes < $thresholdBytes) {
            return CheckResult::warn(
                "disk space below threshold: {$freeGb}GB free",
                $this->ms($start),
                $metadata
            );
        }

        return CheckResult::ok($this->ms($start), $metadata);
    }
}
