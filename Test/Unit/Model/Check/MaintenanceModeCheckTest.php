<?php
namespace Fr3on\Healthz\Test\Unit\Model\Check;

use Fr3on\Healthz\Model\Check\MaintenanceModeCheck;
use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\MaintenanceMode;
use PHPUnit\Framework\TestCase;

class MaintenanceModeCheckTest extends TestCase
{
    private $maintenanceModeMock;
    private $subject;

    protected function setUp(): void
    {
        $this->maintenanceModeMock = $this->createMock(MaintenanceMode::class);
        $this->subject = new MaintenanceModeCheck($this->maintenanceModeMock);
    }

    public function testRunReturnsOkWhenOff()
    {
        $this->maintenanceModeMock->method('isOn')->willReturn(false);
        
        $result = $this->subject->run();
        $this->assertEquals(CheckResult::STATUS_OK, $result->getStatus());
        $this->assertFalse($result->getMetadata()['enabled']);
    }

    public function testRunReturnsFailWhenOn()
    {
        $this->maintenanceModeMock->method('isOn')->willReturn(true);
        
        $result = $this->subject->run();
        $this->assertEquals(CheckResult::STATUS_FAIL, $result->getStatus());
    }
}
