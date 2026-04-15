<?php
namespace Fr3on\Healthz\Model;

use Fr3on\Healthz\Model\Check\CheckInterface;

class CheckRegistry
{
    /**
     * @param CheckInterface[] $checks
     */
    public function __construct(
        private readonly Config $config,
        private readonly array $checks = []
    ) {}

    /**
     * @return CheckInterface[]
     */
    public function getAll(): array
    {
        return $this->checks;
    }

    /**
     * @return CheckInterface[]
     */
    public function getCritical(): array
    {
        return array_filter($this->checks, fn($check) => $check->isCritical());
    }

    /**
     * @param string $name
     * @return CheckInterface|null
     */
    public function get(string $name): ?CheckInterface
    {
        return $this->checks[$name] ?? null;
    }

    /**
     * Run a set of checks and return results keyed by check name.
     * Never throws.
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     * @param CheckInterface[] $checks
     * @return array<string, CheckResult>
     */
    public function run(array $checks): array
    {
        $results = [];
        $timeoutSeconds = (int)ceil($this->config->getCheckTimeoutMs() / 1000);
        
        foreach ($checks as $check) {
            // Re-set the time limit for each check
            if ($timeoutSeconds > 0) {
                @set_time_limit($timeoutSeconds);
            }
            $results[$check->getName()] = $check->run();
        }
        return $results;
    }
}
