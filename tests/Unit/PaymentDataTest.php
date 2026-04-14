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

it('excludes null optional fields from array', function () {
    $paymentData = new PaymentData(
        totalAmount: 10.00,
        currency: 'BDT',
        transactionId: 'TRANS_1',
        successUrl: 'https://example.com/success',
        failUrl: 'https://example.com/fail',
        cancelUrl: 'https://example.com/cancel',
        customerName: 'Jane',
        customerEmail: 'jane@example.com',
        customerAddress1: 'Dhaka',
        customerCity: 'Dhaka',
        customerCountry: 'Bangladesh',
        customerPhone: '01700000000',
        customerPostcode: '1000',
        productName: 'Item',
        productCategory: 'General',
    );

    $array = $paymentData->toArray('store', 'pass');

    expect($array)->not->toHaveKey('ipn_url')
        ->and($array)->not->toHaveKey('multi_card_name')
        ->and($array)->not->toHaveKey('value_a')
        ->and($array)->not->toHaveKey('value_b')
        ->and($array)->not->toHaveKey('value_c')
        ->and($array)->not->toHaveKey('value_d');
});

it('includes optional fields when provided', function () {
    $paymentData = new PaymentData(
        totalAmount: 10.00,
        currency: 'BDT',
        transactionId: 'TRANS_1',
        successUrl: 'https://example.com/success',
        failUrl: 'https://example.com/fail',
        cancelUrl: 'https://example.com/cancel',
        customerName: 'Jane',
        customerEmail: 'jane@example.com',
        customerAddress1: 'Dhaka',
        customerCity: 'Dhaka',
        customerCountry: 'Bangladesh',
        customerPhone: '01700000000',
        customerPostcode: '1000',
        productName: 'Item',
        productCategory: 'General',
        ipnUrl: 'https://example.com/ipn',
        valueA: 'ref_a',
    );

    $array = $paymentData->toArray('store', 'pass');

    expect($array['ipn_url'])->toBe('https://example.com/ipn')
        ->and($array['value_a'])->toBe('ref_a')
        ->and($array)->not->toHaveKey('value_b');
});
