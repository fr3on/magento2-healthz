<?php
namespace Fr3on\Healthz\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private const XML_PATH_ENABLED           = 'healthz/general/enabled';
    private const XML_PATH_DETAIL_TOKEN     = 'healthz/general/detail_token';
    private const XML_PATH_DISK_THRESHOLD   = 'healthz/general/disk_threshold_gb';
    private const XML_PATH_CHECK_TIMEOUT    = 'healthz/general/check_timeout_ms';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED);
    }

    public function getDetailToken(): string
    {
        return (string)($this->scopeConfig->getValue(self::XML_PATH_DETAIL_TOKEN) ?? '');
    }

    public function getDiskThresholdGb(): float
    {
        return (float)($this->scopeConfig->getValue(self::XML_PATH_DISK_THRESHOLD) ?: 1.0);
    }

    public function getCheckTimeoutMs(): int
    {
        return (int)($this->scopeConfig->getValue(self::XML_PATH_CHECK_TIMEOUT) ?: 3000);
    }
}
