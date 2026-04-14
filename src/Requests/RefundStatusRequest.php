<?php

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use RaiyanSarker\SSLCommerz\Responses\RefundStatusResponse;

class RefundStatusRequest extends Request
{
    protected Method $method = Method::GET;

    protected ?string $response = RefundStatusResponse::class;

    public function __construct(
        protected string $refundReferenceId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/validator/api/merchantTransIDvalidationAPI.php';
    }

    protected function defaultQuery(): array
    {
        return [
            'refund_ref_id' => $this->refundReferenceId,
            'format' => 'json',
        ];
    }
}
