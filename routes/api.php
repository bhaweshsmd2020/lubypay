<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
 */
Route::group(['namespace' => 'Api', 'middleware' => ['cors']], function ()
{
    //Registration
    Route::get('get-device-info', 'RegistrationController@getDeviceInfo');
    Route::post('send-notification','RegistrationController@SendNotification');
    Route::get('check_password','RegistrationController@CheckPassword');
    Route::post('registration-with-otp', 'RegistrationController@registrationWithOtp');
    Route::get('check-merchant-user-role-existence', 'RegistrationController@getMerchantUserRoleExistence');
    Route::post('registration', 'RegistrationController@registration');
    Route::post('registration/duplicate-email-check', 'RegistrationController@duplicateEmailCheckApi');
    Route::post('registration/duplicate-phone-number-check', 'RegistrationController@duplicatePhoneNumberCheckApi');
    Route::post('registration/duplicate-device-check', 'RegistrationController@duplicateDeviceCheckApi');
    Route::post('reset-password', 'RegistrationController@resetPassword');
    Route::post('register-device', 'RegistrationController@registerDevices');
    Route::post('noticeboard', 'RegistrationController@noticeboard');
    Route::post('read-notification', 'RegistrationController@readNotification');
    Route::post('message_detail', 'RegistrationController@message_detail');
    Route::post('banner', 'RegistrationController@banner');
    Route::post('registration/duplicate-device-register', 'RegistrationController@duplicateDeviceRegister');
    Route::post('registration/send-email', 'RegistrationController@send_email');
    Route::post('registration/verify-email', 'RegistrationController@verify_email');
    Route::post('check-sms-gateway', 'RegistrationController@checkSmsGateway');
    Route::post('stripe-webhook', 'PlaidController@stripeWebhook')->name('stripe.webhook');
    
    //Login
    Route::get('get-preference-settings', 'LoginController@getPreferenceSettings');
    Route::get('check-login-via', 'LoginController@checkLoginVia');
    Route::post('check-app-update', 'LoginController@checkAppUpdate');
    Route::post('login', 'LoginController@login');
    // Route::post('send-otp', 'LoginController@sendOTP');
    Route::post('resend-otp', 'LoginController@resendOTP');
    Route::post('verify-otp', 'LoginController@verifyOTP');
    Route::post('send-forgot-otp', 'LoginController@sendforgotOTP');
    Route::post('verify-forgot-otp', 'LoginController@verifyforgotOTP');
    Route::post('login-with-otp', 'LoginController@loginWithOTP');
    Route::post('login-with-passcode', 'LoginController@loginWithPasscode');
    Route::post('login-with-touch', 'LoginController@loginWithTouch');
    Route::post('forgot-password', 'LoginController@forgotPassword');
    Route::post('logout', 'LoginController@logoutfromotherdevices');
    Route::post('logout-device', 'LoginController@logout');
    Route::post('get-offers', 'LoginController@allOffers');
    Route::post('read-offers', 'LoginController@readOffers');
    Route::post('check-maintainance', 'LoginController@check_maintainance');
    
    //Lithic
    Route::post('add-funding-account','LithicController@AddFundingAccount');
    Route::post('enroll-virtual-user','LithicController@EnrollAccount');
    Route::post('create-virtual-card','LithicController@CreateCard');
    Route::post('get-virtual-card-list','LithicController@GetCardList');
    Route::post('update-virtual-card','LithicController@UpdateCard');
    Route::post('open-close-card','LithicController@OpenCloseCard');
    Route::post('get-transaction','LithicController@GetTransaction');
    Route::post('transaction-webhook','LithicController@TransactionWebhook');
    Route::post('get-current-card-limit','LithicController@GetCardLimit');
    Route::post('get-card-limit', 'LithicController@getCardLimitAmt');
    Route::post('set-account-limit','LithicController@SetAccountLimit');
    
    //Services
    Route::post('get-services', 'ServiceController@getServices');
    
    //Transaction
    Route::get('activityall', 'TransactionController@getTransactionApi');
    Route::post('read-transaction', 'TransactionController@readTransaction');
    
    //SendMoney
    Route::post('get-last-five-send-transaction', 'SendMoneyController@GetSendTransaction');
    Route::get('get-send-money-currencies', 'SendMoneyController@getSendMoneyCurrenciesApi');
    
    //Profile
    Route::get('check-user-status', 'ProfileController@checkUserStatusApi');
    Route::post('upload-kyc-document', 'ProfileController@uploadKycDocument');
    Route::post('upload-kyc-document-react', 'ProfileController@uploadKycDocumentReact');
    Route::post('kyc-status-store', 'ProfileController@kycstatusstore');
    Route::get('get-kyc-status-manual-persona', 'ProfileController@getKycStatusManualPersona');
    Route::post('upload-json','ProfileController@testcreateImageFromBase64');
    
    //Wallet
    Route::post('store-temp-token', 'WalletController@store_temp_token');
    Route::post('check-wallet', 'WalletController@check_wallet');
    Route::post('currencies-exchange-rate', 'WalletController@currencies_exchange_rate');
    Route::post('update-wallet', 'WalletController@update_wallet');
    Route::post('store-card-token', 'WalletController@store_card_token');
    Route::post('check-card-wallet', 'WalletController@check_card_wallet');
    Route::post('update-card-wallet', 'WalletController@update_card_wallet');
    Route::post('get-wallet-details', 'WalletController@get_wallet_details');
    Route::post('card-to-card-notify', 'WalletController@card_to_card_notify');
    Route::post('exchange-amount', 'WalletController@exchange_amount');
    Route::post('card-exchange-rate', 'WalletController@card_exchange_rate');
    
    //Support Ticket
    Route::post('new-support-ticket', 'SupportController@newSupportTicket');
    Route::post('list-support-ticket', 'SupportController@listSupportTicket');
    Route::post('send-message', 'SupportController@sendMessage');
    Route::post('ticket-details', 'SupportController@ticketDetails');
    Route::post('send-sns', 'SupportController@send_sns');
    Route::get('api_key_data', 'SupportController@ApikeyData');
    Route::post('read-support-ticket', 'SupportController@ReadSupportTicket');

    //Webhook
    Route::post('/user/registration', 'WebhookController@registration');
    
    //Reloadly
    Route::get('get-countries', 'ReloadlyController@getCountries');
    Route::post('kyc-countries', 'ReloadlyController@kycCountries');
    Route::get('get-all-countries', 'ReloadlyController@getallCountries');
    Route::get('get-new-countries', 'ReloadlyController@testcountry');
    
    //Language
    Route::get('changelanguage', 'LanguageController@change_language');
    Route::get('devicelanguage', 'LanguageController@device_language');
    Route::post('cms-language', 'LanguageController@cmslanguage');   
    Route::get('get-language', 'LanguageController@get_language');
    Route::post('card-update', 'CardController@cardUpdate')->name('card.update');
    Route::post('change-authentication', 'RegistrationController@changeAuthentication');

});

