<?php

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Responses;

use Saloon\Http\Response;

/**
 * @phpstan-type RefundStatusDetail array{
 *     APIConnect: string,
 *     bank_tran_id: string,
 *     tran_id: string,
 *     refund_ref_id: string,
 *     initiated_on: string,
 *     refunded_on: string,
 *     status: string,
 *     errorReason: string
 * }
 */
class RefundStatusResponse extends Response
{
    public function getStatus(): string
    {
        $status = $this->json('status');
        return is_scalar($status) ? (string) $status : '';
    }

    /** Status is 'refunded' when fully processed. */
    public function isRefunded(): bool
    {
        return $this->getStatus() === 'refunded';
    }

    public function isProcessing(): bool
    {
        return $this->getStatus() === 'processing';
    }

    public function getRefundReferenceId(): ?string
    {
        $value = $this->json('refund_ref_id');
        return is_string($value) ? $value : null;
    }

    public function getErrorReason(): ?string
    {
        $value = $this->json('errorReason');
        return is_string($value) && $value !== '' ? $value : null;
    }
}
