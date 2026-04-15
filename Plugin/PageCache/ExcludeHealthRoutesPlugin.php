<?php
namespace Fr3on\Healthz\Plugin\PageCache;

use Magento\PageCache\Model\Config;
use Magento\Framework\App\RequestInterface;

class ExcludeHealthRoutesPlugin
{
    public function __construct(
        private readonly RequestInterface $request
    ) {}

    /**
     * Disable Full Page Cache for health check routes.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param Config $subject
     * @param bool $result
     * @return bool
     */
    public function afterIsEnabled(Config $subject, bool $result): bool
    {
        if (str_starts_with($this->request->getRequestUri(), '/_health')) {
            return false;
        }
        return $result;
    }
}
