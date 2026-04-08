<?php

use RaiyanSarker\SSLCommerz\Data\PaymentData;

it('can transform payment data to array', function () {
    $paymentData = new PaymentData(
        totalAmount: 100.50,
        currency: 'BDT',
        transactionId: 'TRANS_123',
        successUrl: 'https://example.com/success',
        failUrl: 'https://example.com/fail',
        cancelUrl: 'https://example.com/cancel',
        customerName: 'John Doe',
        customerEmail: 'john@example.com',
        customerAddress1: 'Dhaka',
        customerCity: 'Dhaka',
        customerCountry: 'Bangladesh',
        customerPhone: '01700000000',
        customerPostcode: '1000',
        productName: 'Test Product',
        productCategory: 'General'
    );

    $array = $paymentData->toArray('store_123', 'pass_123');

    expect($array)->toBeArray()
        ->and($array['store_id'])->toBe('store_123')
        ->and($array['store_passwd'])->toBe('pass_123')
        ->and($array['total_amount'])->toBe('100.50')
        ->and($array['currency'])->toBe('BDT')
        ->and($array['tran_id'])->toBe('TRANS_123');
});
