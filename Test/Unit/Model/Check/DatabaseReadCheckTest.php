<?php
namespace Fr3on\Healthz\Test\Unit\Model\Check;

use Fr3on\Healthz\Model\Check\DatabaseReadCheck;
use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use PHPUnit\Framework\TestCase;

class DatabaseReadCheckTest extends TestCase
{
    private $resourceConnectionMock;
    private $connectionMock;
    private $subject;

    protected function setUp(): void
    {
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->createMock(AdapterInterface::class);
        
        $this->resourceConnectionMock->method('getConnection')
            ->willReturn($this->connectionMock);
            
        $this->subject = new DatabaseReadCheck($this->resourceConnectionMock);
    }

    public function testGetName()
    {
        $this->assertEquals('database_read', $this->subject->getName());
    }

    public function testIsCritical()
    {
        $this->assertTrue($this->subject->isCritical());
    }

    public function testRunReturnsOkOnSuccess()
    {
        $this->connectionMock->expects($this->once())
            ->method('fetchOne')
            ->with('SELECT 1');
            
        $result = $this->subject->run();
        $this->assertEquals(CheckResult::STATUS_OK, $result->getStatus());
    }

    public function testRunReturnsFailOnException()
    {
        $this->connectionMock->expects($this->once())
            ->method('fetchOne')
            ->willThrowException(new \Exception('Connection failed'));
            
        $result = $this->subject->run();
        $this->assertEquals(CheckResult::STATUS_FAIL, $result->getStatus());
        $this->assertStringContainsString('Connection failed', $result->getError());
    }
}
