<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    return redirect('/');
});

Route::get('/', 'HomeController@index')->name('home');
Route::get('/privacy-policy', 'HomeController@privacyPolicy')->name('privacy_policy');

// changing-language
Route::get('change-lang', 'HomeController@setLocalization');

//coinPayment IPN
Route::post('coinpayment/check', 'Users\DepositController@coinpaymentCheckStatus');

// user email check on registration
Route::post('user-registration-check-email', 'Auth\RegisterController@checkUserRegistrationEmail');

Route::get('/invoice/{id}', 'StoreController@invoice');
Route::get('/collect-payment-invoice/{id}', 'StoreController@collectPaymentInvoice');

// Unauthenticated User
Route::group(['middleware' => ['no_auth:users', 'locale']], function () {
    Route::get('/login', 'Auth\LoginController@index')->name("login");
    Route::post('/authenticate', 'Auth\LoginController@authenticate');
    Route::get('register', 'Auth\RegisterController@create');
    Route::post('register/duplicate-phone-number-check', 'Auth\RegisterController@registerDuplicatePhoneNumberCheck');
    Route::post('register/duplicate-user-check', 'Auth\RegisterController@checkDuplicateUser');
    Route::post('register/store-personal-info', 'Auth\RegisterController@storePersonalInfo')->name('register.personal.info');
    Route::post('register/store', 'Auth\RegisterController@store')->name('register.store');
    Route::get('/user/verify/{token}', 'Auth\RegisterController@verifyUser');
    Route::view('forget-password', 'frontend.auth.forgetPassword')->name('user.forget_password');
    Route::post('forget-password', 'Auth\ForgotPasswordController@forgetPassword');
    Route::get('password/resets/{token}', 'Auth\ForgotPasswordController@verifyToken');
    Route::post('confirm-password', 'Auth\ForgotPasswordController@confirmNewPassword');
    
    Route::get('mpos-register', 'Auth\RegisterController@mpos_create');
    Route::post('mpos/register/store', 'Auth\RegisterController@mpos_store');
    Route::get('/mpos-register-success', 'Auth\RegisterController@mpos_success');
});

//2fa
Route::group(['middleware' => ['guest:users', 'locale', 'check-user-inactive'], 'namespace' => 'Users'], function () {
    Route::get('2fa', 'CustomerController@view2fa');
    Route::post('2fa/verify', 'CustomerController@verify2fa')->name('2fa-verify.store');
    Route::get('google2fa', 'CustomerController@viewGoogle2fa')->name('google2fa');
    Route::post('google2fa/verify', 'CustomerController@verifyGoogle2fa')->name('2fa-verify.google_authenticator');
    Route::post('google2fa/verifyGoogle2faOtp', 'CustomerController@verifyGoogle2faOtp')->name('2fa-verify.google_otp');
});

