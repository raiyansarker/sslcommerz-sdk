<?php

/**
 * SSLCommerz Validation Response
 *
 * This class handles the response from a payment validation request.
 * It provides methods to verify if a payment is valid and access transaction details.
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Responses;

use Saloon\Http\Response;

/**
 * Class ValidationResponse
 *
 * The Saloon response class for payment validation.
 *
 * @package RaiyanSarker\SSLCommerz\Responses
 * @author Raiyan Sarker
 *
 * @phpstan-type ValidationDetail array{
 *     status: string,
 *     tran_date: string,
 *     tran_id: string,
 *     val_id: string,
 *     amount: string,
 *     store_amount: string,
 *     currency: string,
 *     bank_tran_id: string,
 *     card_type: string,
 *     card_no: string,
 *     card_issuer: string,
 *     card_brand: string,
 *     card_issuer_country: string,
 *     card_issuer_country_code: string,
 *     currency_type: string,
 *     currency_amount: string,
 *     currency_rate: string,
 *     base_fair: string,
 *     value_a: string,
 *     value_b: string,
 *     value_c: string,
 *     value_d: string,
 *     risk_level: string,
 *     risk_title: string,
 *     store_id: string,
 *     verify_sign: string,
 *     verify_key: string,
 *     verify_sign_sha2: string
 * }
 */
class ValidationResponse extends Response
{
    /**
     * Get the status of the validation.
     *
     * @return string The status (e.g., 'VALID', 'INVALID', 'FAILED').
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getStatus(): string
    {
        $status = $this->json('status');
        return is_scalar($status) ? (string) $status : '';
    }

    /**
     * Check if the payment is valid.
     *
     * @return bool True if valid, false otherwise.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function isValid(): bool
    {
        return in_array($this->getStatus(), ['VALID', 'VALIDATED'], true);
    }

    /**
     * Get the unique transaction ID (tran_id) assigned by the merchant.
     *
     * @return string|null The transaction ID if available.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getTransactionId(): ?string
    {
        $value = $this->json('tran_id');
        return is_string($value) ? $value : null;
    }

    /**
     * Get the payment amount.
     *
     * @return float|null The amount if available.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getAmount(): ?float
    {
        $amount = $this->json('amount');
        return is_numeric($amount) ? (float) $amount : null;
    }

    /**
     * Get the bank transaction ID provided by SSLCommerz.
     *
     * @return string|null The bank transaction ID if available.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getBankTransactionId(): ?string
    {
        $value = $this->json('bank_tran_id');
        return is_string($value) ? $value : null;
    }

    /**
     * Get the validation ID (val_id) provided by SSLCommerz.
     *
     * @return string|null The validation ID if available.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getValId(): ?string
    {
        $value = $this->json('val_id');
        return is_string($value) ? $value : null;
    }

    public function getCurrency(): ?string
    {
        $value = $this->json('currency');
        return is_string($value) ? $value : null;
    }

    /**
     * Get the risk level of the transaction.
     * 0 = safe, 1 = risky. Always check before fulfilling an order.
     */
    public function getRiskLevel(): ?int
    {
        $value = $this->json('risk_level');
        return is_numeric($value) ? (int) $value : null;
    }

    public function getRiskTitle(): ?string
    {
        $value = $this->json('risk_title');
        return is_string($value) ? $value : null;
    }

    /**
     * Get the full response data as an associative array.
     *
     * @return ValidationDetail The complete response data.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getData(): array
    {
        /** @var ValidationDetail $data */
        $data = $this->json();
        return $data;
    }
}
