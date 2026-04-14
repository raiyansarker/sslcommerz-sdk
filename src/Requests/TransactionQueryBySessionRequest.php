<?php

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use RaiyanSarker\SSLCommerz\Responses\TransactionQueryBySessionResponse;

class TransactionQueryBySessionRequest extends Request
{
    protected Method $method = Method::GET;

    protected ?string $response = TransactionQueryBySessionResponse::class;

    public function __construct(
        protected string $sessionKey,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/validator/api/merchantTransIDvalidationAPI.php';
    }

    protected function defaultQuery(): array
    {
        return [
            'sessionkey' => $this->sessionKey,
            'format' => 'json',
        ];
    }
}
