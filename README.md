# SSLCommerz PHP SDK

A modern, typesafe PHP SDK for [SSLCommerz](https://www.sslcommerz.com/) Payment Gateway, built on top of [Saloon PHP](https://docs.saloon.dev/).

## Features

- **Modern PHP**: Requires PHP 8.2 or higher.
- **Typesafe**: Uses DTOs and custom response classes.
- **Saloon PHP**: Leverages the power of Saloon for API requests.
- **Testing**: Built-in support for mocking and testing with [Pest](https://pestphp.com/).
- **PHPStan**: Analyzed at `max` level for maximum reliability.

## Installation

```bash
composer require raiyansarker/sslcommerz-sdk
```

## Usage

### Initialize Connector

```php
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;

$connector = new SSLCommerzConnector(
    storeId: 'your_store_id',
    storePassword: 'your_store_password',
    isSandbox: true // Set to false for live
);
```

### Initialize Payment

```php
use RaiyanSarker\SSLCommerz\Data\PaymentData;

$paymentData = new PaymentData(
    totalAmount: 100.00,
    currency: 'BDT',
    transactionId: 'TRANS_123456',
    successUrl: 'https://your-domain.com/success',
    failUrl: 'https://your-domain.com/fail',
    cancelUrl: 'https://your-domain.com/cancel',
    customerName: 'John Doe',
    customerEmail: 'john@example.com',
    customerAddress1: 'Dhaka',
    customerCity: 'Dhaka',
    customerCountry: 'Bangladesh',
    customerPhone: '01700000000',
    customerPostcode: '1234',
    productName: 'Test Product',
    productCategory: 'Electronics'
);

$response = $connector->initializePayment($paymentData);

if ($response->isSuccess()) {
    $gatewayUrl = $response->getGatewayPageURL();
    // Redirect user to $gatewayUrl
} else {
    echo "Error: " . $response->getFailedReason();
}
```

### Validate Payment

```php
$valId = $_POST['val_id']; // From SSLCommerz callback
$response = $connector->validatePayment($valId);

if ($response->isValid()) {
    $transactionId = $response->getTransactionId();
    $amount = $response->getAmount();
    // Update your database
}
```

### Query Transaction

```php
$response = $connector->queryTransaction('TRANS_123456');

if ($response->isSuccess()) {
    $transaction = $response->getFirstTransaction();
    // Handle transaction details
}
```

### Refund Transaction

```php
$response = $connector->refundTransaction(
    refundAmount: 50.00,
    bankTransactionId: 'BANK_TRAN_ID',
    referenceTransactionId: 'TRANS_123456',
    refundRemarks: 'Customer requested refund'
);

if ($response->isSuccess()) {
    echo "Refund initiated: " . $response->getRefundReferenceId();
}
```

## Laravel Integration

The SSLCommerz PHP SDK provides seamless integration with Laravel through a built-in Service Provider and Facade.

### 1. Installation & Auto-Discovery

The SDK uses Laravel's package auto-discovery. Once you install the package via Composer, the Service Provider and `SSLCommerz` Facade will be registered automatically.

### 2. Configuration

Publish the default configuration file to `config/sslcommerz.php`:

```bash
php artisan vendor:publish --tag="sslcommerz-config"
```

#### Environment Variables

Configure your SSLCommerz credentials in your `.env` file:

```env
SSLCOMMERZ_STORE_ID=your_store_id
SSLCOMMERZ_STORE_PASSWORD=your_store_password
SSLCOMMERZ_SANDBOX=true
```

#### Configuration Options

The published `config/sslcommerz.php` file allows you to manage:

| Option | Environment Variable | Default | Description |
|--------|----------------------|---------|-------------|
| `store_id` | `SSLCOMMERZ_STORE_ID` | `null` | Your SSLCommerz Store ID. |
| `store_password` | `SSLCOMMERZ_STORE_PASSWORD` | `null` | Your SSLCommerz Store Password. |
| `sandbox` | `SSLCOMMERZ_SANDBOX` | `true` | Set to `false` for production (live) mode. |

### 3. Usage

You can interact with the SDK using either the `SSLCommerz` Facade or by injecting the `SSLCommerzConnector`.

#### Using the Facade

The Facade provides a static interface to all connector methods:

```php
use RaiyanSarker\SSLCommerz\Laravel\Facades\SSLCommerz;
use RaiyanSarker\SSLCommerz\Data\PaymentData;

public function initiatePayment()
{
    $paymentData = new PaymentData(
        totalAmount: 100.00,
        currency: 'BDT',
        transactionId: 'TXN_' . uniqid(),
        // ... other required fields
    );

    $response = SSLCommerz::initializePayment($paymentData);

    if ($response->isSuccess()) {
        return redirect()->away($response->getGatewayPageURL());
    }

    return back()->with('error', $response->getFailedReason());
}
```

#### Dependency Injection

The `SSLCommerzConnector` is bound as a singleton in the Laravel service container:

```php
use RaiyanSarker\SSLCommerz\SSLCommerzConnector;
use RaiyanSarker\SSLCommerz\Data\PaymentData;

public function handleCallback(Request $request, SSLCommerzConnector $connector)
{
    $response = $connector->validatePayment($request->input('val_id'));

    if ($response->isValid()) {
        // Handle successful payment
    }
}
```

### 4. Handling Callbacks & CSRF

SSLCommerz requires three mandatory callback URLs (Success, Fail, and Cancel) and an optional IPN (Instant Payment Notification) URL.

#### Define Routes

SSLCommerz sends a **POST** request to these URLs. Define them in your `routes/web.php`:

```php
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::post('/payment/success', [PaymentController::class, 'success']);
Route::post('/payment/fail', [PaymentController::class, 'fail']);
Route::post('/payment/cancel', [PaymentController::class, 'cancel']);
Route::post('/payment/ipn', [PaymentController::class, 'ipn']);
```

#### Exclude from CSRF Protection

Since SSLCommerz sends external POST requests, you must exclude these routes from Laravel's CSRF protection.

**For Laravel 11+ (in `bootstrap/app.php`):**

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        '/payment/success',
        '/payment/fail',
        '/payment/cancel',
        '/payment/ipn',
    ]);
})
```

**For older Laravel versions (in `app/Http/Middleware/VerifyCsrfToken.php`):**

```php
protected $except = [
    '/payment/success',
    '/payment/fail',
    '/payment/cancel',
    '/payment/ipn',
];
```

#### Handling the Callback in a Controller

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RaiyanSarker\SSLCommerz\Laravel\Facades\SSLCommerz;

class PaymentController extends Controller
{
    public function success(Request $request)
    {
        $validationId = $request->input('val_id');
        $response = SSLCommerz::validatePayment($validationId);

        if ($response->isValid()) {
            $transactionId = $response->getTransactionId();
            // Update your database, mark order as paid
            return redirect()->route('order.complete', ['id' => $transactionId]);
        }

        return redirect()->route('checkout')->with('error', 'Payment validation failed.');
    }

    public function fail(Request $request)
    {
        return redirect()->route('checkout')->with('error', 'Payment failed.');
    }

    public function cancel(Request $request)
    {
        return redirect()->route('checkout')->with('info', 'Payment cancelled.');
    }

    public function ipn(Request $request)
    {
        // IPN is sent in the background by SSLCommerz
        $validationId = $request->input('val_id');
        $status = $request->input('status');

        if ($status === 'VALID' || $status === 'VALIDATED') {
            $response = SSLCommerz::validatePayment($validationId);

            if ($response->isValid()) {
                // Update your database using $response->getTransactionId()
                // It is recommended to check if the order is already marked as paid
            }
        }

        return response()->json(['status' => 'OK']);
    }
}
```