// Authenticated User
Route::group(['middleware' => ['guest:users', 'locale', 'twoFa', 'check-user-inactive'], 'namespace' => 'Users'], function ()
{

    Route::get('mobilereload', 'MobileReloadController@index');
    Route::post('getOperatornew', 'MobileReloadController@getOperator');
    Route::post('getvaluenew', 'MobileReloadController@getvalue');
    Route::post('makerechargenew', 'MobileReloadController@makerecharge');

    Route::get('reports', 'MobileReloadController@reports');




    Route::get('dashboard', 'CustomerController@dashboard');
    Route::get('comissions', 'CustomerController@comissions');


/*Cable TV */
    Route::get('utility/cable', 'CableTvController@index');
    Route::get('utility/pay-cable-bill', 'CableTvController@add');
    Route::post('utility/pay-cable-bill', 'CableTvController@add');
    Route::get('utility/edit-cable-bill/{id}', 'CableTvController@edit');
    Route::post('utility/edit-cable-bill', 'CableTvController@edit');

    /*Gas Bill */
    Route::get('utility/gas', 'GasBillController@index');
    Route::get('utility/pay-gas-bill', 'GasBillController@add');
    Route::post('utility/pay-gas-bill', 'GasBillController@add');
    Route::get('utility/edit-gas-bill/{id}', 'GasBillController@edit');
    Route::post('utility/edit-gas-bill', 'GasBillController@edit');

    /*Electricity Bill */
    Route::get('utility/electricity', 'ElectricityBillController@index');
    Route::get('utility/pay-electricity-bill', 'ElectricityBillController@add');
    Route::post('utility/pay-electricity-bill', 'ElectricityBillController@add');
    Route::get('utility/edit-electricity-bill/{id}', 'ElectricityBillController@edit');
    Route::post('utility/edit-electricity-bill', 'ElectricityBillController@edit');

    /*Water Bill */
    Route::get('utility/water', 'WaterBillController@index');
    Route::get('utility/pay-water-bill', 'WaterBillController@add');
    Route::post('utility/pay-water-bill', 'WaterBillController@add');
    Route::get('utility/edit-water-bill/{id}', 'WaterBillController@edit');
    Route::post('utility/edit-water-bill', 'WaterBillController@edit');

    /*Internet Bill */
    Route::get('utility/internet', 'InternetBillController@index');
    Route::get('utility/pay-internet-bill', 'InternetBillController@add');
    Route::post('utility/pay-internet-bill', 'InternetBillController@add');
    Route::get('utility/edit-internet-bill/{id}', 'InternetBillController@edit');
    Route::post('utility/edit-internet-bill', 'InternetBillController@edit');

    /*Insurance*/
    Route::get('utility/insurance', 'InsuranceController@index');
    Route::get('utility/pay-insurance-bill', 'InsuranceController@add');
    Route::post('utility/pay-insurance-bill', 'InsuranceController@add');
    Route::get('utility/edit-insurance-bill/{id}', 'InsuranceController@edit');
    Route::post('utility/edit-insurance-bill', 'InsuranceController@edit');

    /*Gift Card*/
    Route::get('card/gift-card', 'GiftCardController@index');
    Route::get('card/add-gift-card', 'GiftCardController@add');
    Route::post('card/add-gift-card', 'GiftCardController@add');
    Route::get('card/edit-gift-card/{id}', 'GiftCardController@edit');
    Route::post('card/edit-gift-card', 'GiftCardController@edit');

    /*Prepaid Card*/
    Route::get('card/prepaid-card', 'PrepaidCardController@index');
    Route::get('card/add-prepaid-card', 'PrepaidCardController@add');
    Route::post('card/add-prepaid-card', 'PrepaidCardController@add');
    Route::get('card/edit-prepaid-cardl/{id}', 'PrepaidCardController@edit');
    Route::post('card/edit-prepaid-card', 'PrepaidCardController@edit');

    /*Admin Dhiraagu Transactions*/
     //Route::get('/dhiraagu-cashin', 'UtilityController@cashin');







    // Route for redirecting to crypto preference disabled page
    Route::get('user/crypto/preference-disabled', 'CustomerController@userCryptoPreferenceDisabled');

    Route::get('/logout', 'CustomerController@logout')->name('logout');
    Route::get('check-user-status', 'CustomerController@checkUserStatus');
    Route::get('check-request-creator-suspended-status', 'CustomerController@checkRequestCreatorSuspendedStatus');
    Route::get('check-request-creator-inactive-status', 'CustomerController@checkRequestCreatorInactiveStatus');
    Route::get('check-processed-by', 'CustomerController@checkProcessedBy');
    
     //VIRTUAL CARD
    Route::get('virtualcard', 'VirtualCardController@virtualcard')->name('user.virtualcard');
    Route::get('virtualtransactions/{id}', 'VirtualCardController@virtualtransactions')->name('user.virtualtransactions');
    Route::get('virtual_card/{id}', 'VirtualCardController@getCardsList')->name('user.virtual_card');
    Route::post('create_new', 'VirtualCardController@createCard')->name('user.create_new');
    Route::post('update_virtual_card', 'VirtualCardController@updateVirtualCard')->name('user.update_virtual_card');
    Route::post('pause_virtual_card','VirtualCardController@pausedVirtualCard')->name('user.pause_virtual_card');
    Route::post('close_virtual_card','VirtualCardController@closeVirtualCard')->name('user.close_virtual_card');
    Route::post('open_virtual_card','VirtualCardController@openVirtualCard')->name('user.open_virtual_card');

    //Settings
    Route::group(['middleware' => ['permission:manage_setting']], function ()
    {
        Route::get('profile', 'CustomerController@profile');
        Route::get('profile/2fa', 'CustomerController@profileTwoFa');
        Route::post('profile/2fa/update', 'CustomerController@UpdateProfileTwoFa');
        Route::post('profile/2fa/ajaxTwoFa', 'CustomerController@ajaxTwoFa');

        //add or update user's qr-code
        Route::post('profile/qr-code/add-or-update', 'CustomerController@addOrUpdateUserProfileQrCode');
        Route::get('profile/qr-code-print/{id}/{printQrCode}', 'CustomerController@printUserQrCode');
        
        
        
        
        /***********************transfered from smddeveloper************** ***************/	
        Route::get('mystore', 'MerchantController@mystore')->name('mystore');	    
		Route::post('merchant/submitdata', 'MerchantController@submitdata')->name('submitstore');
		
		Route::get('merchant/states', 'MerchantController@states')->name('states');	
		Route::get('merchant/citys', 'MerchantController@citys')->name('citys');
		/************************************* ***************/	
		
    // 26-10-2020
        Route::get('shipping_cost', 'MerchantController@shipping_cost')->name('shopping_cost');
        Route::get('merchant/shippingcostadd', 'MerchantController@shippingcostadd');
        Route::post('merchant/shippingcoststore', 'MerchantController@shippingcoststore');
        Route::get('merchant/shippingcostedit/{id}', 'MerchantController@shippingcostedit');
        Route::post('merchant/shippingcostupdate', 'MerchantController@shippingcostupdate');
    // 26-10-2020
     // new code 27-10-2020
     Route::get('/orders', 'OrderController@index');
     Route::get('orders/detail/{id}', 'OrderController@detail');
     Route::post('orders/changestatus', 'OrderController@changestatus');
     
    // new code 27-10-2020
	/***********************transfere from smddeveloper************** ***************/	
        
        
        //KYC
        Route::get('kyc', 'CustomerController@kyc');
        Route::get('user/kyc', 'CustomerController@user_per_kyc')->name("user.kyc");
        Route::get('profile/personal-id', 'CustomerController@personalId');
        Route::post('profile/personal-id-update', 'CustomerController@updatePersonalId');
        Route::get('profile/personal-address', 'CustomerController@personalAddress');
        Route::post('profile/personal-address-update', 'CustomerController@updatePersonalAddress');
        Route::get('profile/personal-photo', 'CustomerController@personalPhoto');
        Route::post('profile/personal-photo-update', 'CustomerController@updatePersonalPhoto');


        Route::get('profile/upgrade', 'CustomerController@profileUpgrade');
        Route::post('profile/upgrade-update', 'CustomerController@updateProfileUpgrade');
        
        Route::get('profile/business-verification', 'CustomerController@businessVerification');
        //

        //google2fa
        Route::post('profile/2fa/google2fa', 'CustomerController@google2fa');
        Route::post('profile/2fa/google2fa/complete-google2fa-verification', 'CustomerController@completeGoogle2faVerification');
        Route::post('profile/2fa/google2fa/otp-verify', 'CustomerController@google2faOtpVerification')->middleware('google2fa');
        //

        //2fa
        Route::post('profile/2fa/disabledTwoFa', 'CustomerController@disabledTwoFa');
        Route::post('profile/2fa/ajaxTwoFaSettingsVerify', 'CustomerController@ajaxTwoFaSettingsVerify');
        Route::post('profile/2fa/check-phone', 'CustomerController@checkPhoneFor2fa');
        //

        Route::post('prifile/update_password', 'CustomerController@updateProfilePassword');
        Route::match(['get', 'post'], 'profile-image-upload', 'CustomerController@profileImage');
        Route::post('profile/getVerificationCode', 'CustomerController@generatePhoneVerificationCode');
        Route::post('profile/complete-phone-verification', 'CustomerController@completePhoneVerification');
        Route::post('profile/add-phone-number', 'CustomerController@addPhoneNumberViaAjax'); //without verification
        Route::post('profile/update-phone-number', 'CustomerController@updatePhoneNumberViaAjax');
        Route::post('profile/editGetVerificationCode', 'CustomerController@editGeneratePhoneVerificationCode');
        Route::post('profile/edit-complete-phone-verification', 'CustomerController@editCompletePhoneVerification');
        Route::post('profile/delete-phone-number', 'CustomerController@deletePhoneNumberViaAjax');
        Route::post('prifile/update', 'CustomerController@updateProfileInfo');
        Route::post('profile/duplicate-phone-number-check', 'CustomerController@userDuplicatePhoneNumberCheck');
    });

    // Deposit - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_deposit']], function ()
    {
        Route::get('deposit-money/print/{id}', 'DepositController@depositPrintPdf');
    });

    // Deposit - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_deposit', 'check-user-suspended']], function ()
    {
        Route::match(array('GET', 'POST'), 'deposit', 'DepositController@create');
        Route::post('deposit/getDepositFeesLimit', 'DepositController@getDepositFeesLimit');
        Route::post('deposit/fees-limit-currency-payment-methods-is-active-payment-methods-list', 'DepositController@getDepositMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods');
        Route::post('deposit/store', 'DepositController@store');

        //Stripe
        Route::get('deposit/stripe_payment', 'DepositController@stripePayment');
        Route::post('deposit/stripe_payment_store', 'DepositController@stripePaymentStore');
        Route::get('deposit/stripe-payment/success', 'DepositController@stripePaymentSuccess')->name('deposit.stripe.success');

        //PayPal
        Route::get('deposit/payment_success', 'DepositController@paypalDepositPaymentConfirm');
        Route::get('deposit/payment_cancel', 'DepositController@paymentCancel');
        Route::get('deposit/paypal-payment/success', 'DepositController@paypalDepositPaymentSuccess')->name('deposit.paypal.success');

        //2Checkout
        Route::get('deposit/checkout/payment', 'DepositController@checkoutPayment');
        Route::get('deposit/checkout/payment/confirm', 'DepositController@checkoutPaymentConfirm');
        Route::get('deposit/checkout/payment/success', 'DepositController@checkoutPaymentSuccess')->name('deposit.checkout.success');

        //PayUmoney
        Route::get('deposit/payumoney_payment', 'DepositController@payumoneyPayment');
        Route::post('deposit/payumoney_confirm', 'DepositController@payumoneyPaymentConfirm');
        Route::get('deposit/payumoney_success', 'DepositController@payumoneyPaymentSuccess')->name('deposit.payumoney.success');
        Route::post('deposit/payumoney_fail', 'DepositController@payumoneyPaymentFail');

        //Bank
        Route::post('deposit/bank-payment', 'DepositController@bankPaymentConfirm');
        Route::post('deposit/bank-payment/get-bank-detail', 'DepositController@getBankDetailOnChange');
        Route::get('deposit/bank-payment/check-ref', 'DepositController@checkRef');
        Route::get('deposit/bank-payment/success', 'DepositController@bankPaymentSuccess')->name('deposit.bank.success');

        //Payeer
        Route::get('deposit/payeer/payment', 'DepositController@payeerPayement');
        Route::get('deposit/payeer/payment/confirm', 'DepositController@payeerPayementConfirm');
        Route::get('deposit/payeer/payment/fail', 'DepositController@payeerPayementFail');
        Route::get('deposit/payeer/payment/status', 'DepositController@payeerPayementStatus');
        Route::get('deposit/payeer/payment/success', 'DepositController@payeerPayementSuccess')->name('deposit.payeer.success');

        //Coinpayment (cancel-only)
        Route::get('deposit/coinpayments/cancel', 'DepositController@coinpaymentsCancel');
    
        
    });

    // Withdrawal - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_withdrawal']], function ()
    {



        Route::get('payouts', 'WithdrawalController@payouts');
        Route::get('payouts/detail/{id}', 'WithdrawalController@payoutsDetail');
        Route::get('payout/setting', 'WithdrawalController@payoutSetting');
        Route::get('withdrawal-money/print/{id}', 'WithdrawalController@withdrawalPrintPdf');
        
        
        
        
        //Gift Card
        
        Route::get('/countries', 'GiftCardController@getCountries')->name('giftcard.list');
        Route::get('/giftcard', 'GiftCardController@allgiftcard');
        Route::get('/giftcarddetails/{id}', 'GiftCardController@giftcarddetails');
        Route::get('order-gift-card', 'GiftCardController@order_gift_card');
        Route::post('/submit/giftcard', 'GiftCardController@SubmitGiftCard')->name('submit.giftcard');
    });

    // Withdrawal - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_withdrawal', 'check-user-suspended']], function ()
    {
        Route::post('payout/setting/store', 'WithdrawalController@payoutSettingStore');
        Route::post('payout/setting/update', 'WithdrawalController@payoutSettingUpdate');
        Route::post('payout/setting/delete', 'WithdrawalController@payoutSettingDestroy');
        Route::match(array('GET', 'POST'), 'payout', 'WithdrawalController@withdrawalCreate');
        Route::get('withdrawal/confirm-transaction', 'WithdrawalController@withdrawalConfirmation');
        Route::get('withdrawal/method/{id}', 'WithdrawalController@selectWithdrawalMethod');
        Route::post('withdrawal/store', 'WithdrawalController@withdrawalStore');
        Route::post('withdrawal/amount-limit', 'WithdrawalController@withdrawalAmountLimitCheck');
        Route::post('withdrawal/fees-limit-payment-method-isActive-currencies', 'WithdrawalController@getWithdrawalFeesLimitsActiveCurrencies');
    });

    //Transfer - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_transfer']], function ()
    {
        Route::get('moneytransfer/print/{id}', 'MoneyTransferController@transferPrintPdf');
    });

    //Transfer - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_transfer', 'check-user-suspended']], function ()
    {


        Route::match('GET', 'moneytransfer', 'MoneyTransferController@create');
        Route::post('transfer', 'MoneyTransferController@create');
        Route::post('transfer-user-email-phone-receiver-status-validate', 'MoneyTransferController@transferUserEmailPhoneReceiverStatusValidate');
        Route::post('amount-limit', 'MoneyTransferController@amountLimitCheck');
        Route::get('send-money-confirm', 'MoneyTransferController@sendMoneyConfirm');
    });










    // transactions
    Route::group(['middleware' => ['permission:manage_transaction']], function ()
    {
        Route::match(array('GET', 'POST'), 'transactions', 'UserTransactionController@index');
        Route::get('transactions/{id}', 'UserTransactionController@showDetails');
        Route::post('get_transaction', 'UserTransactionController@getTransaction');
        Route::get('transactions/print/{id}', 'UserTransactionController@getTransactionPrintPdf');
        Route::get('transactions/exchangeTransactionPrintPdf/{id}', 'UserTransactionController@exchangeTransactionPrintPdf');
        Route::get('transactions/merchant-payment-print/{id}', 'UserTransactionController@merchantPaymentTransactionPrintPdf');
        Route::get('transactions/crypto-sent-received-print/{id}', 'UserTransactionController@cryptoSentReceivedTransactionPrintPdf');
    });

    // Currency Exchange - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_exchange']], function ()
    {
        Route::get('exchange-money/print/{id}', 'ExchangeController@exchangeOfPrintPdf');
    });

    // Currency Exchange - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_exchange', 'check-user-suspended']], function ()
    {
        Route::get('exchange', 'ExchangeController@exchange');
        Route::post('exchange-of-money', 'ExchangeController@exchangeOfCurrency');
        Route::post('exchange/get-currencies-except-users-existing-wallets', 'ExchangeController@getActiveHasTransactionExceptUsersExistingWalletsCurrencies');
        Route::post('exchange/get-currencies-exchange-rate', 'ExchangeController@getCurrenciesExchangeRate');
        Route::get('exchange-of-money-success', 'ExchangeController@exchangeOfCurrencyConfirm');
        Route::post('exchange/getBalanceOfToWallet', 'ExchangeController@getBalanceOfToWallet');
        Route::post('exchange/amount-limit-check', 'ExchangeController@amountLimitCheck');
    });

    // Request Payments - Without Suspend Middleware
    Route::group(['middleware' => ['permission:manage_request_payment']], function ()
    {
        Route::get('request-payment/print/{id}', 'RequestPaymentController@printPdf');
        Route::post('request_payment/cancel', 'RequestPaymentController@cancel');
        Route::post('request_payment/cancelfrom', 'RequestPaymentController@cancelfrom');
    });

    // Request Payments - With Suspend Middleware
    Route::group(['middleware' => ['permission:manage_request_payment', 'check-user-suspended']], function ()
    {
        Route::get('request_payment/check-creator-status', 'RequestPaymentController@checkReqCreatorStatus');
        Route::get('request_payment/add', 'RequestPaymentController@add');
        Route::post('request', 'RequestPaymentController@store');
        Route::get('request_payment/accept/{id}', 'RequestPaymentController@requestAccept');
        Route::post('request-payment/amount-limit', 'RequestPaymentController@amountLimitCheck');
        Route::post('request_payment/request-user-email-phone-receiver-status-validate', 'RequestPaymentController@requestUserEmailPhoneReceiverStatusValidate');
        Route::post('request_payment/accepted', 'RequestPaymentController@requestAccepted');
        Route::get('request_payment/accept-money-confirm', 'RequestPaymentController@requestAcceptedConfirm');
        Route::get('request-money-confirm', 'RequestPaymentController@requestMoneyConfirm');
    });
    
    
     Route::group(['middleware' => ['permission:manage_product']], function ()
    {
        
        // 31-10-2020 rajesh
         Route::get('categories', 'CategoriesController@index');
        Route::get('categories/add', 'CategoriesController@add');
        Route::get('categories/edit/{id}', 'CategoriesController@edit');
        Route::post('categories/store', 'CategoriesController@store');
        Route::post('categories/update', 'CategoriesController@update');
        // 31-10-2020 rajesh
        // 2-11-2020
        Route::get('attributes', 'AttributesController@index');
        Route::get('attributes/add', 'AttributesController@add');
        Route::get('attributes/edit/{id}', 'AttributesController@edit');
        Route::post('attributes/store', 'AttributesController@store');
        Route::post('attributes/update', 'AttributesController@update');
        
        Route::get('attributes/addvalue', 'AttributesController@addvalue');
        Route::get('attributes/editvalue/{id}', 'AttributesController@editvalue');
        Route::post('attributes/storevalue', 'AttributesController@storevalue');
        Route::post('attributes/updatevalue', 'AttributesController@updatevalue');
         Route::get('customers', 'CustomerController@customers');
        // 2-11-2020
        // 3-11-2020
        Route::get('packeging', 'PackegingController@index');
        Route::get('packeging/add', 'PackegingController@add');
        Route::get('packeging/edit/{id}', 'PackegingController@edit');
         Route::post('packeging/store', 'PackegingController@store');
         Route::post('packeging/update', 'PackegingController@update');
        // 3-11-2020
        
        Route::get('products', 'ProductController@index');
        Route::get('product/add', 'ProductController@add');
        Route::get('product/edit/{id}', 'ProductController@edit');
        Route::post('product/store', 'ProductController@store');
        Route::post('product/update', 'ProductController@update');
        
         // QR Code - starts
         Route::post('merchant/generate-standard-merchant-payment-qrCode', 'MerchantController@generateStandardMerchantPaymentQrCode');
         Route::post('merchant/generate-express-merchant-qr-code', 'MerchantController@generateExpressMerchantQrCode');
         Route::post('merchant/update-express-merchant-qr-code', 'MerchantController@updateExpressMerchantQrCode');
         Route::get('merchant/qr-code-print/{id}/{printQrCode}', 'MerchantController@printMerchantQrCode');
         
         Route::post('merchant/generate-store-merchant-qr-code', 'MerchantController@generateStoreMerchantQrCode');
         Route::post('merchant/update-store-merchant-qr-code', 'MerchantController@updateStoreMerchantQrCode');
         // QR Code - ends
        Route::get('merchant/detail/{id}', 'MerchantController@detail');
        Route::get('merchant/payments', 'MerchantController@payments');
        
        Route::get('merchant/edit/{id}', 'MerchantController@edit');
        Route::post('merchant/update', 'MerchantController@update');
        // Route::post('getOperator', 'MerchantController@getOperator');
        // Route::post('getvalue', 'MerchantController@getvalue');
        // Route::post('makerecharge', 'MerchantController@makerecharge');
    });

    // Merchants
    Route::group(['middleware' => ['permission:manage_merchant']], function ()
    {
        Route::get('merchants', 'MerchantController@index');


         // QR Code - starts
         Route::post('merchant/generate-standard-merchant-payment-qrCode', 'MerchantController@generateStandardMerchantPaymentQrCode');
         Route::post('merchant/generate-express-merchant-qr-code', 'MerchantController@generateExpressMerchantQrCode');
         Route::post('merchant/update-express-merchant-qr-code', 'MerchantController@updateExpressMerchantQrCode');
         Route::get('merchant/qr-code-print/{id}/{printQrCode}', 'MerchantController@printMerchantQrCode');
         // QR Code - ends
        Route::get('merchant/detail/{id}', 'MerchantController@detail');
        Route::get('merchant/payments', 'MerchantController@payments');
        Route::get('merchant/add', 'MerchantController@add');
        Route::post('merchant/store', 'MerchantController@store');
        Route::get('merchant/edit/{id}', 'MerchantController@edit');
        Route::post('merchant/update', 'MerchantController@update');
        // Route::post('getOperator', 'MerchantController@getOperator');
        // Route::post('getvalue', 'MerchantController@getvalue');
        // Route::post('makerecharge', 'MerchantController@makerecharge');
    });

    // Disputes
    Route::group(['middleware' => ['permission:manage_dispute']], function ()
    {
        Route::get('disputes', 'DisputeController@index');
        Route::get('dispute/add/{id}', 'DisputeController@add');
        Route::post('dispute/open', 'DisputeController@store');
        Route::get('dispute/discussion/{id}', 'DisputeController@discussion');
        Route::post('dispute/reply', 'DisputeController@storeReply');
        Route::post('dispute/change_reply_status', 'DisputeController@changeReplyStatus');


    });

    // Tickets
    Route::group(['middleware' => ['permission:manage_ticket']], function ()
    {
        Route::get('tickets', 'TicketController@index');
        Route::get('ticket/add', 'TicketController@create');
        Route::post('ticket/store', 'TicketController@store');
        Route::get('ticket/reply/{id}', 'TicketController@reply');
        Route::post('ticket/reply_store', 'TicketController@reply_store');
        Route::post('ticket/change_reply_status', 'TicketController@changeReplyStatus');
        
        
       
        /*For Topup Reacharge From Start form Here (Shubham Kumar Date: 05/05/2021)*/
        // Route::get('gettoken','TopupController@gettoken');
        // Route::get('get-form-topup', 'TopupController@index');
        // Route::get('get-operator-details','TopupController@getOperator');
        // Route::post('post-topup-data','TopupController@post_topup_data');
        // Route::get('all-topups','TopupController@all_topups');
        // Route::get('your-limit','TopupController@your_limit');
        
        Route::post('ding-getoperatorplan', 'DingConnectController@getoperatorplan');
        Route::get('ding-connect', 'DingConnectController@index');
        Route::post('ding-getOperator', 'DingConnectController@getOperator');
        Route::post('ding-getvalue', 'DingConnectController@getvalue');
        Route::post('ding-confirmvalue', 'DingConnectController@confirmvalue');
        Route::post('ding-makerecharge', 'DingConnectController@makerecharge');
        Route::get('do_topup', 'DingConnectController@run_curl_post');
        
        Route::get('topup', 'TopupController@index');
        Route::post('getOperator', 'TopupController@getOperator');
        Route::post('getvalue', 'TopupController@getvalue');
        Route::post('confirmvalue', 'TopupController@confirmvalue');
        Route::post('getoperatorplan', 'TopupController@getoperatorplan');
        Route::post('getwallet', 'TopupController@getwallet');
        Route::post('makerecharge', 'TopupController@makerecharge');
        
       
    });

    //Crypt Send/Transfer/Withdraw - With check enabled cryto preference and suspend middleware
    Route::group(['middleware' => ['check-enabled-currencies-preference','check-user-suspended']], function ()
    {
        Route::get('crpto/send/{walletCurrencyCode}/{walletId}', 'CryptoSendController@sendCryptoCreate');
        Route::get('crpto/send/validate-address', 'CryptoSendController@validateCryptoAddress');
        Route::get('crpto/send/validate-user-balance', 'CryptoSendController@validateUserBalanceAgainstAmount');
        Route::post('crpto/send/confirm', 'CryptoSendController@sendCryptoConfirm');
        Route::get('crpto/send/success', 'CryptoSendController@sendCryptoSuccess');
    });

    //Crypt Receive - With check enabled cryto preference and suspend middleware
    Route::group(['middleware' => ['check-enabled-currencies-preference','check-user-suspended']], function ()
    {
        Route::get('crpto/receive/{walletCurrencyCode}/{walletId}', 'CryptoReceiveController@receiveCryptoCreate');
    });
});

