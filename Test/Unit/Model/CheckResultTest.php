<?php
namespace Fr3on\Healthz\Test\Unit\Model;

use Fr3on\Healthz\Model\CheckResult;
use PHPUnit\Framework\TestCase;

class CheckResultTest extends TestCase
{
    public function testOkFactory()
    {
        $result = CheckResult::ok(10, ['foo' => 'bar']);
        $this->assertEquals(CheckResult::STATUS_OK, $result->getStatus());
        $this->assertEquals(10, $result->getDurationMs());
        $this->assertEquals(['foo' => 'bar'], $result->getMetadata());
        $this->assertTrue($result->isOk());
        $this->assertFalse($result->isFail());
    }

    public function testFailFactory()
    {
        $result = CheckResult::fail('some error', 5);
        $this->assertEquals(CheckResult::STATUS_FAIL, $result->getStatus());
        $this->assertEquals('some error', $result->getError());
        $this->assertEquals(5, $result->getDurationMs());
        $this->assertTrue($result->isFail());
        $this->assertFalse($result->isOk());
    }

    public function testWarnFactory()
    {
        $result = CheckResult::warn('some warning', 1, ['meta' => 123]);
        $this->assertEquals(CheckResult::STATUS_WARN, $result->getStatus());
        $this->assertEquals('some warning', $result->getError());
        $this->assertFalse($result->isOk());
        $this->assertFalse($result->isFail());
    }

    public function testToArray()
    {
        $result = CheckResult::ok(4, ['k' => 'v']);
        $expected = [
            'status' => 'ok',
            'duration_ms' => 4,
            'k' => 'v'
        ];
        $this->assertEquals($expected, $result->toArray());
    }
}
