<?php
namespace Fr3on\Healthz\Controller\Health;

use DateTime;
use Fr3on\Healthz\Model\CheckRegistry;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Detail implements HttpGetActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly CheckRegistry $checkRegistry,
        private readonly Config $config,
        private readonly ProductMetadataInterface $productMetadata,
        private readonly State $appState,
        private readonly RequestInterface $request
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

        // Optional token check
        $token = $this->config->getDetailToken();
        if (!empty($token)) {
            $provided = $this->request->getHeader('X-Health-Token')
                ?? $this->request->getParam('token');
            if ($provided !== $token) {
                return $response->setHttpResponseCode(401)
                                ->setData(['error' => 'unauthorized']);
            }
        }

        $results = $this->checkRegistry->run($this->checkRegistry->getAll());
        $allOk = true;
        $checksOut = [];

        foreach ($results as $name => $result) {
            $checksOut[$name] = $result->toArray();
            if ($result->isFail()) {
                $allOk = false;
            }
        }

        return $response->setData([
            'status'    => $allOk ? 'ok' : 'fail',
            'timestamp' => (new DateTime())->format('c'),
            'magento'   => [
                'version' => $this->productMetadata->getVersion(),
                'edition' => $this->productMetadata->getEdition(),
                'mode'    => $this->appState->getMode(),
            ],
            'php'       => [
                'version'    => PHP_VERSION,
                'extensions' => get_loaded_extensions(),
            ],
            'checks'    => $checksOut,
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
