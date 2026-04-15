<?php
namespace Fr3on\Healthz\Controller\Health;

use DateTime;
use Fr3on\Healthz\Model\Config;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Live implements HttpGetActionInterface, CsrfAwareActionInterface
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

    public function createException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
