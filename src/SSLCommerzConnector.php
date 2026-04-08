<?php

/**
 * SSLCommerz Connector
 *
 * This class serves as the main entry point for interacting with the SSLCommerz API.
 * It handles the configuration and execution of various payment-related requests.
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use RaiyanSarker\SSLCommerz\Data\PaymentData;
use RaiyanSarker\SSLCommerz\Requests\InitializePaymentRequest;
use RaiyanSarker\SSLCommerz\Requests\RefundTransactionRequest;
use RaiyanSarker\SSLCommerz\Requests\TransactionQueryRequest;
use RaiyanSarker\SSLCommerz\Requests\ValidatePaymentRequest;
use RaiyanSarker\SSLCommerz\Responses\PaymentInitializationResponse;
use RaiyanSarker\SSLCommerz\Responses\RefundResponse;
use RaiyanSarker\SSLCommerz\Responses\TransactionQueryResponse;
use RaiyanSarker\SSLCommerz\Responses\ValidationResponse;

/**
 * Class SSLCommerzConnector
 *
 * The connector class for Saloon to handle SSLCommerz API requests.
 *
 * @package RaiyanSarker\SSLCommerz
 * @author Raiyan Sarker
 */
class SSLCommerzConnector extends Connector
{
    use AcceptsJson;

    /**
     * Constructor for the SSLCommerz connector.
     *
     * @param string $storeId SSLCommerz store ID provided by SSLCommerz.
     * @param string $storePassword SSLCommerz store password provided by SSLCommerz.
     * @param bool $isSandbox Whether to use the sandbox (test) or live environment.
     */
    public function __construct(
        /** @var string */
        protected string $storeId,
        /** @var string */
        protected string $storePassword,
        /** @var bool */
        protected bool $isSandbox = true,
    ) {}

    /**
     * Initialize a payment request with SSLCommerz.
     *
     * This method sends a request to initialize a transaction and returns a response
     * containing the session key and payment gateway URL.
     *
     * @param PaymentData $paymentData The data required to initialize the payment.
     * @return PaymentInitializationResponse The response from the initialization request.
     * @throws \Saloon\Exceptions\Request\RequestException If the request fails due to client or server errors.
     * @throws \Saloon\Exceptions\Request\FatalRequestException If a fatal error occurs during the request (e.g., connection timeout).
     * @throws \LogicException If the request could not be sent.
     */
    public function initializePayment(
        PaymentData $paymentData,
    ): PaymentInitializationResponse {
        /** @var PaymentInitializationResponse */
        return $this->send(
            new InitializePaymentRequest(
                $paymentData,
                $this->storeId,
                $this->storePassword,
            ),
        );
    }

    /**
     * Validate a payment using a validation ID.
     *
     * After a successful payment, SSLCommerz provides a val_id which should be used
     * to verify the transaction details from the server side.
     *
     * @param string $validationId The SSLCommerz validation ID (val_id).
     * @return ValidationResponse The response containing validation details.
     * @throws \Saloon\Exceptions\Request\RequestException If the request fails due to client or server errors.
     * @throws \Saloon\Exceptions\Request\FatalRequestException If a fatal error occurs during the request (e.g., connection timeout).
     * @throws \LogicException If the request could not be sent.
     */
    public function validatePayment(string $validationId): ValidationResponse
    {
        /** @var ValidationResponse */
        return $this->send(
            new ValidatePaymentRequest(
                $validationId,
                $this->storeId,
                $this->storePassword,
            ),
        );
    }

    /**
     * Query a transaction's status using its transaction ID.
     *
     * This allows checking the status of a specific transaction using the merchant's tran_id.
     *
     * @param string $transactionId The unique transaction ID (tran_id) assigned by the merchant.
     * @return TransactionQueryResponse The response containing transaction query details.
     * @throws \Saloon\Exceptions\Request\RequestException If the request fails due to client or server errors.
     * @throws \Saloon\Exceptions\Request\FatalRequestException If a fatal error occurs during the request (e.g., connection timeout).
     * @throws \LogicException If the request could not be sent.
     */
    public function queryTransaction(
        string $transactionId,
    ): TransactionQueryResponse {
        /** @var TransactionQueryResponse */
        return $this->send(
            new TransactionQueryRequest(
                $transactionId,
                $this->storeId,
                $this->storePassword,
            ),
        );
    }

    /**
     * Request a refund for a previously successful transaction.
     *
     * @param float $refundAmount The amount to be refunded.
     * @param string $bankTransactionId The bank transaction ID associated with the payment.
     * @param string $referenceTransactionId The reference (merchant) transaction ID.
     * @param string $refundRemarks Optional remarks for the refund request.
     * @return RefundResponse The response from the refund request.
     * @throws \Saloon\Exceptions\Request\RequestException If the request fails due to client or server errors.
     * @throws \Saloon\Exceptions\Request\FatalRequestException If a fatal error occurs during the request (e.g., connection timeout).
     * @throws \LogicException If the request could not be sent.
     */
    public function refundTransaction(
        float $refundAmount,
        string $bankTransactionId,
        string $referenceTransactionId,
        string $refundRemarks = "",
    ): RefundResponse {
        /** @var RefundResponse */
        return $this->send(
            new RefundTransactionRequest(
                $refundAmount,
                $bankTransactionId,
                $referenceTransactionId,
                $this->storeId,
                $this->storePassword,
                $refundRemarks,
            ),
        );
    }

    /**
     * Resolve the base URL for the SSLCommerz API.
     *
     * Switches between sandbox and live URLs based on the isSandbox property.
     *
     * @return string The resolved base URL.
     */
    public function resolveBaseUrl(): string
    {
        return $this->isSandbox
            ? "https://sandbox.sslcommerz.com"
            : "https://securepay.sslcommerz.com";
    }

    /**
     * Define default query parameters for all requests.
     *
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return [];
    }

    /**
     * Define default headers for all requests.
     *
     * SSLCommerz typically requires form-urlencoded content for many operations.
     *
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        return [
            "Content-Type" => "application/x-www-form-urlencoded",
        ];
    }

    /**
     * Get the configured store ID.
     *
     * @return string The store ID.
     */
    public function getStoreId(): string
    {
        return $this->storeId;
    }

    /**
     * Get the configured store password.
     *
     * @return string The store password.
     */
    public function getStorePassword(): string
    {
        return $this->storePassword;
    }
}
