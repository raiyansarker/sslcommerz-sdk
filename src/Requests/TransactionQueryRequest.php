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
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

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

    /**
     * Constructor for TransactionQueryRequest.
     *
     * @param string $transactionId The unique transaction ID (tran_id) assigned by the merchant.
     * @param string $storeId The SSLCommerz store ID.
     * @param string $storePassword The SSLCommerz store password.
     */
    public function __construct(
        /** @var string */
        protected string $transactionId,
        /** @var string */
        protected string $storeId,
        /** @var string */
        protected string $storePassword,
    ) {
    }

    /**
     * Resolve the API endpoint for transaction querying.
     *
     * @return string The endpoint path.
     */
    public function resolveEndpoint(): string
    {
        return '/validator/api/merchantTransIDvalidationAPI.php';
    }

    /**
     * Define the default query parameters for the request.
     *
     * @return TransactionQueryParameters The formatted query parameters.
     */
    protected function defaultQuery(): array
    {
        return [
            'tran_id' => $this->transactionId,
            'store_id' => $this->storeId,
            'store_passwd' => $this->storePassword,
            'format' => 'json',
        ];
    }
}
