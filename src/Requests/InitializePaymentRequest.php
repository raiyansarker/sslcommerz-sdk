<?php

/**
 * SSLCommerz Initialize Payment Request
 *
 * This request is used to initialize a transaction with SSLCommerz.
 * It sends the payment details and receives a session key and gateway URL.
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Requests;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;
use RaiyanSarker\SSLCommerz\Data\PaymentData;
use RaiyanSarker\SSLCommerz\Responses\PaymentInitializationResponse;

/**
 * Class InitializePaymentRequest
 *
 * The Saloon request class for initializing a payment.
 *
 * @package RaiyanSarker\SSLCommerz\Requests
 * @author Raiyan Sarker
 *
 * @phpstan-type InitializationRequestBody array{
 *     store_id: string,
 *     store_passwd: string,
 *     total_amount: string,
 *     currency: string,
 *     tran_id: string,
 *     success_url: string,
 *     fail_url: string,
 *     cancel_url: string,
 *     ipn_url?: string,
 *     cus_name: string,
 *     cus_email: string,
 *     cus_add1: string,
 *     cus_city: string,
 *     cus_country: string,
 *     cus_phone: string,
 *     cus_postcode: string,
 *     product_name: string,
 *     product_category: string,
 *     product_profile: string,
 *     shipping_method: string,
 *     multi_card_name?: string,
 *     value_a?: string,
 *     value_b?: string,
 *     value_c?: string,
 *     value_d?: string
 * }
 */
class InitializePaymentRequest extends Request implements HasBody
{
    use HasFormBody;

    /**
     * @var Method The HTTP method for the request.
     */
    protected Method $method = Method::POST;

    /**
     * @var class-string<PaymentInitializationResponse>|null The response class for this request.
     */
    protected ?string $response = PaymentInitializationResponse::class;

    /**
     * Constructor for InitializePaymentRequest.
     *
     * @param PaymentData $paymentData The payment details to be sent to SSLCommerz.
     * @param string $storeId The SSLCommerz store ID.
     * @param string $storePassword The SSLCommerz store password.
     */
    public function __construct(
        protected PaymentData $paymentData,
        protected string $storeId,
        protected string $storePassword,
    ) {
    }

    /**
     * Resolve the API endpoint for payment initialization.
     *
     * @return string The endpoint path.
     */
    public function resolveEndpoint(): string
    {
        return '/gwprocess/v4/api.php';
    }

    /**
     * Define the default body for the request.
     *
     * This uses the PaymentData object to generate the required form parameters.
     *
     * @return InitializationRequestBody The formatted request body.
     */
    protected function defaultBody(): array
    {
        /** @var InitializationRequestBody */
        return $this->paymentData->toArray(
            $this->storeId,
            $this->storePassword
        );
    }
}
