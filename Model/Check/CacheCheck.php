<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\CacheInterface;

class CacheCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly CacheInterface $cache
    ) {}

    public function getName(): string
    {
        return 'cache';
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
            $testKey = 'healthz_' . time();
            $this->cache->save('1', $testKey, [], 10);
            $result = $this->cache->load($testKey);
            $this->cache->remove($testKey);

            if ($result !== '1') {
                return CheckResult::fail('cache read/write roundtrip failed', $this->getDurationMs($start));
            }

            $metadata = ['backend' => get_class($this->cache)];
            return CheckResult::ok($this->getDurationMs($start), $metadata);
        } catch (\Throwable $e) {
            return CheckResult::fail('cache: ' . $e->getMessage(), $this->getDurationMs($start));
        }
    }
}
