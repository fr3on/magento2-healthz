<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ResourceConnection;

class SessionCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly DeploymentConfig $deploymentConfig,
        private readonly ResourceConnection $resourceConnection
    ) {}

    public function getName(): string
    {
        return 'session';
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
            $saveMethod = $this->deploymentConfig->getConfigData('session/save') ?: 'files';
            
            if ($saveMethod === 'files') {
                $path = BP . '/var/session';
                if (is_dir($path) && is_writable($path)) {
                    return CheckResult::ok($this->getDurationMs($start), ['backend' => 'files']);
                }
                return CheckResult::fail('session directory not writable', $this->getDurationMs($start));
            }

            if ($saveMethod === 'redis') {
                $redisConfig = $this->deploymentConfig->getConfigData('session/redis');
                if ($redisConfig && isset($redisConfig['host'], $redisConfig['port'])) {
                    $host = $redisConfig['host'];
                    $port = $redisConfig['port'];
                    $connection = @fsockopen($host, $port, $errno, $errstr, 2.0);
                    if ($connection) {
                        fclose($connection);
                        return CheckResult::ok($this->getDurationMs($start), ['backend' => 'redis', 'host' => $host]);
                    }
                    return CheckResult::fail("redis session connection failed: $errstr", $this->getDurationMs($start));
                }
            }

            if ($saveMethod === 'db') {
                $connection = $this->resourceConnection->getConnection();
                $tableName = $this->resourceConnection->getTableName('session');
                if ($connection->isTableExists($tableName)) {
                    return CheckResult::ok($this->getDurationMs($start), ['backend' => 'db']);
                }
            }

            return CheckResult::warn("unknown session save method: $saveMethod", $this->getDurationMs($start));
        } catch (\Throwable $e) {
            return CheckResult::fail('session: ' . $e->getMessage(), $this->getDurationMs($start));
        }
    }
}
