<?php

/**
 * SSLCommerz Transaction Query Response
 *
 * This class handles the response from a transaction query request.
 * It provides methods to access the status and details of multiple transactions.
 *
 * @author Raiyan Sarker
 */

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Responses;

use Saloon\Http\Response;

/**
 * Class TransactionQueryResponse
 *
 * The Saloon response class for transaction query requests.
 *
 * @package RaiyanSarker\SSLCommerz\Responses
 * @author Raiyan Sarker
 *
 * @phpstan-type TransactionDetail array{
 *     tran_id: string,
 *     val_id: string,
 *     amount: string,
 *     card_type: string,
 *     store_amount: string,
 *     card_no: string,
 *     bank_tran_id: string,
 *     status: string,
 *     tran_date: string,
 *     currency: string,
 *     card_issuer: string,
 *     card_brand: string,
 *     card_issuer_country: string,
 *     card_issuer_country_code: string,
 *     store_id: string,
 *     verify_sign: string,
 *     verify_key: string,
 *     verify_sign_sha2: string,
 *     currency_type: string,
 *     currency_amount: string,
 *     currency_rate: string,
 *     base_fair: string,
 *     value_a: string,
 *     value_b: string,
 *     value_c: string,
 *     value_d: string,
 *     risk_level: string,
 *     risk_title: string
 * }
 */
class TransactionQueryResponse extends Response
{
    /**
     * Get the status of the transaction query.
     *
     * @return string The status (e.g., 'SUCCESS', 'FAILED').
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getStatus(): string
    {
        $status = $this->json('status');
        return is_scalar($status) ? (string) $status : '';
    }

    /**
     * Check if the transaction query was successful.
     *
     * @return bool True if successful, false otherwise.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function isSuccess(): bool
    {
        return $this->getStatus() === 'SUCCESS';
    }

    /**
     * Get the total number of transactions found.
     *
     * @return int The transaction count.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getTransactionCount(): int
    {
        $count = $this->json('no_of_trans_found', 0);
        return is_numeric($count) ? (int) $count : 0;
    }

    /**
     * Get all transactions returned in the response.
     *
     * @return list<TransactionDetail> A list of transaction details.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getTransactions(): array
    {
        /** @var list<TransactionDetail> $elements */
        $elements = $this->json('element', []);
        return $elements;
    }

    /**
     * Get the first transaction in the response if any exist.
     *
     * @return TransactionDetail|null The first transaction details or null.
     * @throws \JsonException If the response body could not be decoded as JSON.
     */
    public function getFirstTransaction(): ?array
    {
        $transactions = $this->getTransactions();
        return $transactions[0] ?? null;
    }
}