/* Merchant Payment Start*/
Route::match(array('GET', 'POST'), 'payment/form', 'MerchantPaymentController@index')->name('user.merchant.payment_form');
Route::get('payment/method-form', 'MerchantPaymentController@showPaymentForm')->name('user.merchant.show_payment_form');
Route::get('payment/success', 'MerchantPaymentController@success')->name('merchant.payment.success');
Route::get('payment/fail', 'MerchantPaymentController@fail');

//paymoney
Route::post('payment/mts_pay', 'MerchantPaymentController@mtsPayment');

//stripe
Route::post('payment/stripe', 'MerchantPaymentController@stripePayment');
Route::post('standard-merchant/stripe-make-payment', 'MerchantPaymentController@stripeMakePayment');

//paypal
Route::POST('payment/paypal_payment_success', 'MerchantPaymentController@paypalPaymentSuccess');

//payumoney
Route::post('payment/payumoney', 'MerchantPaymentController@payumoney');
Route::post('payment/payumoney_success', 'MerchantPaymentController@payuPaymentSuccess');
Route::post('payment/payumoney_fail', 'MerchantPaymentController@merchantPayumoneyPaymentFail');

//CoinPayments
Route::post('payment/coinpayments', 'MerchantPaymentController@coinPayments');
Route::post('payment/coinpayments/make-transaction', 'MerchantPaymentController@coinPaymentMakeTransaction');
Route::get('payment/coinpayments/coinpayment-transaction-info', 'MerchantPaymentController@viewCoinpaymentTransactionInfo');

