<?php
namespace Fr3on\Healthz\Controller\Ready;

use DateTime;
use Fr3on\Healthz\Model\CheckRegistry;
use Fr3on\Healthz\Model\Config;
use Fr3on\Healthz\Logger\Logger;
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
        private readonly CheckRegistry $checkRegistry,
        private readonly Config $config,
        private readonly Logger $logger
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

        $criticalChecks = $this->checkRegistry->getCritical();
        $results = $this->checkRegistry->run($criticalChecks);
        
        $allOk = true;
        $checksOut = [];
        $firstError = null;

        foreach ($results as $name => $result) {
            $checksOut[$name] = $result->getStatus();
            if ($result->isFail()) {
                $allOk = false;
                if (!$firstError) {
                    $firstError = $name . ': ' . $result->getError();
                }
            }
        }

        $body = [
            'status'    => $allOk ? 'ok' : 'fail',
            'timestamp' => (new DateTime())->format('c'),
            'checks'    => $checksOut,
        ];

        if ($firstError) {
            $body['error'] = $firstError;
            $this->logger->error('Healthz readiness probe failed: ' . $firstError);
        }

        return $response
            ->setHttpResponseCode($allOk ? 200 : 503)
            ->setData($body);
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
