<?php
namespace Fr3on\Healthz\Test\Unit\Model\Check;

use Fr3on\Healthz\Model\Check\CacheCheck;
use Fr3on\Healthz\Model\CheckResult;
use Magento\Framework\App\CacheInterface;
use PHPUnit\Framework\TestCase;

class CacheCheckTest extends TestCase
{
    private $cacheMock;
    private $subject;

    protected function setUp(): void
    {
        $this->cacheMock = $this->createMock(CacheInterface::class);
        $this->subject = new CacheCheck($this->cacheMock);
    }

    public function testRunReturnsOkOnSuccess()
    {
        $this->cacheMock->expects($this->once())->method('save')->willReturn(true);
        $this->cacheMock->expects($this->once())->method('load')->willReturn('1');
        $this->cacheMock->expects($this->once())->method('remove');
        
        $result = $this->subject->run();
        $this->assertEquals(CheckResult::STATUS_OK, $result->getStatus());
    }

    public function testRunReturnsFailOnMismatch()
    {
        $this->cacheMock->method('load')->willReturn('0');
        
        $result = $this->subject->run();
        $this->assertEquals(CheckResult::STATUS_FAIL, $result->getStatus());
        $this->assertStringContainsString('roundtrip failed', $result->getError());
    }

    public function testRunReturnsFailOnException()
    {
        $this->cacheMock->method('save')->willThrowException(new \Exception('Cache down'));
        
        $result = $this->subject->run();
        $this->assertEquals(CheckResult::STATUS_FAIL, $result->getStatus());
        $this->assertStringContainsString('Cache down', $result->getError());
    }
}
