<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\ResourceConnection;

class DatabaseWriteCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly ResourceConnection $resourceConnection
    ) {}

    public function getName(): string
    {
        return 'database_write';
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
        try {
            $connection = $this->resourceConnection->getConnection();
            $connection->query('CREATE TEMPORARY TABLE healthz_write_check (id INT)');
            $connection->query('DROP TEMPORARY TABLE healthz_write_check');
            return CheckResult::ok($this->getDurationMs($start));
        } catch (\Throwable $e) {
            return CheckResult::fail('database write: ' . $e->getMessage(), $this->getDurationMs($start));
        }
    }
}
