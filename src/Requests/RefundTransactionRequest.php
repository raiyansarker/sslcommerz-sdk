<?php

/**
 * SSLCommerz Refund Transaction Request
 *
 * This request is used to initiate a refund for a previously successful transaction.
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;
use RaiyanSarker\SSLCommerz\Responses\RefundResponse;
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

/**
 * Class RefundTransactionRequest
 *
 * The Saloon request class for refunding a transaction.
 *
 * @package RaiyanSarker\SSLCommerz\Requests
 * @author Raiyan Sarker
 *
 * @phpstan-type RefundRequestBody array{
 *     refund_amount: string,
 *     refund_remarks: string,
 *     bank_tran_id: string,
 *     refe_id: string,
 *     store_id: string,
 *     store_passwd: string,
 *     format: string
 * }
 */
class RefundTransactionRequest extends Request implements HasBody
{
    use HasFormBody;

    /**
     * @var Method The HTTP method for the request.
     */
    protected Method $method = Method::POST;

    /**
     * @var class-string<RefundResponse>|null The response class for this request.
     */
    protected ?string $response = RefundResponse::class;

    /**
     * Constructor for RefundTransactionRequest.
     *
     * @param float $refundAmount The amount to be refunded.
     * @param string $bankTransactionId The bank transaction ID provided by SSLCommerz during payment.
     * @param string $referenceTransactionId The merchant's unique transaction ID (tran_id).
     * @param string $storeId The SSLCommerz store ID.
     * @param string $storePassword The SSLCommerz store password.
     * @param string $refundRemarks Optional remarks for the refund.
     */
    public function __construct(
        /** @var float */
        protected float $refundAmount,
        /** @var string */
        protected string $bankTransactionId,
        /** @var string */
        protected string $referenceTransactionId,
        /** @var string */
        protected string $storeId,
        /** @var string */
        protected string $storePassword,
        /** @var string */
        protected string $refundRemarks = '',
    ) {
    }

    /**
     * Resolve the API endpoint for refund processing.
     *
     * @return string The endpoint path.
     */
    public function resolveEndpoint(): string
    {
        return '/validator/api/merchantTransIDvalidationAPI.php';
    }

    /**
     * Define the default body for the request.
     *
     * @return RefundRequestBody The formatted request body.
     */
    protected function defaultBody(): array
    {
        return [
            'refund_amount' => number_format($this->refundAmount, 2, '.', ''),
            'refund_remarks' => $this->refundRemarks,
            'bank_tran_id' => $this->bankTransactionId,
            'refe_id' => $this->referenceTransactionId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json',
        ];
    }
}