/*
|--------------------------------------------------------------------------
| API Routes - With Authorization Middleware
|--------------------------------------------------------------------------
 */
Route::group(['namespace' => 'Api', 'middleware' => ['check-authorization-token','cors']], function ()
{
    //Login
    Route::post('partner-details', 'LoginController@partner_details');
    
    //Registration
    Route::post('enable-touch', 'RegistrationController@enableTouch');
    Route::post('enable-passcode', 'RegistrationController@enablePasscode');
    Route::post('set-passcode', 'RegistrationController@setPasscode');
    Route::post('change-passcode', 'RegistrationController@changePasscode');
    Route::post('update-password', 'RegistrationController@update_password');
    Route::post('change-password', 'RegistrationController@changePassword');

    //Profile
    Route::post('pages', 'ProfileController@pages');
    Route::post('vcard_status', 'ProfileController@vcard_status');
    Route::match(array('GET','POST'),'get-default-wallet-balance', 'ProfileController@getDefaultWalletBalance');
    Route::get('get-user-profile', 'ProfileController@getUserProfile');
    Route::post('update-user-profile', 'ProfileController@updateUserProfile');
    Route::get('get-user-specific-details', 'ProfileController@getUserSpecificProfile');
    Route::post('profile/duplicate-email-check', 'ProfileController@userProfileDuplicateEmailCheckApi');
    Route::get('check-processed-by', 'ProfileController@checkProcessedByApi');
    Route::get('available-balance', 'ProfileController@getUserAvailableWalletsBalances');
    Route::get('get-kyc-status', 'ProfileController@getKycStatus');
    
    //Deposit
    Route::get('get-fees-list-for-topup', 'DepositMoneyController@getFeesListByForTopup');
    Route::get('get-payment-method-list', 'DepositMoneyController@getPaymentMethodList');
    Route::get('get-deposit-currency-list', 'DepositMoneyController@getDepositCurrencyList');
    Route::post('get-deposit-bank-list', 'DepositMoneyController@getDepositBankList');
    Route::post('fees-limit-currency-payment-methods-is-active-payment-methods-list', 'DepositMoneyController@getDepositMatchedFeesLimitsCurrencyPaymentMethodsSettingsPaymentMethods');
    Route::get('get-fees-list-by-payment-method', 'DepositMoneyController@getFeesListByPaymentMethod');
    Route::get('get-deposit-details-with-amount-limit-check', 'DepositMoneyController@getDepositDetailsWithAmountLimitCheck');
    Route::post('deposit/get-bank-detail', 'DepositMoneyController@getBankDetails');
    Route::post('deposit/bank-payment-store', 'DepositMoneyController@bankPaymentStore');
    Route::post('deposit/get-stripe-info', 'DepositMoneyController@getStripeInfo');
    Route::post('deposit/stripe-payment-store', 'DepositMoneyController@stripePaymentStore');
    Route::post('deposit/get-paypal-info', 'DepositMoneyController@getPeypalInfo');
    Route::post('deposit/paypal-payment-store', 'DepositMoneyController@peypalPaymentStore');
    Route::post('get-intent-info', 'DepositMoneyController@getIntentInfo');
    Route::post('retrieve-intent-info', 'DepositMoneyController@getRetrieveInfo');  
    Route::post('deposit/stripe-deposit-store', 'DepositMoneyController@stripeDepositStore');
    Route::post('braintree', 'DepositMoneyController@braintree');
    Route::post('deposit/braintree-payment-store', 'DepositMoneyController@braintreePaymentStore');
    
    //SendMoney
    Route::get('get-notification', 'SendMoneyController@testnoti');
    Route::post('send-money-email-check', 'SendMoneyController@postSendMoneyEmailCheckApi');
    Route::post('send-money-phone-check', 'SendMoneyController@postSendMoneyPhoneCheckApi');
    Route::get('get-send-money-currencies', 'SendMoneyController@getSendMoneyCurrenciesApi');
    Route::post('check-send-money-amount-limit', 'SendMoneyController@postSendMoneyFeesAmountLimitCheckApi');
    Route::post('send-money-pay', 'SendMoneyController@postSendMoneyPayApi');
    Route::get('get-recommended-amount', 'SendMoneyController@getRecommendedamt');
    
    //Reloadly
    Route::post('get-operators-by-iso', 'ReloadlyController@getOperatorsByIso');
    Route::post('get-operators-for-phone', 'ReloadlyController@getOperatorsForPhone');
    Route::post('get-balance', 'ReloadlyController@getBalance');
    Route::post('get-operator-by-id', 'ReloadlyController@getOperatorById');
    Route::post('get-operators-by-iso', 'ReloadlyController@getOperatorsByIso');
    Route::post('get-operators-for-phone', 'ReloadlyController@getOperatorsForPhone');
    Route::post('get-balance', 'ReloadlyController@getBalance');
    Route::post('get-operator-by-id', 'ReloadlyController@getOperatorById');
    Route::post('top-up', 'ReloadlyController@topUp');
    
    //Ding
    Route::group(['prefix' => 'ding'], function (){
        Route::post('get-operator', 'DingController@getOperator');
        Route::post('get-operator-plans', 'DingController@getoperatorplan');
        Route::post('make-recharge', 'DingController@makerecharge');
        Route::post('get-product', 'DingController@getProductByNumber');
        Route::post('get-estimated-price', 'DingController@getEstimatedPrice');
    });
    
    //Topup
    Route::post('get-operator', 'TopupController@getOperator');
    Route::post('get-operator-plans', 'TopupController@getoperatorplan');
    Route::post('topup-details', 'TopupController@getvalue');
    Route::post('get-wallet', 'TopupController@getwallet');
    Route::post('my-wallets', 'TopupController@mywallets');
    Route::post('make-recharge', 'TopupController@makerecharge');

    //Transaction
    Route::get('activityall', 'TransactionController@getTransactionApi');
    Route::post('transaction-unread-count', 'TransactionController@transactionUnreadCount');
    Route::get('activitysummary', 'TransactionController@getTransactionsummary');
    Route::get('transaction-details', 'TransactionController@getTransactionDetailsApi');
    
    //RequestMoney
    Route::get('transaction-details/request-payment/check-creator-status', 'RequestMoneyController@checkReqCreatorStatusApi');
    Route::post('transaction-details/acceptmoney', 'RequestMoneyController@acceptmoney');
    Route::post('transaction-details/cancelmoney', 'RequestMoneyController@cancelmoney');
    Route::post('phonebook', 'RequestMoneyController@phonebook');
    Route::get('sendnotification','RequestMoneyController@sendFCMPush1');
    Route::post('request-money-email-check', 'RequestMoneyController@postRequestMoneyEmailCheckApi');
    Route::post('request-money-phone-check', 'RequestMoneyController@postRequestMoneyPhoneCheckApi');//
    Route::get('get-request-currency', 'RequestMoneyController@getRequestMoneyCurrenciesApi');//
    Route::post('request-money-pay', 'RequestMoneyController@postRequestMoneyPayApi');
    Route::post('get-last-five-request-transaction', 'RequestMoneyController@GetFiveRequest');
    Route::get('accept-request-email-phone', 'AcceptCancelRequestMoneyController@getAcceptRequestEmailOrPhone');
    Route::post('request-accept-amount-limit-check', 'AcceptCancelRequestMoneyController@getAcceptRequestAmountLimit');
    Route::get('get-accept-fees-details', 'AcceptCancelRequestMoneyController@getAcceptFeesDetails');
    Route::post('accept-request-payment-pay', 'AcceptCancelRequestMoneyController@requestAcceptedConfirm');
    Route::post('cancel-request', 'AcceptCancelRequestMoneyController@requestCancel');
    
    //Store
    Route::post('store-list', 'StoreController@store_list');
    Route::post('store-update', 'StoreController@store_update');
    Route::post('store-category-list', 'StoreController@store_category_list');
    Route::post('store-category-add', 'StoreController@store_category_add');
    Route::post('store-category-update', 'StoreController@store_category_update');
    Route::post('store-attributes-list', 'StoreController@store_attributes_list');
    Route::post('store-attributes-add', 'StoreController@store_attributes_add');
    Route::post('store-attributes-value', 'StoreController@store_attributes_value');
    Route::post('store-attributes-update', 'StoreController@store_attributes_update');
    Route::post('store-attributes-value-list', 'StoreController@store_attributes_value_list');
    Route::post('store-attributes-value-add', 'StoreController@store_attributes_value_add');
    Route::post('store-attributes-value-update', 'StoreController@store_attributes_value_update');
    Route::post('store-products-list', 'StoreController@store_products_list');
    Route::post('store-products-add', 'StoreController@store_products_add');
    Route::post('store-products-update', 'StoreController@store_products_update');
    Route::post('store-product-details', 'StoreController@store_products_details');
    Route::post('store-product-stock', 'StoreController@store_products_stock');
    Route::post('store-orders-list', 'StoreController@store_orders_list');
    Route::post('store-order-details', 'StoreController@store_order_details');
    Route::post('store-order-status-update', 'StoreController@store_order_status_update');
    Route::post('add-cart', 'StoreController@add_cart');
    Route::post('cart', 'StoreController@cart');
    Route::post('update-cart', 'StoreController@update_cart');
    Route::post('checkout', 'StoreController@checkout');
    Route::post('update-order', 'StoreController@update_order');
    Route::post('store-qrcode', 'StoreController@store_qrcode');
    Route::post('fetch-credentials', 'StoreController@fetch_credentials');
    Route::post('check-order', 'StoreController@check_order');
    Route::post('today-payment', 'StoreController@today_payment');
    Route::post('invoice', 'StoreController@invoice');
    Route::post('country-payouts', 'StoreController@country_payouts');
    Route::post('add-country-bank', 'StoreController@add_country_bank');
    Route::post('country-bank-list', 'StoreController@country_bank_list');
    Route::post('bank-payout', 'StoreController@bank_payout');
    Route::post('check-country-bank', 'StoreController@check_country_bank');
    Route::post('edit-country-bank', 'StoreController@edit_country_bank');
    Route::post('delete-country-bank', 'StoreController@delete_country_bank');
    Route::post('bank-check-limit', 'StoreController@bank_check_limit');
    Route::post('transaction-limit', 'StoreController@transaction_limit');
    
        //collect payment by touch card
    Route::post('collect-payment/collect-payment-store', 'StoreController@CollectPaymentStore');
    Route::post('collect-payment-invoice', 'StoreController@collectPaymentInvoice');
    Route::post('create-collect-payment-wallet', 'StoreController@createCollectPaymentWallet');

    //PayoutSetting
    Route::get('payout-setting', 'PayoutSettingController@index');
    Route::POST('add-withdraw-setting', 'PayoutSettingController@store');
    Route::POST('edit-withdraw-setting', 'PayoutSettingController@update');
    Route::POST('delete-payout-setting', 'PayoutSettingController@delete');
    Route::get('get-withdraw-payment-methods', 'PayoutSettingController@paymentMethods');
    Route::get('withdrawal/get-all-countries', 'PayoutSettingController@getAllCountries');

    //PayoutMoney
    Route::get('check-payout-settings', 'PayoutMoneyController@checkPayoutSettingsApi');
    Route::get('get-withdraw-payment-method', 'PayoutMoneyController@getWithdrawalPaymentMethod');
    Route::get('get-withdraw-currencies-based-on-payment-method', 'PayoutMoneyController@getWithdrawalCurrencyBasedOnPaymentMethod');
    Route::get('get-withdraw-details-with-amount-limit-check', 'PayoutMoneyController@getWithdrawDetailsWithAmountLimitCheck');
    Route::post('withdraw-money-pay', 'PayoutMoneyController@withdrawMoneyConfirm');

    //ExchangeMoney
    Route::get('get-User-Wallets-WithActive-HasTransaction', 'ExchangeMoneyController@getUserWalletsWithActiveAndHasTransactionCurrency');
    Route::post('exchange-review', 'ExchangeMoneyController@exchangeReview');
    Route::post('getBalanceOfFromAndToWallet', 'ExchangeMoneyController@getBalanceOfFromAndToWallet');
    Route::post('getWalletsExceptSelectedFromWallet', 'ExchangeMoneyController@getWalletsExceptSelectedFromWallet');
    Route::post('get-currencies-exchange-rate', 'ExchangeMoneyController@getCurrenciesExchangeRate');
    Route::post('review-exchange-details', 'ExchangeMoneyController@reviewExchangeDetails');
    Route::post('exchange-money-complete', 'ExchangeMoneyController@exchangeMoneyComplete');

    //qrCode
    Route::post('perform-qr-code-operation', 'QrCodeController@performQrCodeOperationApi');
    Route::get('get-user-qr-code', 'QrCodeController@getUserQrCode');
    Route::post('add-or-update-user-qr-code', 'QrCodeController@addOrUpdateUserQrCode');
    Route::post('send-money-request-money-perform-qr-code-operation', 'QrCodeController@performSendMoneyRequestMoneyQrCodeOperationApi');
    Route::post('perform-merchant-payment-qr-code-review', 'QrCodeController@performMerchantPaymentQrCodeReviewApi');
    Route::post('perform-merchant-payment-qr-code-submit', 'QrCodeController@performMerchantPaymentQrCodeSubmit');
    Route::post('perform-express-merchant-payment-qr-code-merchant-currency-user-wallets-review', 'QrCodeController@performExpressMerchantPaymentMerchantCurrencyUserWalletsReviewApi'); //new
    Route::post('perform-express-merchant-payment-qr-code-merchant-amount-review', 'QrCodeController@performExpressMerchantPaymentAmountReviewApi');
    Route::post('perform-express-merchant-payment-qr-code-submit', 'QrCodeController@performExpressMerchantPaymentQrCodeSubmit');
    Route::post('perform-store-merchant-payment-qr-code-merchant-currency-user-wallets-review', 'QrCodeController@performStoreMerchantPaymentMerchantCurrencyUserWalletsReviewApi');
    Route::post('perform-store-merchant-payment-qr-code-submit', 'QrCodeController@performStoreMerchantPaymentQrCodeSubmit');
    Route::post('qrwallet', 'QrCodeController@qrwallet');

    //CryptoCurrency
    Route::get('crypto/get-user-crypto-wallets', 'CryptoCurrencyController@getUserCryptoWallets');
    Route::get('crypto/get-user-crypto-wallet-address', 'CryptoCurrencyController@getUserCryptoWalletAddress');
    Route::get('crypto/get-enabled-currencies-preference', 'CryptoCurrencyController@getEnabledCurrenciesPreference');
    Route::get('crypto/get-crypto-currency-status', 'CryptoCurrencyController@getCryptoCurrencyStatus');

    //CryptoSend
    Route::get('crypto/send/get-network-fee', 'CryptoSendController@getNetworkFee');
    Route::post('crypto/send/check-receiver-address', 'CryptoSendController@checkReceiverAddress');
    Route::post('crypto/send/check-amount-balance', 'CryptoSendController@checkAmountBalance');
    Route::post('crypto/send/review', 'CryptoSendController@cryptoSendReview');
    Route::post('crypto/send/confirm', 'CryptoSendController@cryptoSendConfirm');
    
    //Virtualcard
    Route::get('virtualcard', 'VirtualcardController@virtualcard');
    Route::post('createCard', 'VirtualcardController@createCard');
    Route::post('updateVirtualCard', 'VirtualcardController@updateVirtualCard');
    Route::post('pausedVirtualCard', 'VirtualcardController@pausedVirtualCard');
    Route::post('openVirtualCard', 'VirtualcardController@openVirtualCard');
    Route::post('closeVirtualCard', 'VirtualcardController@closeVirtualCard');
    Route::get('virtualtransactions', 'VirtualcardController@virtualtransactions');
    Route::post('add-card-info','VirtualcardController@addnewfund');
    Route::post('get-saved-card-list','VirtualcardController@getsavedcard');
    Route::post('remove-saved-card','VirtualcardController@removesavedcard');
    
    //GiftCard
    Route::get('/countries', 'GiftCardController@getCountries')->name('giftcard.list');
    Route::get('/giftcard', 'GiftCardController@allgiftcard');
    Route::get('/giftcarddetails', 'GiftCardController@giftcarddetails');
    Route::post('/submit/giftcardpayment', 'GiftCardController@SubmitGiftCard')->name('submit.giftcard');
    Route::post('order-gift-card', 'GiftCardController@order_gift_card');
    Route::get('gift-code', 'GiftCardController@getGiftCodeApi');
    Route::post('gift/get-currencies-exchange-rate', 'GiftCardController@getCurrenciesExchangeRateforGift');
    Route::get('user-gift-list', 'GiftCardController@UserGiftList');
    
    Route::post('check-card-user', 'CardController@checkUser')->name('check.card.user');
    Route::post('card-registration', 'CardController@cardRegistration')->name('card.registration');
    Route::post('card-countries', 'CardController@cardCountries')->name('card.countries');
    Route::post('card-states', 'CardController@cardStates')->name('card.states');
    Route::post('card-cities', 'CardController@cardCities')->name('card.cities');
    Route::post('subscription-plans', 'CardController@subscriptionPlans')->name('subscription.plans');
    Route::post('subscribe', 'CardController@subscribe')->name('subscribe');
    Route::post('card-types', 'CardController@cardTypes')->name('card.types');
    Route::post('card-create', 'CardController@cardCreate')->name('card.create');
    Route::post('all-cards', 'CardController@allCards')->name('all.cards');
    Route::post('card-limit', 'CardController@cardLimit')->name('card.limit');
    Route::post('card-details', 'CardController@cardDetails')->name('card.details');
    Route::post('additional-details', 'CardController@additionalDetails')->name('additional.details');
    Route::post('card-status', 'CardController@cardStatus')->name('card.status');
    Route::post('card-topup', 'CardController@cardTopup')->name('card.topup');
    Route::post('preview-topup', 'CardController@previewTopup')->name('preview.topup');
    Route::post('card-transactions', 'CardController@cardTransactions')->name('card.transactions');
    Route::post('card-analytics', 'CardController@cardAnalytics')->name('card.analytics');
    Route::post('filter-transactions', 'CardController@filterTransactions')->name('filter.transactions');
    Route::post('subscription-details', 'CardController@subscriptionDetails')->name('subscription.details');
    Route::post('upgrade-subscription', 'CardController@upgradeSubscription')->name('upgrade.subscription');
    Route::post('renew-subscription', 'CardController@renewSubscription')->name('renew.subscription');
    
    Route::post('create-link-token', 'PlaidController@createLinkToken')->name('create.link.token');
    Route::post('get-link-token', 'PlaidController@getLinkToken')->name('get.link.token');
    Route::post('plaid-deposit-store', 'PlaidController@plaidDepositStore')->name('plaid.deposit.store');
});