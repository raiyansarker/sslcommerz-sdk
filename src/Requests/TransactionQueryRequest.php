<?php

/**
 * SSLCommerz Transaction Query Request
 *
 * This request is used to query the status and details of a specific transaction
 * using the merchant's transaction ID (tran_id).
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use RaiyanSarker\SSLCommerz\Responses\TransactionQueryResponse;

/**
 * Class TransactionQueryRequest
 *
 * The Saloon request class for querying a transaction.
 *
 * @package RaiyanSarker\SSLCommerz\Requests
 * @author Raiyan Sarker
 *
 * @phpstan-type TransactionQueryParameters array{
 *     tran_id: string,
 *     store_id: string,
 *     store_passwd: string,
 *     format: string
 * }
 */
class TransactionQueryRequest extends Request
{
    /**
     * @var Method The HTTP method for the request.
     */
    protected Method $method = Method::GET;

    /**
     * @var class-string<TransactionQueryResponse>|null The response class for this request.
     */
    protected ?string $response = TransactionQueryResponse::class;

    public function __construct(
        protected string $transactionId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/validator/api/merchantTransIDvalidationAPI.php';
    }

    protected function defaultQuery(): array
    {
        return [
            'tran_id' => $this->transactionId,
            'format' => 'json',
        ];
    }
}
