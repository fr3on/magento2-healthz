<?php
namespace Fr3on\Healthz\Controller\Live;

use DateTime;
use Fr3on\Healthz\Model\Config;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Index implements HttpGetActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly Config $config
    ) {}

    public function execute(): Json
    {
        $response = $this->jsonFactory->create();
        $response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate', true);
        $response->setHeader('Pragma', 'no-cache', true);

        if (!$this->config->isEnabled()) {
            return $response->setHttpResponseCode(404)
                            ->setData(['error' => 'not found']);
        }

        return $response->setData([
            'status'    => 'ok',
            'timestamp' => (new DateTime())->format('c'),
        ]);
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