### 5. Testing & Mocking in Laravel

When writing tests for your Laravel application, you can use the `SSLCommerz` Facade's built-in mocking capabilities or Saloon's mocking features.

#### Mocking with Facade

```php
use RaiyanSarker\SSLCommerz\Laravel\Facades\SSLCommerz;
use RaiyanSarker\SSLCommerz\Responses\PaymentInitializationResponse;

test('it redirects to gateway on successful initialization', function () {
    SSLCommerz::shouldReceive('initializePayment')
        ->once()
        ->andReturn(/* mock response object */);

    $response = $this->post('/checkout');

    $response->assertRedirect();
});
```

#### Mocking with Saloon

Since the SDK is built on Saloon, you can use `Saloon::fake()` for more granular control:

```php
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;
use RaiyanSarker\SSLCommerz\Requests\InitializePaymentRequest;

Saloon::fake([
    InitializePaymentRequest::class => MockResponse::make(['status' => 'SUCCESS', 'GatewayPageURL' => 'https://sandbox.sslcommerz.com/gw'], 200),
]);
```

## Testing

The project uses [Pest](https://pestphp.com/) for testing. Tests are divided into:

- `tests/Unit`: For isolated logic (e.g., DTO transformations).
- `tests/Feature`: For integration tests and API request/response validation.

Run tests with Pest:

```bash
composer test
```

Run static analysis with PHPStan:

```bash
composer stan
```

Run both:

```bash
composer all
```

## Resources

- **Postman Collection**: A comprehensive Postman collection is available in `resources/postman/SSLCommerz.postman_collection.json` to help you explore and test the API endpoints.

## Credits

- Special thanks to [sumoncse19](https://www.postman.com/sumoncse19) for providing the original Postman collection for the SSLCommerz API.

## License

MIT
