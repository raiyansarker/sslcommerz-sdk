<?php

/**
 * SSLCommerz Refund Response
 *
 * This class handles the response from a refund transaction request.
 * It provides methods to access the status and details of the refund.
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Responses;

use Saloon\Http\Response;

/**
 * Class RefundResponse
 *
 * The Saloon response class for refund requests.
 *
 * @package RaiyanSarker\SSLCommerz\Responses
 * @author Raiyan Sarker
 *
 * @phpstan-type RefundDetail array{
 *     status: string,
 *     bank_tran_id: string,
 *     trans_id: string,
 *     refund_ref_id: string,
 *     refund_amount: string,
 *     error: string|null
 * }
 */
class RefundResponse extends Response
{
    /**
     * Get the status of the refund request.
     *
     * @return string The status (e.g., 'SUCCESS', 'FAILED').
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getStatus(): string
    {
        $status = $this->json('status');
        return is_scalar($status) ? (string) $status : '';
    }

    /**
     * Check if the refund request was successful.
     *
     * @return bool True if successful, false otherwise.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function isSuccess(): bool
    {
        return $this->getStatus() === 'success';
    }

    /**
     * Get the refund reference ID provided by SSLCommerz.
     *
     * @return string|null The refund reference ID if available.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getRefundReferenceId(): ?string
    {
        $value = $this->json('refund_ref_id');
        return is_string($value) ? $value : null;
    }

    /**
     * Get the failure reason if the refund request failed.
     *
     * @return string|null The failure reason if available.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getFailedReason(): ?string
    {
        $value = $this->json('errorReason');
        return is_string($value) ? $value : null;
    }

    /**
     * Get the amount that was successfully refunded.
     *
     * @return float|null The refunded amount if available.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getRefundAmount(): ?float
    {
        $amount = $this->json('refund_amount');
        return is_numeric($amount) ? (float) $amount : null;
    }

    /**
     * Get the full response data as an associative array.
     *
     * @return RefundDetail The complete response data.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getData(): array
    {
        /** @var RefundDetail $data */
        $data = $this->json();
        return $data;
    }
}
