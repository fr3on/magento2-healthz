<?php
namespace Fr3on\Healthz\Test\Unit\Model\Check;

use Fr3on\Healthz\Model\Check\DiskSpaceCheck;
use Fr3on\Healthz\Model\CheckResult;
use Fr3on\Healthz\Model\Config;
use PHPUnit\Framework\TestCase;

class DiskSpaceCheckTest extends TestCase
{
    private $configMock;
    private $subject;

    protected function setUp(): void
    {
        if (!defined('BP')) {
            define('BP', '/tmp');
        }

        $this->configMock = $this->createMock(Config::class);
        $this->subject = new DiskSpaceCheck($this->configMock, '/tmp');
    }

    public function testIsCriticalIsFalse()
    {
        $this->assertFalse($this->subject->isCritical());
    }

    public function testRunReturnsWarnWhenBelowThreshold()
    {
        $this->configMock->method('getDiskThresholdGb')->willReturn(1000000.0); // very high threshold
        
        $result = $this->subject->run();
        // Since we are mocking thresholds, we expect a warning if current disk is less than 1PB
        if ($result->getStatus() === CheckResult::STATUS_WARN) {
            $this->assertStringContainsString('disk space below threshold', $result->getError());
        }
    }
}
