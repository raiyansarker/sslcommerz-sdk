<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use RaiyanSarker\SSLCommerz\Data\PaymentData;
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

it('can initialize payment', function () {
    $mockClient = new MockClient([
        'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' => MockResponse::make([
            'status' => 'SUCCESS',
            'sessionkey' => 'F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3',
            'GatewayPageURL' => 'https://sandbox.sslcommerz.com/gwprocess/v4/gw.php?skey=F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3',
        ], 200),
    ]);

    $connector = new SSLCommerzConnector('test_store', 'test_password', true);
    $connector->withMockClient($mockClient);

    $paymentData = new PaymentData(
        totalAmount: 100.00,
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

    $response = $connector->initializePayment($paymentData);

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getSessionKey())->toBe('F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3')
        ->and($response->getGatewayPageURL())->toContain('skey=F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3');
});

it('can validate payment', function () {
    $mockClient = new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php*' => MockResponse::make([
            'status' => 'VALID',
            'tran_id' => 'TRANS_123',
            'amount' => '100.00',
            'bank_tran_id' => 'BANK_123',
            'val_id' => 'VAL_123',
        ], 200),
    ]);

    $connector = new SSLCommerzConnector('test_store', 'test_password', true);
    $connector->withMockClient($mockClient);

    $response = $connector->validatePayment('VAL_123');

    expect($response->isValid())->toBeTrue()
        ->and($response->getTransactionId())->toBe('TRANS_123')
        ->and($response->getAmount())->toBe(100.00);
});

it('can query transaction', function () {
    $mockClient = new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'status' => 'SUCCESS',
            'no_of_trans_found' => 1,
            'element' => [
                [
                    'tran_id' => 'TRANS_123',
                    'status' => 'VALID',
                    'amount' => '100.00',
                ],
            ],
        ], 200),
    ]);

    $connector = new SSLCommerzConnector('test_store', 'test_password', true);
    $connector->withMockClient($mockClient);

    $response = $connector->queryTransaction('TRANS_123');

    $firstTransaction = $response->getFirstTransaction();

    assert($firstTransaction !== null);

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getTransactionCount())->toBe(1)
        ->and($firstTransaction['tran_id'])->toBe('TRANS_123');
});

it('can refund transaction', function () {
    $mockClient = new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php' => MockResponse::make([
            'status' => 'SUCCESS',
            'refund_ref_id' => 'REFUND_123',
            'refund_amount' => '50.00',
        ], 200),
    ]);

    $connector = new SSLCommerzConnector('test_store', 'test_password', true);
    $connector->withMockClient($mockClient);

    $response = $connector->refundTransaction(50.00, 'BANK_123', 'TRANS_123');

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getRefundReferenceId())->toBe('REFUND_123')
        ->and($response->getRefundAmount())->toBe(50.00);
});
