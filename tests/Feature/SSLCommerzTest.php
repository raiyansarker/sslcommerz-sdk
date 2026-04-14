<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use RaiyanSarker\SSLCommerz\Data\PaymentData;
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

// ── Helpers ──────────────────────────────────────────────────────────────────

function makeConnector(): SSLCommerzConnector
{
    return new SSLCommerzConnector('test_store', 'test_password', true);
}

function makePaymentData(): PaymentData
{
    return new PaymentData(
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
}

// ── Payment Initialization ────────────────────────────────────────────────────

it('can initialize payment', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' => MockResponse::make([
            'status' => 'SUCCESS',
            'sessionkey' => 'F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3',
            'GatewayPageURL' => 'https://sandbox.sslcommerz.com/gwprocess/v4/gw.php?skey=F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3',
        ], 200),
    ]));

    $response = $connector->initializePayment(makePaymentData());

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getSessionKey())->toBe('F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3')
        ->and($response->getGatewayPageURL())->toContain('skey=F9E4A9F2D8B3C4E5F6A7B8C9D0E1F2A3');
});

it('returns failed initialization', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/gwprocess/v4/api.php' => MockResponse::make([
            'status' => 'FAILED',
            'failedreason' => 'Invalid store credentials',
        ], 200),
    ]));

    $response = $connector->initializePayment(makePaymentData());

    expect($response->isSuccess())->toBeFalse()
        ->and($response->getFailedReason())->toBe('Invalid store credentials')
        ->and($response->getSessionKey())->toBeNull()
        ->and($response->getGatewayPageURL())->toBeNull();
});

// ── Payment Validation ────────────────────────────────────────────────────────

it('can validate payment', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php*' => MockResponse::make([
            'status' => 'VALID',
            'tran_id' => 'TRANS_123',
            'amount' => '100.00',
            'bank_tran_id' => 'BANK_123',
            'val_id' => 'VAL_123',
        ], 200),
    ]));

    $response = $connector->validatePayment('VAL_123');

    expect($response->isValid())->toBeTrue()
        ->and($response->getTransactionId())->toBe('TRANS_123')
        ->and($response->getAmount())->toBe(100.00)
        ->and($response->getBankTransactionId())->toBe('BANK_123')
        ->and($response->getValId())->toBe('VAL_123');
});

it('treats VALIDATED status as valid', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php*' => MockResponse::make([
            'status' => 'VALIDATED',
            'tran_id' => 'TRANS_123',
            'amount' => '100.00',
        ], 200),
    ]));

    $response = $connector->validatePayment('VAL_123');

    expect($response->isValid())->toBeTrue();
});

it('returns invalid validation', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php*' => MockResponse::make([
            'status' => 'INVALID_TRANSACTION',
        ], 200),
    ]));

    $response = $connector->validatePayment('BAD_VAL_ID');

    expect($response->isValid())->toBeFalse()
        ->and($response->getTransactionId())->toBeNull()
        ->and($response->getAmount())->toBeNull();
});

// ── Transaction Query ─────────────────────────────────────────────────────────

it('can query transaction', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'DONE',
            'no_of_trans_found' => 1,
            'element' => [
                ['tran_id' => 'TRANS_123', 'status' => 'VALID', 'amount' => '100.00'],
            ],
        ], 200),
    ]));

    $response = $connector->queryTransaction('TRANS_123');
    $first = $response->getFirstTransaction();

    assert($first !== null);

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getTransactionCount())->toBe(1)
        ->and($first['tran_id'])->toBe('TRANS_123');
});

it('can query transaction by session key', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'DONE',
            'status' => 'VALID',
            'sessionkey' => 'SESSION_ABC',
            'tran_id' => 'TRANS_123',
            'val_id' => 'VAL_123',
            'amount' => '100.00',
            'store_amount' => '96.00',
            'bank_tran_id' => 'BANK_123',
        ], 200),
    ]));

    $response = $connector->queryTransactionBySessionKey('SESSION_ABC');

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getStatus())->toBe('VALID')
        ->and($response->getTransactionId())->toBe('TRANS_123')
        ->and($response->getAmount())->toBe(100.00)
        ->and($response->getValId())->toBe('VAL_123')
        ->and($response->getBankTransactionId())->toBe('BANK_123');
});

