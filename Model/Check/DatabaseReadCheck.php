<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\ResourceConnection;

class DatabaseReadCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {}

    public function getName(): string
    {
        return 'database_read';
    }

    public function isCritical(): bool
    {
        return true;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function run(): CheckResult
    {
        $start = hrtime(true);
        try {
            $connection = $this->resourceConnection->getConnection();
            $connection->fetchOne('SELECT 1');
            return CheckResult::ok($this->getDurationMs($start));
        } catch (\Throwable $e) {
            return CheckResult::fail('database: ' . $e->getMessage(), $this->getDurationMs($start));
        }
    }
}
