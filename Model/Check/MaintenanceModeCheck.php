<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\MaintenanceMode;

class MaintenanceModeCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly MaintenanceMode $maintenanceMode
    ) {}

    public function getName(): string
    {
        return 'maintenance_mode';
    }

    public function isCritical(): bool
    {
        return true;
    }

    public function run(): CheckResult
    {
        $start = hrtime(true);
        if ($this->maintenanceMode->isOn()) {
            return CheckResult::fail('maintenance mode is enabled', $this->ms($start));
        }
        return CheckResult::ok($this->ms($start), ['enabled' => false]);
    }
}