it('can retrieve multiple transactions from query', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'DONE',
            'no_of_trans_found' => 2,
            'element' => [
                ['tran_id' => 'TRANS_123', 'status' => 'VALID', 'amount' => '100.00'],
                ['tran_id' => 'TRANS_123', 'status' => 'FAILED', 'amount' => '100.00'],
            ],
        ], 200),
    ]));

    $response = $connector->queryTransaction('TRANS_123');
    $transactions = $response->getTransactions();

    expect($response->getTransactionCount())->toBe(2)
        ->and($transactions)->toHaveCount(2)
        ->and($transactions[0]['status'])->toBe('VALID')
        ->and($transactions[1]['status'])->toBe('FAILED');
});

it('returns null for first transaction when none found', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'DONE',
            'no_of_trans_found' => 0,
            'element' => [],
        ], 200),
    ]));

    $response = $connector->queryTransaction('UNKNOWN');

    expect($response->getFirstTransaction())->toBeNull()
        ->and($response->getTransactions())->toBe([])
        ->and($response->getTransactionCount())->toBe(0);
});

it('reports failure when tran-id query APIConnect is not DONE', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'FAILED',
        ], 200),
    ]));

    $response = $connector->queryTransaction('TRANS_123');

    expect($response->isSuccess())->toBeFalse()
        ->and($response->getTransactionCount())->toBe(0)
        ->and($response->getFirstTransaction())->toBeNull();
});

it('reports failure when session-key query APIConnect is not DONE', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'INVALID_REQUEST',
        ], 200),
    ]));

    $response = $connector->queryTransactionBySessionKey('BAD_SESSION');

    expect($response->isSuccess())->toBeFalse()
        ->and($response->getTransactionId())->toBeNull()
        ->and($response->getAmount())->toBeNull();
});

// ── Refund ────────────────────────────────────────────────────────────────────

it('can refund transaction', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'status' => 'success',
            'refund_ref_id' => 'REFUND_123',
            'refund_amount' => '50.00',
        ], 200),
    ]));

    $response = $connector->refundTransaction(50.00, 'BANK_123', 'REFUND_TRAN_123', 'TRANS_123');

    expect($response->isSuccess())->toBeTrue()
        ->and($response->getRefundReferenceId())->toBe('REFUND_123')
        ->and($response->getRefundAmount())->toBe(50.00);
});

it('returns failed refund with error reason', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'status' => 'failed',
            'errorReason' => 'Refund amount exceeds original transaction',
        ], 200),
    ]));

    $response = $connector->refundTransaction(999.00, 'BANK_123', 'REFUND_TRAN_123', 'TRANS_123');

    expect($response->isSuccess())->toBeFalse()
        ->and($response->getFailedReason())->toBe('Refund amount exceeds original transaction')
        ->and($response->getRefundReferenceId())->toBeNull();
});

it('can query refund status', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'DONE',
            'status' => 'refunded',
            'refund_ref_id' => 'REFUND_REF_123',
            'errorReason' => '',
        ], 200),
    ]));

    $response = $connector->queryRefundStatus('REFUND_REF_123');

    expect($response->isRefunded())->toBeTrue()
        ->and($response->isProcessing())->toBeFalse()
        ->and($response->getRefundReferenceId())->toBe('REFUND_REF_123')
        ->and($response->getErrorReason())->toBeNull();
});

it('can query refund status while processing', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'DONE',
            'status' => 'processing',
            'refund_ref_id' => 'REFUND_REF_123',
            'errorReason' => '',
        ], 200),
    ]));

    $response = $connector->queryRefundStatus('REFUND_REF_123');

    expect($response->isRefunded())->toBeFalse()
        ->and($response->isProcessing())->toBeTrue();
});

it('reports failure when refund status query APIConnect is not DONE', function () {
    $connector = makeConnector();
    $connector->withMockClient(new MockClient([
        'https://sandbox.sslcommerz.com/validator/api/merchantTransIDvalidationAPI.php*' => MockResponse::make([
            'APIConnect' => 'INACTIVE',
            'errorReason' => 'Store is inactive',
        ], 200),
    ]));

    $response = $connector->queryRefundStatus('REFUND_REF_123');

    expect($response->isRefunded())->toBeFalse()
        ->and($response->isProcessing())->toBeFalse()
        ->and($response->getErrorReason())->toBe('Store is inactive');
});
