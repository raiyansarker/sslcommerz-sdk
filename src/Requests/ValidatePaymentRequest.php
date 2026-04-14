<?php

/**
 * SSLCommerz Validate Payment Request
 *
 * This request is used to validate a transaction using the SSLCommerz validation ID (val_id).
 * This is typically done after a successful payment callback to verify transaction details.
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Requests;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use RaiyanSarker\SSLCommerz\Responses\ValidationResponse;
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

/**
 * Class ValidatePaymentRequest
 *
 * The Saloon request class for validating a payment.
 *
 * @package RaiyanSarker\SSLCommerz\Requests
 * @author Raiyan Sarker
 *
 * @phpstan-type ValidationQueryParameters array{
 *     val_id: string,
 *     store_id: string,
 *     store_passwd: string,
 *     format: string
 * }
 */
class ValidatePaymentRequest extends Request
{
    /**
     * @var Method The HTTP method for the request.
     */
    protected Method $method = Method::GET;

    /**
     * @var class-string<ValidationResponse>|null The response class for this request.
     */
    protected ?string $response = ValidationResponse::class;

    public function __construct(
        protected string $validationId,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/validator/api/validationserverAPI.php';
    }

    protected function defaultQuery(): array
    {
        return [
            'val_id' => $this->validationId,
            'format' => 'json',
        ];
    }
}
