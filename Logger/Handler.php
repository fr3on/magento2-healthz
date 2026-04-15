<?php
namespace Fr3on\Healthz\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

class Handler extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/healthz.log';

    /**
     * @var int
     */
    protected $loggerType = MonologLogger::ERROR;
}
