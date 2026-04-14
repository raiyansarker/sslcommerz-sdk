<?php

declare(strict_types=1);

namespace RaiyanSarker\SSLCommerz\Responses;

use Saloon\Http\Response;

/**
 * Response for transaction queries by session key.
 * The API returns a flat payload (not element[]) for this operation.
 *
 * @phpstan-type SessionQueryDetail array{
 *     APIConnect: string,
 *     status: string,
 *     sessionkey: string,
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
 *     risk_level: string,
 *     risk_title: string
 * }
 */
class TransactionQueryBySessionResponse extends Response
{
    public function isSuccess(): bool
    {
        $connect = $this->json('APIConnect');
        return is_string($connect) && $connect === 'DONE';
    }

    public function getStatus(): string
    {
        $status = $this->json('status');
        return is_scalar($status) ? (string) $status : '';
    }

    public function getTransactionId(): ?string
    {
        $value = $this->json('tran_id');
        return is_string($value) ? $value : null;
    }

    public function getAmount(): ?float
    {
        $amount = $this->json('amount');
        return is_numeric($amount) ? (float) $amount : null;
    }

    public function getValId(): ?string
    {
        $value = $this->json('val_id');
        return is_string($value) ? $value : null;
    }

    public function getBankTransactionId(): ?string
    {
        $value = $this->json('bank_tran_id');
        return is_string($value) ? $value : null;
    }
}
