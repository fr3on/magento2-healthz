<?php
namespace Fr3on\Healthz\Model\Check;

use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ElasticsearchCheck implements CheckInterface
{
    use TimeMeasurementTrait;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    public function getName(): string
    {
        return 'elasticsearch';
    }

    public function isCritical(): bool
    {
        return false;
    }

    public function run(): CheckResult
    {
        $start = hrtime(true);
        try {
            $engine = $this->scopeConfig->getValue('catalog/search/engine');
            
            if (in_array($engine, ['elasticsearch7', 'elasticsearch8', 'opensearch'])) {
                $host = $this->scopeConfig->getValue('catalog/search/elasticsearch7_server_hostname') ?: 'localhost';
                $port = $this->scopeConfig->getValue('catalog/search/elasticsearch7_server_port') ?: '9200';
                
                $connection = @fsockopen($host, $port, $errno, $errstr, 2.0);
                if ($connection) {
                    fclose($connection);
                    return CheckResult::ok($this->ms($start), ['backend' => $engine, 'host' => $host]);
                }
                return CheckResult::fail("$engine connection failed: $errstr", $this->ms($start));
            }

            if ($engine === 'mysql' || !$engine) {
                return CheckResult::ok($this->ms($start), ['backend' => 'mysql', 'note' => 'native mysql search']);
            }

            return CheckResult::warn("unknown search engine: $engine", $this->ms($start));
        } catch (\Throwable $e) {
            return CheckResult::fail('elasticsearch: ' . $e->getMessage(), $this->ms($start));
        }
    }
}
