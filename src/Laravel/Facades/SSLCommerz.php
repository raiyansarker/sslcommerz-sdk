<?php

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

/**
 * @see \RaiyanSarker\SSLCommerz\SSLCommerzConnector
 *
 * @method static \RaiyanSarker\SSLCommerz\Responses\PaymentInitializationResponse initializePayment(\RaiyanSarker\SSLCommerz\Data\PaymentData $paymentData)
 * @method static \RaiyanSarker\SSLCommerz\Responses\ValidationResponse validatePayment(string $validationId)
 * @method static \RaiyanSarker\SSLCommerz\Responses\TransactionQueryResponse queryTransaction(string $transactionId)
 * @method static \RaiyanSarker\SSLCommerz\Responses\TransactionQueryBySessionResponse queryTransactionBySessionKey(string $sessionKey)
 * @method static \RaiyanSarker\SSLCommerz\Responses\RefundResponse refundTransaction(float $refundAmount, string $bankTransactionId, string $refundTransactionId, string $referenceTransactionId, string $refundRemarks = "")
 * @method static \RaiyanSarker\SSLCommerz\Responses\RefundStatusResponse queryRefundStatus(string $refundReferenceId)
 * @method static string getStoreId()
 * @method static string resolveBaseUrl()
 */
class SSLCommerz extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return SSLCommerzConnector::class;
    }
}
