<?php
namespace Fr3on\Healthz\Model;

/**
 * @SuppressWarnings(PHPMD.ShortMethodName)
 */
class CheckResult
{
    public const STATUS_OK   = 'ok';
    public const STATUS_FAIL = 'fail';
    public const STATUS_WARN = 'warn';   // non-critical degraded state

    private string $status;
    private int $durationMs;
    private ?string $error;
    private array $metadata;  // extra key/value pairs for detail response

    public function __construct(
        string $status,
        int $durationMs = 0,
        ?string $error = null,
        array $metadata = []
    ) {
        $this->status     = $status;
        $this->durationMs = $durationMs;
        $this->error      = $error;
        $this->metadata   = $metadata;
    }

    public static function ok(int $durationMs = 0, array $metadata = []): self
    {
        return new self(self::STATUS_OK, $durationMs, null, $metadata);
    }

    public static function fail(string $error, int $durationMs = 0): self
    {
        return new self(self::STATUS_FAIL, $durationMs, $error);
    }

    public static function warn(string $message, int $durationMs = 0, array $metadata = []): self
    {
        return new self(self::STATUS_WARN, $durationMs, $message, $metadata);
    }

    public function isOk(): bool   { return $this->status === self::STATUS_OK; }
    public function isFail(): bool { return $this->status === self::STATUS_FAIL; }
    public function getStatus(): string     { return $this->status; }
    public function getDurationMs(): int    { return $this->durationMs; }
    public function getError(): ?string     { return $this->error; }
    public function getMetadata(): array    { return $this->metadata; }

    public function toArray(): array
    {
        $data = ['status' => $this->status, 'duration_ms' => $this->durationMs];
        if ($this->error) {
            $data['error'] = $this->error;
        }
        foreach ($this->metadata as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }
}
