<?php

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use RaiyanSarker\SSLCommerz\Responses\RefundResponse;

/**
 * @phpstan-type RefundQueryParameters array{
 *     bank_tran_id: string,
 *     refund_trans_id: string,
 *     refund_amount: string,
 *     refund_remarks: string,
 *     refe_id: string,
 *     store_id: string,
 *     store_passwd: string,
 *     format: string
 * }
 */
class RefundTransactionRequest extends Request
{
    protected Method $method = Method::GET;

    protected ?string $response = RefundResponse::class;

    public function __construct(
        protected float $refundAmount,
        protected string $bankTransactionId,
        protected string $refundTransactionId,
        protected string $referenceTransactionId,
        protected string $storeId,
        protected string $storePassword,
        protected string $refundRemarks = '',
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/validator/api/merchantTransIDvalidationAPI.php';
    }

    /**
     * @return RefundQueryParameters
     */
    protected function defaultQuery(): array
    {
        return [
            'bank_tran_id' => $this->bankTransactionId,
            'refund_trans_id' => $this->refundTransactionId,
            'refund_amount' => number_format($this->refundAmount, 2, '.', ''),
            'refund_remarks' => $this->refundRemarks,
            'refe_id' => $this->referenceTransactionId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json',
        ];
    }
}
