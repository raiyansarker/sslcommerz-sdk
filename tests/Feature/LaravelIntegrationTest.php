<?php

use RaiyanSarker\SSLCommerz\Laravel\Facades\SSLCommerz;
use RaiyanSarker\SSLCommerz\Laravel\SSLCommerzServiceProvider;
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

uses(\Orchestra\Testbench\TestCase::class);

beforeEach(function () {
    app()->register(SSLCommerzServiceProvider::class);

    config()->set('sslcommerz.store_id', 'test_store_id');
    config()->set('sslcommerz.store_password', 'test_store_password');
    config()->set('sslcommerz.sandbox', true);
});

test('it can resolve the connector from the container', function () {
    /** @var SSLCommerzConnector $connector */
    $connector = app()->make(SSLCommerzConnector::class);

    expect($connector)->toBeInstanceOf(SSLCommerzConnector::class);
    expect($connector->getStoreId())->toBe('test_store_id');
    expect($connector->resolveBaseUrl())->toBe('https://sandbox.sslcommerz.com');
});

test('it can resolve the connector with live environment', function () {
    config()->set('sslcommerz.sandbox', false);

    /** @var SSLCommerzConnector $connector */
    $connector = app()->make(SSLCommerzConnector::class);

    expect($connector->resolveBaseUrl())->toBe('https://securepay.sslcommerz.com');
});

test('the facade resolves to the connector', function () {
    expect(SSLCommerz::getFacadeRoot())->toBeInstanceOf(SSLCommerzConnector::class);
});

test('it can use the facade to access connector methods', function () {
    expect(SSLCommerz::getStoreId())->toBe('test_store_id');
});
