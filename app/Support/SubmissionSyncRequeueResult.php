<?php

declare(strict_types=1);

namespace App\Support;

final readonly class SubmissionSyncRequeueResult
{
    /**
     * @param  'danger'|'success'|'warning'  $status
     */
    public function __construct(
        public bool $queued,
        public string $message,
        public string $status,
    ) {}

    public static function success(string $message): self
    {
        return new self(true, $message, 'success');
    }

    public static function warning(string $message): self
    {
        return new self(false, $message, 'warning');
    }

    public static function danger(string $message): self
    {
        return new self(false, $message, 'danger');
    }
}
