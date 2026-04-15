<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Fr3on\Healthz\Model\Config;

class DiskSpaceCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly Config $config,
        private readonly string $checkPath = BP . '/var'
    ) {}

    public function getName(): string
    {
        return 'disk_space';
    }

    public function isCritical(): bool
    {
        return false;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function run(): CheckResult
    {
        $start = hrtime(true);
        $path = $this->checkPath;
        
        if (!is_dir($path)) {
            return CheckResult::warn('disk_space check: var/ directory not found', $this->getDurationMs($start));
        }

        $freeBytes = disk_free_space($path);
        if ($freeBytes === false) {
            return CheckResult::warn('disk_free_space() returned false', $this->getDurationMs($start));
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
                $this->getDurationMs($start),
                $metadata
            );
        }

        return CheckResult::ok($this->getDurationMs($start), $metadata);
    }
}
