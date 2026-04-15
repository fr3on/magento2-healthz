<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;

class QueueCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly DeploymentConfig $deploymentConfig,
        private readonly ResourceConnection $resourceConnection
    ) {}

    public function getName(): string
    {
        return 'queue';
    }

    public function isCritical(): bool
    {
        return false;
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function run(): CheckResult
    {
        $start = hrtime(true);
        try {
            $queueConfig = $this->deploymentConfig->getConfigData('queue/amqp');
            
            if ($queueConfig && isset($queueConfig['host'], $queueConfig['port'])) {
                $host = $queueConfig['host'];
                $port = $queueConfig['port'];
                $connection = @fsockopen($host, $port, $errno, $errstr, 2.0);
                if ($connection) {
                    fclose($connection);
                    return CheckResult::ok($this->getDurationMs($start), ['backend' => 'amqp', 'host' => $host]);
                }
                return CheckResult::fail("amqp connection failed: $errstr", $this->getDurationMs($start));
            }

            // Fallback to DB queue check
            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('queue_message');
            if ($connection->isTableExists($tableName)) {
                $connection->fetchOne("SELECT 1 FROM $tableName LIMIT 1");
                return CheckResult::ok($this->getDurationMs($start), ['backend' => 'db']);
            }

            return CheckResult::warn('queue backend unknown or unreachable', $this->getDurationMs($start));
        } catch (\Throwable $e) {
            return CheckResult::fail('queue: ' . $e->getMessage(), $this->getDurationMs($start));
        }
    }
}