/* PayMoney Merchant API Start*/
Route::post('merchant/api/verify', 'ExpressMerchantPaymentController@verifyClient');
Route::match(array('GET', 'POST'), 'merchant/payment', 'ExpressMerchantPaymentController@generatedUrl');
Route::post('merchant/api/transaction-info', 'ExpressMerchantPaymentController@storeTransactionInfo');
Route::get('merchant/payment/cancel', 'ExpressMerchantPaymentController@cancelPayment');

Route::group(['middleware' => ['guest:users']], function () {
    Route::get('merchant/payment/confirm', 'ExpressMerchantPaymentController@confirmPayment');
});


Route::get('gateway-payment/success', 'MerchantPaymentController@paymentSuccess')->name('gateway.payment.success');

Route::get('gateway/payment/{gateway}', 'GatewayController@pay')->name('gateway.pay');
Route::post('gateway/confirm-payment', 'GatewayController@confirmPayment')->name('gateway.confirm_payment');
Route::match(array('GET', 'POST'), 'gateway/payment-verify/{gateway}', 'GatewayController@verify')->name('gateway.payment_verify');
Route::match(array('GET', 'POST'), 'gateway/payment-cancel/{gateway}', 'GatewayController@cancelPayment')->name('gateway.payment_cancel');




Route::post('deposit/bank-payment/get-bank-detail', 'Users\DepositController@getBankDetailOnChange')->name('user.deposit.bank.details');
Route::get('deposit/success', 'Users\DepositController@depositSuccess')->name('user.deposit.success');
Route::get('deposit/complete', 'Users\DepositController@depositComplete')->name('deposit.complete');
Route::get('deposit-money/print/{id}', 'Users\DepositController@depositPrintPdf')->name('user.deposit.print');

Route::get('coinpayment/summery', 'Users\DepositController@coinPaymentSummary')->name('coinpayment.summery');



Route::get('download/package', 'ContentController@downloadPackage');
Route::get('{url}', 'ContentController@pageDetail');

