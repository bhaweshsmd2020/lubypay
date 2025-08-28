<?php

use App\Http\Helpers\Common;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// Unauthenticated Admin
Route::group(['middleware' => ['no_auth:admin', 'locale']], function ()
{
    Route::get('/', function ()
    {
        return view('admin.auth.login');
    })->name('admin');

    Route::post('adminlog', 'AuthController@authenticate');
    Route::match(['GET', 'POST'], 'forget-password', 'AuthController@forgetPassword');
    Route::get('password/resets/{token}', 'AuthController@verifyToken');
    Route::post('confirm-password', 'AuthController@confirmNewPassword');
});

// Authenticated Admin
Route::group(['middleware' => ['guest:admin', 'locale']], function ()
{
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        (new Common())->one_time_message('success',__('Cache successfully cleared.'));
        return back();
    })->name('clear.cache');

    Route::post('submit-fa', 'AuthController@submitfa'); 
    Route::get('faverify', 'AuthController@faverify')->name('faverify');  
    
    // Profile
    Route::get('profile', 'ProfileController@profile');
    Route::post('update-profile/{id}', 'ProfileController@profileUpdate');
    Route::get('change-password', 'ProfileController@changePassword');
    Route::post('update-password', 'ProfileController@updatePassword');
    Route::post('2fa', 'ProfileController@submit2fa');
    Route::post('check-password', 'ProfileController@passwordCheck');
    Route::get('adminlogout', 'ProfileController@logout')->name('adminlogout');
    
    // Notification
    Route::post('notifications/update/{id}', 'NotificationController@update');
    Route::get('notifications', 'NotificationController@index');
    Route::get('notifications/update/all', 'NotificationController@updateall');
    Route::post('notifications/read', 'NotificationController@updateread');
    Route::get('pushsms', 'NotificationController@pushsms'); 
    Route::get('send-offer-push', 'NotificationController@offerpush'); 
    Route::post('add-offer-image', 'NotificationController@add_offer_image'); 
    Route::get('delete-offer/{id}', 'NotificationController@deleteOffer');
    Route::post('sendpushsms', 'NotificationController@sendpushsms');
    Route::get('edit-offer/{id}', 'NotificationController@editOffer');
    Route::post('update-offer/{id}', 'NotificationController@updateOffer');
    
    // Agent
    Route::get('user-deposit/', 'AgentController@userDeposit');
    Route::get('user-withdrawal/', 'AgentController@userWithdrawal');
    Route::get('user-send-request/', 'AgentController@userSendRequest');
    Route::get('user-receive-request/', 'AgentController@userReceiveRequest');
    
    // Carriers
    Route::get('carriers/', 'CarriersController@index');
    
    // Utility
    Route::get('utility/', 'UtilityController@index');
    Route::get('utility/add', 'UtilityController@add');
    Route::post('utility/add', 'UtilityController@add');
    Route::get('utility/edit/{id}', 'UtilityController@edit');
    Route::post('utility/edit', 'UtilityController@edit');
    Route::get('utility/delete/{id}', 'UtilityController@delete');
    Route::get('utility/cashin', 'UtilityController@cashin');
    Route::get('utility/postpaid-bill', 'UtilityController@postpaid_bill');
    Route::get('utility/prepaid-bill', 'UtilityController@prepaid_bill');
    Route::get('utility/payments', 'UtilityController@payments');
    
    // Cable Tv
    Route::get('utility/all-water-bill', 'CableTvController@index');
    Route::get('utility/water-bill-form', 'CableTvController@add');
    Route::post('utility/pay-cable-bill', 'CableTvController@paystore');
    Route::post('utility/find-cable-bill', 'CableTvController@findcablebill');
    Route::get('utility/cable', 'CableTvController@index');
    Route::get('utility/pay-cable-bill', 'CableTvController@add');
    Route::post('utility/pay-cable-bill', 'CableTvController@add');
    Route::get('utility/edit-cable-bill/{id}', 'CableTvController@edit');
    Route::post('utility/edit-cable-bill', 'CableTvController@edit');
    
    // Gas Bill
    Route::get('utility/gas', 'GasBillController@villgas');
    Route::get('utility/pay-villa-gas-bill','GasBillController@payvillgas');
    Route::post('utility/find-villa-gas-bill', 'GasBillController@findvillagas');
    Route::post('utility/pay-villa-gas-bill', 'GasBillController@storevillagas');  
    Route::post('utility/find-maldive-gas-bill', 'GasBillController@findmaldivegas');
    Route::post('utility/pay-maldive-gas-bill', 'GasBillController@paymaldivegas');
    Route::get('utility/gas', 'GasBillController@index');
    Route::get('utility/pay-gas-bill', 'GasBillController@add');
    Route::post('utility/pay-gas-bill', 'GasBillController@add');
    Route::get('utility/edit-gas-bill/{id}', 'GasBillController@edit');
    Route::post('utility/edit-gas-bill', 'GasBillController@edit');
    Route::get('utility/maldive-gas', 'GasBillController@index');
    
    // Electricity Bill
    Route::post('utility/find-electricity-bill', 'ElectricityBillController@findBill');
    Route::post('utility/pay-electricity-bill', 'ElectricityBillController@payBill');
    Route::get('utility/electricity', 'ElectricityBillController@index');
    Route::get('utility/pay-electricity-bill', 'ElectricityBillController@add');
    Route::post('utility/pay-electricity-bill', 'ElectricityBillController@add');
    Route::get('utility/edit-electricity-bill/{id}', 'ElectricityBillController@edit');
    Route::post('utility/edit-electricity-bill', 'ElectricityBillController@edit');
    
    // Water Bill
    Route::post('utility/find-cable_tv-bill', 'WaterBillController@findcabletvbill');
    Route::post('utility/pay-cable_tv-bill', 'WaterBillController@paycabletvbill');
    Route::get('utility/dhiragu-pay-cable-tv', 'WaterBillController@find_dhiraagu_cabletvbill');
    Route::get('utility/all-cable-bill', 'WaterBillController@index');
    Route::get('utility/cable-bill-form', 'WaterBillController@add');
    Route::get('utility/water', 'WaterBillController@index');
    Route::get('utility/pay-water-bill', 'WaterBillController@add');
    Route::post('utility/pay-water-bill', 'WaterBillController@add');
    Route::get('utility/edit-water-bill/{id}', 'WaterBillController@edit');
    Route::post('utility/edit-water-bill', 'WaterBillController@edit');
    
    // Internet Bill
    Route::get('utility/internet', 'InternetBillController@index');
    Route::get('utility/pay-internet-bill', 'InternetBillController@add');
    Route::post('utility/pay-internet-bill', 'InternetBillController@add');
    Route::get('utility/edit-internet-bill/{id}', 'InternetBillController@edit');
    Route::post('utility/edit-internet-bill', 'InternetBillController@edit');
    
    // Insurance
    Route::get('utility/insurance', 'InsuranceController@index');
    Route::get('utility/pay-insurance-bill', 'InsuranceController@add');
    Route::post('utility/pay-insurance-bill', 'InsuranceController@add');
    Route::get('utility/edit-insurance-bill/{id}', 'InsuranceController@edit');
    Route::post('utility/edit-insurance-bill', 'InsuranceController@edit');
    
    // Gift Card
    Route::get('card/gift-card', 'GiftCardController@index');
    Route::get('card/add-gift-card', 'GiftCardController@add');
    Route::post('card/add-gift-card', 'GiftCardController@add');
    Route::get('card/edit-gift-card/{id}', 'GiftCardController@edit');
    Route::post('card/edit-gift-card', 'GiftCardController@edit');
    Route::get('/countries', 'GiftCardController@getCountries')->name('giftcard.list');
    Route::get('/giftcard', 'GiftCardController@allgiftcard');
    Route::get('/giftcarddetails/{id}', 'GiftCardController@giftcarddetails');
    Route::get('order-gift-card', 'GiftCardController@order_gift_card');
    Route::post('/submit/giftcard', 'GiftCardController@SubmitGiftCard')->name('submit.giftcard');
    
    // Dhiraagu
    Route::get('ooredoo-internet-bill-form','DhiraaguController@ooredoo_bill_form');
    Route::post('load-ooredoo-internet-bill','DhiraaguController@ooredoo_bill_load');
    Route::post('pay-ooredoo-internet-bill','DhiraaguController@ooredoo_bill_pay');
    Route::post('load-ooredoo-customer','DhiraaguController@ooredoo_customer');
    Route::post('pay-ooredoo-topup','DhiraaguController@pay_ooredoo_topup');
    Route::get('ooredoo-postpaid-form','DhiraaguController@ooredoo_postpaid_form');
    Route::post('load-ooredoo-customer-for-postpaid','DhiraaguController@find_ooredoo_postpaid_customer');
    Route::post('pay-ooredoo-postpaid','DhiraaguController@pay_ooredoo_postpaid');
    Route::get('dhiragu-internet-bill-form','DhiraaguController@dhiraagu_bill_form');
    Route::get('faseyha-internet-bill-form','DhiraaguController@internet_bill_form');
    Route::get('dhiragu-internet-bill','DhiraaguController@internet_bill');
    Route::post('load-internet-bill', 'DhiraaguController@find_intenet_bill');
    Route::post('pay-internet-bill', 'DhiraaguController@pay_internet_bill');
    Route::get('load-dhiraagu-bill', 'DhiraaguController@pending_bill');
    Route::get('all-dhiragu-bill','DhiraaguController@index'); 
    Route::get('all-dhiragu-topup','DhiraaguController@topup');
    Route::get('topup-list','DhiraaguController@topup_list');
    Route::get('edit-topup-list/{id}','DhiraaguController@topup_list_edit');
    Route::get('add-dhiragu-add-prepaid','DhiraaguController@add_prepaid'); 
    Route::post('pay-dhiraagu-reload','DhiraaguController@pay_prepaid'); 
    Route::post('dhiraagu-cashin','DhiraaguController@dhiraagu_cashin');
    Route::post('dhiraagu-payment','DhiraaguController@dhiraagu_payment');
    Route::post('dhiraagu-pay-postpaid','DhiraaguController@dhiraagu_postpaid');
    Route::post('dhiraagu-pay-internet','DhiraaguController@dhiraagu_internet_pay');
    Route::post('dhiraagu-pay-cable-tv','DhiraaguController@dhiraagu_pay_cable_tv');
    Route::get('add-dhiragu-bill','DhiraaguController@add_dhiragu_bill');    
    Route::get('dhiragu-all-reload','DhiraaguController@dhiragu_all_reload');
    Route::get('new-reload','DhiraaguController@new_reload'); 
    Route::get('ooredoo-reload','DhiraaguController@ooredoo_reload'); 
    Route::get('all-cashin','DhiraaguController@allcashin');
    Route::get('new-cashin','DhiraaguController@newcashin');
    Route::get('all-payments','DhiraaguController@all_payments');
    Route::get('new-payments','DhiraaguController@new_payments');
    Route::get('get-customer-id','DhiraaguController@getcustomerid');
    Route::get('all-mwsc-list','DhiraaguController@mwsclist');
    
    // Prepaid Card
    Route::get('card/user-card', 'PrepaidCardController@user_card');
    Route::get('card/user-carddetail/{id}', 'PrepaidCardController@user_cardedit');
    Route::get('card/user-transactions/{id}', 'PrepaidCardController@user_transactions');
    Route::get('card/user-list', 'PrepaidCardController@user_list');
    Route::get('card/funding-account', 'PrepaidCardController@funding_accounts');
    Route::get('card/all-user-transactions', 'PrepaidCardController@funding_accounts');
    Route::get('card-fees', 'PrepaidCardController@card_fees');
    Route::get('card-update', 'PrepaidCardController@card_update');
    Route::get('card/transactions', 'PrepaidCardController@card_transactions');
    Route::get('card/card-limit', 'PrepaidCardController@card_limit');
    Route::post('card/card-transactions', 'PrepaidCardController@cardtransactions');
    Route::get('card/vuser-details/{id}', 'PrepaidCardController@vcard_details');
    Route::post('card/vuser-update/{id}', 'PrepaidCardController@vcard_update');
    Route::get('card/prepaid-card', 'PrepaidCardController@index');
    Route::get('card/add-prepaid-card', 'PrepaidCardController@add');
    Route::post('card/add-prepaid-card', 'PrepaidCardController@add');
    Route::get('card/edit-prepaid-cardl/{id}', 'PrepaidCardController@edit');
    Route::post('card/edit-prepaid-card', 'PrepaidCardController@edit');
    
    // Dashboard
    Route::get('home', 'DashboardController@index')->name('dashboard');
    Route::post('change-lang', 'DashboardController@switchLanguage');
    Route::get('crypto/preference-disabled', 'DashboardController@adminCryptoPreferenceDisabled');
    
    // Admin    
    Route::get('admins', 'AdminController@index');
    Route::get('add-admin', 'AdminController@add');
    Route::post('store-admin', 'AdminController@store');
    Route::get('edit-admin/{id}', 'AdminController@edit');
    Route::post('update-admin/{id}', 'AdminController@update');
    Route::get('delete-admin/{id}', 'AdminController@delete');    
    
    // Reports
    Route::get('report', 'ReportController@index');
    Route::get('store/report', 'ReportController@store_report');
    Route::get('report/detail/{id}', 'ReportController@edit');
    Route::get('report/csv', 'ReportController@reportCsv');
    Route::get('report/pdf', 'ReportController@reportPdf');
    
    // Users
    Route::get('users/moneytransfer/{id}', 'UserController@transfercreate');
    Route::post('users/moneytransfer/{id}', 'UserController@transfercreate');
    Route::get('users/check-processed-by', 'UserController@checkProcessedBy');
    Route::post('users/transfer-user-email-phone-receiver-status-validate', 'UserController@transferUserEmailPhoneReceiverStatusValidate');
    Route::post('users/amount-limit', 'UserController@amountLimitCheck');
    Route::get('users/send-money-confirm/{id}', 'UserController@sendMoneyConfirm');
    Route::get('users/moneytransfer/print/{id}', 'UserController@transferPrintPdf');
    Route::get('users/photoproof/{id}', 'UserController@photoproof');
    Route::get('users/addressproof/{id}', 'UserController@addressproof');
    Route::get('users/idproof/{id}', 'UserController@idproof');
    Route::get('users/bankdetails/{id}', 'UserController@bankdetails');
    Route::get('users/biller/{id}', 'UserController@allstorebill');
    Route::get('users/make-trust/{id}', 'UserController@make_trust');
    Route::get('users/make-user/{id}', 'UserController@make_user');
    Route::post('user/update_biller', 'UserController@updatebiller');
    Route::get('set-limit', 'UserController@set_limit')->middleware(['permission:view_user']);
    Route::post('store-kyc-limit', 'UserController@store_kyc_limit');
    Route::post('store-with-kyc-limit','UserController@store_with_kyc_limit');
    Route::get('users', 'UserController@index')->middleware(['permission:view_user']);
    Route::get('merchant-list', 'UserController@MerchantList')->middleware(['permission:view_merchant']);
    Route::get('users/create', 'UserController@create')->middleware(['permission:add_user']);
    Route::post('users/store', 'UserController@store');
    Route::get('users/view/{id}', 'UserController@show');
    Route::get('users/edit/{id}', 'UserController@edit');
    Route::get('users/card/kyc/{id}', 'UserController@cardKyc');
    Route::get('users/address_edit/{id}', 'UserController@address_edit')->middleware(['permission:edit_user']);
    Route::post('users/address_update', 'UserController@address_update');
    Route::post('users/update', 'UserController@update');
    Route::get('users/delete/{id}', 'UserController@destroy')->middleware(['permission:delete_role']);
    Route::post('email_check', 'UserController@postEmailCheck');
    Route::post('duplicate-email-check', 'UserController@duplicateEmailCheck');
    Route::post('duplicate-user-check', 'UserController@checkDuplicateUser');
    Route::post('duplicate-phone-number-check', 'UserController@duplicatePhoneNumberCheck');
    Route::post('duplicate-phone-number-check1', 'UserController@duplicatePhoneNumberCheck1');
    Route::get('users/transactions/{id}', 'UserController@eachUserTransaction');
    Route::get('users/delete-bill/{id}', 'UserController@deleteBill');
    Route::match(array('GET', 'POST'), 'users/deposit/create/{id}', 'UserController@eachUserDeposit');
    Route::post('users/deposit/amount-fees-limit-check', 'UserController@amountFeesLimitCheck');
    Route::post('users/deposit/storeFromAdmin', 'UserController@eachUserDepositSuccess');
    Route::get('users/deposit/print/{id}', 'UserController@eachUserdepositPrintPdf');
    Route::match(array('GET', 'POST'), 'users/withdraw/create/{id}', 'UserController@eachUserWithdraw');
    Route::post('users/withdraw/amount-fees-limit-check', 'UserController@amountFeesLimitCheck');
    Route::post('users/withdraw/storeFromAdmin', 'UserController@eachUserWithdrawSuccess');
    Route::get('users/withdraw/print/{id}', 'UserController@eachUserWithdrawPrintPdf');
    Route::get('users/wallets/{id}', 'UserController@eachUserWallet');
    Route::get('users/tickets/{id}', 'UserController@eachUserTicket');
    Route::get('users/disputes/{id}', 'UserController@eachUserDispute');
    Route::get('approve-device/{id}', 'UserController@approve_device');
    Route::get('unlink-device', 'UserController@unlink_device');
    Route::get('users/activity-logs/{id}', 'UserController@activity_logs');
    
    Route::get('users/kyc-verications/{id}', 'VerfificationController@index');
    Route::post('users/kyc-verications/update', 'VerfificationController@update');
    
    // GVC Cards
    Route::get('partner', 'CardController@partner');
    Route::post('partner/update', 'CardController@partner_update');
    Route::get('card/requests', 'CardController@card_requests');
    Route::get('all/cards', 'CardController@cards');
    Route::get('card/reloads', 'CardController@reloads');
    Route::get('card/reload/details/{id}', 'CardController@reload_details');
    Route::get('card/transfers', 'CardController@transfers');
    Route::get('card/transfer/details/{id}', 'CardController@transfer_details');
    Route::get('card/subscriptions', 'CardController@subscriptions');
    Route::get('card/subscription/details/{id}', 'CardController@subscription_details');
    Route::get('card/fees', 'CardController@fees');
    Route::post('card/fees/update', 'CardController@fees_update');
    
    // Stores
    Route::get('store-list', 'StoreController@index');
    Route::get('store/edit/{id}', 'StoreController@edit');
    Route::post('store/update', 'StoreController@update');
    Route::get('store/delete/{id}', 'StoreController@destroy');
    Route::get('store/category/list/{id}', 'StoreController@category');
    Route::get('store/category/create/{id}', 'StoreController@categorycreate');
    Route::post('store/category/store/{id}', 'StoreController@categorystore');
    Route::get('store/category/edit/{id}/{cat_id}', 'StoreController@categoryedit');
    Route::post('store/category/update', 'StoreController@categoryupdate');
    Route::get('store/category/delete/{id}/{cat_id}', 'StoreController@categorydestroy');
    Route::get('store/product/list/{id}', 'StoreController@product');
    Route::get('store/product/create/{id}', 'StoreController@productcreate');
    Route::post('store/product/store/{id}', 'StoreController@productstore');
    Route::get('store/product/edit/{id}/{prod_id}', 'StoreController@productedit');
    Route::post('store/product/update', 'StoreController@productupdate');
    Route::get('store/product/delete/{id}/{prod_id}', 'StoreController@productdestroy');
    Route::get('store/orders/list/{id}', 'StoreController@orders');
    Route::get('store/orders/edit/{id}/{ord_id}', 'StoreController@ordersedit');
    Route::get('store/orders/invoice/{id}/{ord_id}', 'StoreController@ordersinvoice');
    
    // Merchants
    Route::get('merchants/upgrade-request', 'MerchantController@upgradeRequest');
    Route::get('merchants', 'MerchantController@index')->middleware(['permission:view_merchant']);
    Route::get('merchant/edit/{id}', 'MerchantController@edit')->middleware(['permission:edit_merchant']);
    Route::get('merchant/upgrade-package/{id}', 'MerchantController@upgrade')->middleware(['permission:edit_merchant']);
    Route::post('merchant/upgrade-package-update', 'MerchantController@upgradeUpdate');
    Route::post('merchant/update', 'MerchantController@update');
    Route::post('merchant/logo_delete', 'MerchantController@deleteLogo');
    Route::post('merchant/delete-merchant-logo', 'MerchantController@deleteMerchantLogo');
    Route::get('merchant/payments/{id}', 'MerchantController@eachMerchantPayment');
    Route::get('merchants/userSearch', 'MerchantController@merchantsUserSearch');
    Route::get('merchants/csv', 'MerchantController@merchantCsv');
    Route::get('merchants/pdf', 'MerchantController@merchantPdf');
    Route::post('merchants/change-fee-with-group-change', 'MerchantController@changeMerchantFeeWithGroupChange');

    //Merchant Payments
    Route::get('merchant_payments', 'MerchantPaymentController@index')->middleware(['permission:view_merchant_payment']);
    Route::get('merchant_payments/edit/{id}', 'MerchantPaymentController@edit')->middleware(['permission:edit_merchant_payment']);
    Route::post('merchant_payments/update', 'MerchantPaymentController@update');
    Route::get('merchant_payments/csv', 'MerchantPaymentController@merchantPaymentCsv');
    Route::get('merchant_payments/pdf', 'MerchantPaymentController@merchantPaymentPdf');

    // Transactions
    Route::get('transactions', 'TransactionController@index')->middleware(['permission:view_transaction']);
    Route::get('transactions/edit/{id}', 'TransactionController@edit')->middleware(['permission:edit_transaction']);
    Route::post('transactions/update/{id}', 'TransactionController@update');
    Route::get('transactions_user_search', 'TransactionController@transactionsUserSearch');
    Route::get('transactions/csv', 'TransactionController@transactionCsv');
    Route::get('transactions/pdf', 'TransactionController@transactionPdf');
    
    // Promotions
    Route::get('promotions', 'PromotionController@index');
    Route::get('promotions/create', 'PromotionController@create');
    Route::post('promotions/store', 'PromotionController@store');
    Route::get('promotions/edit/{id}', 'PromotionController@edit');
    Route::post('promotions/update/{id}', 'PromotionController@update');
    Route::get('promotions_user_search', 'PromotionController@transactionsUserSearch');
    Route::get('promotions/csv', 'PromotionController@transactionCsv');
    Route::get('promotions/pdf', 'PromotionController@transactionPdf');

    // Deposits
    Route::get('deposits', 'DepositController@index')->middleware(['permission:view_deposit']);
    Route::get('deposits/edit/{id}', 'DepositController@edit')->middleware(['permission:edit_deposit']);
    Route::post('deposits/update', 'DepositController@update');
    Route::get('deposits/user_search', 'DepositController@depositsUserSearch');
    Route::get('deposits/csv', 'DepositController@depositCsv');
    Route::get('deposits/pdf', 'DepositController@depositPdf');

    // Withdrawals
    Route::get('withdrawals', 'WithdrawalController@index');
    Route::get('withdrawals/edit/{id}', 'WithdrawalController@edit');
    Route::post('withdrawals/update', 'WithdrawalController@update');
    Route::get('withdrawals/user_search', 'WithdrawalController@withdrawalsUserSearch');
    Route::get('withdrawals/csv', 'WithdrawalController@withdrawalCsv');
    Route::get('withdrawals/pdf', 'WithdrawalController@withdrawalPdf');

    // Transfers
    Route::get('transfers', 'MoneyTransferController@index')->middleware(['permission:view_transfer']);
    Route::get('transfers/edit/{id}', 'MoneyTransferController@edit')->middleware(['permission:edit_transfer']);
    Route::post('transfers/update', 'MoneyTransferController@update');
    Route::get('transfers/user_search', 'MoneyTransferController@transfersUserSearch');
    Route::get('transfers/csv', 'MoneyTransferController@transferCsv');
    Route::get('transfers/pdf', 'MoneyTransferController@transferPdf');

    // Currency Exchanges
    Route::get('exchanges', 'ExchangeController@index')->middleware(['permission:view_exchange']);
    Route::get('exchange/edit/{id}', 'ExchangeController@edit')->middleware(['permission:edit_exchange']);
    Route::post('exchange/update', 'ExchangeController@update');
    Route::get('exchanges/user_search', 'ExchangeController@exchangesUserSearch');
    Route::get('exchanges/csv', 'ExchangeController@exchangeCsv');
    Route::get('exchanges/pdf', 'ExchangeController@exchangePdf');

    // Request Payments
    Route::get('request_payments', 'RequestPaymentController@index')->middleware(['permission:view_request_payment']);
    Route::get('request_payments/edit/{id}', 'RequestPaymentController@edit')->middleware(['permission:edit_request_payment']);
    Route::post('request_payments/update', 'RequestPaymentController@update');
    Route::get('request_payments/user_search', 'RequestPaymentController@requestpaymentsUserSearch');
    Route::get('request_payments/csv', 'RequestPaymentController@requestpaymentCsv');
    Route::get('request_payments/pdf', 'RequestPaymentController@requestpaymentPdf');
    
    // MPOS
    Route::get('mpos', 'MposController@index');
    Route::get('mpos/payouts', 'MposController@mpos_payouts');
    Route::get('mpos/edit/{id}', 'MposController@edit');
    Route::post('mpos/update/{id}', 'MposController@update');
    Route::get('mpos_user_search', 'MposController@mposUserSearch');
    Route::get('mpos/csv', 'MposController@mposCsv');
    Route::get('mpos/pdf', 'MposController@mposPdf');

    // Revenues
    Route::get('revenues', 'RevenueController@revenues_list')->middleware(['permission:view_revenue']);
    Route::get('store/revenues', 'RevenueController@store_revenues_list');
    Route::get('revenues/user_search', 'RevenueController@revenuesUserSearch');
    Route::get('revenues/csv', 'RevenueController@revenueCsv');
    Route::get('revenues/pdf', 'RevenueController@revenuePdf');

    //VIRTUAL CARDS
    Route::get('/virtual_cards', 'AdminVirtualCardController@virtualCards')->name('admin.virtual_cards');
    Route::get('/virtual_cards_transactions', 'AdminVirtualCardController@virtualtransactions')->name('admin.virtual_cards_transactions');
    Route::post('pause_virtual_card','AdminVirtualCardController@pausedVirtualCard')->name('admin.pause_virtual_card');
    Route::post('close_virtual_card','AdminVirtualCardController@closeVirtualCard')->name('admin.close_virtual_card');
    Route::post('open_virtual_card','AdminVirtualCardController@openVirtualCard')->name('admin.open_virtual_card');
    Route::get('delete_virtual_card/{id}','AdminVirtualCardController@deleteVirtualCard')->name('admin.delete_virtual_card');
    Route::get('list_virtual_card','AdminVirtualCardController@getVirtualCardsList')->name('admin.delete_virtual_card');
    Route::get('virtualtransactions/{id}', 'AdminVirtualCardController@virtualtransactions')->name('admin.virtualtransactions');
    Route::post('edit_virtual_card','AdminVirtualCardController@editVirtualCard')->name('admin.edit_virtual_card');
    
    //App Labels
    Route::get('labels', 'LabelController@index')->name('labels');
    Route::get('labels/create', 'LabelController@create')->name('labels.create');
    Route::post('labels/store', 'LabelController@store')->name('labels.store');
    Route::get('labels/edit/{id}', 'LabelController@edit')->name('labels.edit');
    Route::post('labels/update/{id}', 'LabelController@update')->name('labels.update');
    
    // disputes
    Route::get('disputes', 'DisputeController@index')->middleware(['permission:view_disputes']);
    Route::get('dispute/add/{id}', 'DisputeController@add');
    Route::post('dispute/open', 'DisputeController@store');
    Route::get('dispute/discussion/{id}', 'DisputeController@discussion')->middleware(['permission:edit_dispute']);
    Route::post('dispute/reply', 'DisputeController@storeReply');
    Route::post('dispute/change_reply_status', 'DisputeController@changeReplyStatus');
    Route::get('disputes_user_search', 'DisputeController@disputesUserSearch');
    Route::get('disputes_transactions_search', 'DisputeController@disputesTransactionsSearch');

    // Tickets
    Route::get('tickets/list', 'TicketController@index')->middleware(['permission:view_tickets']);
    Route::get('tickets/add', 'TicketController@create')->middleware(['permission:add_ticket']);
    Route::post('tickets/store', 'TicketController@store');
    Route::get('ticket_user_search', 'TicketController@ticketUserSearch');
    Route::get('tickets/reply/{id}', 'TicketController@reply')->middleware(['permission:edit_ticket']);
    Route::post('tickets/change_ticket_status', 'TicketController@change_ticket_status');
    Route::post('tickets/reply/store', 'TicketController@adminTicketReply');
    Route::post('tickets/reply/update', 'TicketController@replyUpdate');
    Route::post('tickets/reply/delete', 'TicketController@replyDelete');
    Route::get('tickets/edit/{id}', 'TicketController@edit')->middleware(['permission:edit_ticket']);
    Route::post('tickets/update', 'TicketController@update');
    Route::get('tickets/delete/{id}', 'TicketController@delete')->middleware(['permission:delete_ticket']);

    // Email Templates
    Route::get('template/{id}', 'EmailTemplateController@index')->middleware(['permission:view_email_template']);
    Route::post('template_update/{id}', 'EmailTemplateController@update')->middleware(['permission:edit_email_template']);
    
    // Notification Templates
    Route::get('notification/template/{id}', 'NotificationTemplateController@index');
    Route::post('notification/template_update/{id}', 'NotificationTemplateController@update');
    
    // Sms Templates
    Route::get('sms-template/{id}', 'SmsTemplateController@index')->middleware(['permission:view_sms_template']);
    Route::post('sms-template/update/{id}', 'SmsTemplateController@update')->middleware(['permission:edit_sms_template']);

    // Activity Logs
    Route::get('activity_logs', 'ActivityLogController@activities_list')->middleware(['permission:view_activity_log']);
    Route::get('auto-kyc', 'ActivityLogController@UserKyc')->middleware(['permission:view_kyc']);
    Route::get('kyc/delete/{id}', 'ActivityLogController@UserKycDelete')->middleware(['permission:delete_kyc']);
    Route::get('kyc/view/{id}', 'ActivityLogController@UserKycView')->middleware(['permission:view_kyc']);

    // For Fraud Detection    
    Route::get('settings/fraud-detection/{tab}/{id}', 'FraudController@index');
    Route::post('settings/update_fraud_detection', 'FraudController@update');
    Route::get('fraud-reports', 'FraudController@fraud_report');
    Route::get('fraud/csv', 'FraudController@fraudCsv');
    Route::get('fraud/pdf', 'FraudController@fraudPdf');

    // Verifications - photo-proofs
    Route::get('manual-kyc', 'PhotoProofController@userskyc');
    Route::get('mpos-manual-kyc', 'PhotoProofController@mposmanualkyc');
    Route::get('manualkycstatus', 'PhotoProofController@manualkycstatus');
    Route::get('photo-proofs', 'PhotoProofController@index')->middleware(['permission:view_identity_verfication']);
    Route::get('photo-proofs/csv', 'PhotoProofController@photoProofsCsv');
    Route::get('photo-proofs/pdf', 'PhotoProofController@photoProofsPdf');
    Route::get('photo-proofs/edit/{id}', 'PhotoProofController@photoProofEdit')->middleware(['permission:edit_identity_verfication']);
    Route::post('photo-proofs/update', 'PhotoProofController@photoProofUpdate');

    // Verifications - identity-proofs
    Route::get('identity-proofs', 'IdentityProofController@index')->middleware(['permission:view_identity_verfication']);
    Route::get('identity-proofs/csv', 'IdentityProofController@identityProofsCsv');
    Route::get('identity-proofs/pdf', 'IdentityProofController@identityProofsPdf');
    Route::get('identity-proofs/edit/{id}', 'IdentityProofController@identityProofEdit')->middleware(['permission:edit_identity_verfication']);
    Route::post('identity-proofs/update', 'IdentityProofController@identityProofUpdate');

    // Verifications - address-proofs
    Route::get('address-proofs', 'AddressProofController@index')->middleware(['permission:view_address_verfication']);
    Route::get('address-proofs/csv', 'AddressProofController@addressProofsCsv');
    Route::get('address-proofs/pdf', 'AddressProofController@addressProofsPdf');
    Route::get('address-proofs/edit/{id}', 'AddressProofController@addressProofEdit')->middleware(['permission:edit_address_verfication']);
    Route::post('address-proofs/update', 'AddressProofController@addressProofUpdate');

    // currencies
    Route::get('settings/currency', 'CurrencyController@index')->middleware(['permission:view_currency']);
    Route::match(array('GET', 'POST'), 'settings/add_currency', 'CurrencyController@add')->middleware(['permission:add_currency']);
    Route::match(array('GET', 'POST'), 'settings/edit_currency/{id}', 'CurrencyController@update')->middleware(['permission:edit_currency']);
    Route::get('settings/delete_currency/{id}', 'CurrencyController@delete')->middleware(['permission:delete_currency']);
    Route::post('currency/image_delete', 'CurrencyController@deleteImage');
    Route::post('settings/currency/delete-currency-logo', 'CurrencyController@deleteCurrencyLogo');
    Route::get('settings/currency/get-active-blockio-crypto-currency-settings', 'CurrencyController@getActiveBlockIoCrytoCurrencySettings');

    // FeesLimit
    Route::get('settings/feeslimit/{tab}/{subs}/{id}', 'FeesLimitController@limitList')->middleware(['permission:edit_currency']);
    Route::post('settings/get-feeslimit-details', 'FeesLimitController@getFesslimitDetails')->name('settings.feesLimitDetails');
    Route::post('settings/feeslimit/update-deposit-limit', 'FeesLimitController@updateDepositLimit');
    Route::post('settings/get-specific-currency-details', 'FeesLimitController@getSpecificCurrencyDetails');

    //Currency PaymentMethod Settings
    Route::get('settings/payment-methods/{tab}/{id}', 'CurrencyPaymentMethodController@paymentMethodList')->middleware(['permission:edit_currency']);
    Route::post('settings/payment-methods/update-paymentMethod-Credentials', 'CurrencyPaymentMethodController@updatePaymentMethodCredentials');
    Route::post('settings/get-payment-methods-details', 'CurrencyPaymentMethodController@getPaymentMethodsDetails');
    Route::post('settings/get-payment-methods-specific-currency-details', 'CurrencyPaymentMethodController@getPaymentMethodsSpecificCurrencyDetails');

    //Bank
    Route::post('settings/payment-methods/add-bank', 'CurrencyPaymentMethodController@addBank');
    Route::post('settings/payment-methods/update-bank', 'CurrencyPaymentMethodController@updateBank');
    Route::post('settings/payment-methods/delete-bank', 'CurrencyPaymentMethodController@deleteBank');
    Route::post('settings/payment-methods/getCpmId', 'CurrencyPaymentMethodController@getCpmId');
    Route::post('settings/payment-methods/show-bank-details', 'CurrencyPaymentMethodController@showbankDetails');
    Route::post('settings/payment-methods/delete-bank-logo', 'CurrencyPaymentMethodController@deleteBankLogo');
    
    //App Pages
    Route::get('apppages', 'ApppageController@index');
    Route::get('apppages/add', 'ApppageController@add');
    Route::post('apppages/store', 'ApppageController@store');
    Route::get('apppages/edit/{id}', 'ApppageController@edit');
    Route::post('apppages/update/{id}', 'ApppageController@update');
    Route::get('apppages/delete/{id}', 'ApppageController@delete');

    //banner
    Route::get('banner', 'BannerController@index');
    Route::get('banner/add', 'BannerController@add');
    Route::post('banner/store', 'BannerController@store');
    Route::get('banner/edit/{id}', 'BannerController@edit');
    Route::post('banner/update', 'BannerController@update');
    Route::get('banner/delete/{id}', 'BannerController@delete');

    //settings
    Route::match(array('GET', 'POST'), 'settings/revenues', 'SettingController@revenues');
    Route::match(array('GET', 'POST'), 'settings', 'SettingController@general');
    Route::post('settings/update-sidebar-company-logo', 'SettingController@updateSideBarCompanyLogo');
    Route::post('settings/logo-delete', 'SettingController@deleteLogo');
    Route::post('settings/logo-delete', 'SettingController@deleteLogo');
    Route::post('settings/check-sms-settings', 'SettingController@checkSmsGatewaySettings');
    Route::post('settings/delete-logo', 'SettingController@deleteSettingLogo');
    Route::post('settings/delete-favicon', 'SettingController@deleteSettingFavicon');
    Route::match(array('GET', 'POST'), 'settings/appversions', 'SettingController@appversions');
    Route::get('settings/virtual-card', 'SettingController@virtual_card');
    Route::post('settings/update-virtual-card', 'SettingController@updateVirtualCard');
    Route::get('settings/fee', 'SettingController@mpos_fees');
    Route::post('settings/fee-update', 'SettingController@mpos_fees_update');
    Route::get('settings/nfc', 'SettingController@nfc_credntials');
    Route::post('settings/nfc-update', 'SettingController@nfc_credntials_update');
    Route::match(array('GET', 'POST'), 'settings/social_links', 'SettingController@social_links')->middleware(['permission:view_social_links']);
    Route::match(array('GET', 'POST'), 'settings/api_informations', 'SettingController@api_informations')->middleware(['permission:view_api_credentials']);
    Route::get('settings/key_informations', 'SettingController@key_informations');
    Route::post('settings/giftcard_informations', 'SettingController@giftcard_informations')->middleware(['permission:view_api_credentials']);
    Route::post('settings/persona_informations', 'SettingController@persona_informations')->middleware(['permission:view_api_credentials']);
    Route::post('settings/ding_informations', 'SettingController@ding_informations')->middleware(['permission:view_api_credentials']);
    Route::post('settings/plaid_informations', 'SettingController@plaid_informations')->middleware(['permission:view_api_credentials']);
    Route::match(array('GET', 'POST'), 'settings/services/{type}', 'SettingController@Services')->middleware(['permission:view_api_credentials']);
    Route::match(array('GET', 'POST'), 'settings/services_edit/{id}', 'SettingController@ServicesEdit')->middleware(['permission:view_api_credentials']);
    Route::match(array('GET', 'POST'), 'settings/services_delete/{id}', 'SettingController@ServicesDelete')->middleware(['permission:view_api_credentials']);
    Route::match(array('GET', 'POST'), 'settings/payment_methods', 'SettingController@payment_methods')->middleware(['permission:view_payment_methods']);
    Route::match(array('GET', 'POST'), 'settings/email', 'SettingController@email')->middleware(['permission:view_email_setting']);
    Route::match(array('GET', 'POST'), 'settings/sms/{type}', 'SettingController@sms')->middleware(['permission:view_sms_setting']);
    Route::get('settings/preference', 'SettingController@preference')->middleware(['permission:view_preference']);
    Route::post('save-preference', 'SettingController@savePreference')->middleware(['permission:edit_preference']);
    Route::get('settings/utility', 'SettingController@all_utility')->middleware(['permission:view_preference']);
    Route::post('settings/update_utility', 'SettingController@update_utility');
    Route::get('settings/error_code', 'SettingController@error_code')->middleware(['permission:view_preference']);
    Route::match(array('GET', 'POST'), 'settings/enable-woocommerce', 'SettingController@enableWoocommerce')->middleware(['permission:view_enable_woocommerce']);
    
    //appstore credentials
    Route::get('settings/app-store-credentials', 'AppStoreCredentialController@getAppStoreCredentials')->middleware(['permission:view_appstore_credentials']);
    Route::post('settings/app-store-credentials/update-google-credentials', 'AppStoreCredentialController@updateGoogleCredentials');
    Route::post('settings/app-store-credentials/update-apple-credentials', 'AppStoreCredentialController@updateAppleCredentials');
    Route::post('settings/app-store-credentials/delete-playstore-logo', 'AppStoreCredentialController@deletePlaystoreLogo');
    Route::post('settings/app-store-credentials/delete-appstore-logo', 'AppStoreCredentialController@deleteAppStoreLogo');

    //countries
    Route::get('settings/country', 'CountryController@index')->middleware(['permission:view_country']);
    Route::match(array('GET', 'POST'), 'settings/add_country', 'CountryController@add')->middleware(['permission:add_country']);
    Route::match(array('GET', 'POST'), 'settings/edit_country/{id}', 'CountryController@update')->middleware(['permission:edit_country']);
    Route::get('settings/delete_country/{id}', 'CountryController@delete')->middleware(['permission:delete_country']);
    Route::match(array('GET', 'POST'), 'settings/add_label/{id}', 'CountryController@add_label');
    Route::post('settings/edit_label/{id}', 'CountryController@edit_label');
    Route::get('settings/delete_label/{id}', 'CountryController@delete_label');
    Route::match(array('GET', 'POST'), 'settings/kyc_methods/{id}', 'CountryController@kyc_methods');
    Route::post('settings/edit_kyc_methods/{id}', 'CountryController@edit_kyc_methods');

    //languages
    Route::get('settings/language', 'LanguageController@index')->middleware(['permission:view_language']);
    Route::match(array('GET', 'POST'), 'settings/add_language', 'LanguageController@add')->middleware(['permission:add_language']);
    Route::match(array('GET', 'POST'), 'settings/edit_language/{id}', 'LanguageController@update')->middleware(['permission:edit_language']);
    Route::get('settings/delete_language/{id}', 'LanguageController@delete')->middleware(['permission:delete_language']);
    Route::post('settings/language/delete-flag', 'LanguageController@deleteFlag');

    //Merchant Group/Roles
    Route::get('settings/merchant-group', 'MerchantGroupController@index')->middleware(['permission:view_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/add-merchant-group', 'MerchantGroupController@add')->middleware(['permission:add_merchant_group']);
    Route::get('settings/merchant-document', 'MerchantGroupController@index_document')->middleware(['permission:view_merchant_group']);
    Route::get('settings/merchant-document/delete/{id}', 'MerchantGroupController@delete_document')->middleware(['permission:view_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/add-merchant-group-document', 'MerchantGroupController@addDocument')->middleware(['permission:add_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/edit-merchant-group-document', 'MerchantGroupController@update_document')->middleware(['permission:add_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/edit-merchant-document/{id}', 'MerchantGroupController@update_document')->middleware(['permission:edit_merchant_group']);
    Route::match(array('GET', 'POST'), 'settings/edit-merchant-group/{id}', 'MerchantGroupController@update')->middleware(['permission:edit_merchant_group']);
    Route::get('settings/delete-merchant-group/{id}', 'MerchantGroupController@delete')->middleware(['permission:delete_merchant_group']);

    //User Group/Roles
    Route::get('settings/user_role', 'UsersRoleController@index')->middleware(['permission:view_group']);
    Route::match(array('GET', 'POST'), 'settings/add_user_role', 'UsersRoleController@add')->middleware(['permission:add_group']);
    Route::match(array('GET', 'POST'), 'settings/edit_user_role/{id}', 'UsersRoleController@update')->middleware(['permission:edit_group']);
    Route::get('settings/delete_user_role/{id}', 'UsersRoleController@delete')->middleware(['permission:delete_group']);
    Route::get('settings/roles/check-user-permissions', 'UsersRoleController@checkUserPermissions');

    //Admin Group/Roles
    Route::get('settings/roles', 'RoleController@index')->middleware(['permission:view_role']);
    Route::match(array('GET', 'POST'), 'settings/add_role', 'RoleController@add')->middleware(['permission:add_role']);
    Route::match(array('GET', 'POST'), 'settings/edit_role/{id}', 'RoleController@update')->middleware(['permission:edit_role']);
    Route::get('settings/delete_role/{id}', 'RoleController@delete')->middleware(['permission:delete_role']);
    Route::post('settings/roles/duplicate-role-check', 'RoleController@duplicateRoleCheck');

    //Database Backup
    Route::get('settings/backup', 'BackupController@index')->middleware(['permission:view_database_backup']);
    Route::get('backup/save', 'BackupController@add')->middleware(['permission:add_database_backup']);
    Route::get('backup/download/{id}', 'BackupController@download')->middleware(['permission:edit_database_backup']);

    //metas
    Route::get('settings/metas', 'MetaController@index')->middleware(['permission:view_meta']);
    Route::match(array('GET', 'POST'), 'settings/edit_meta/{id}', 'MetaController@update')->middleware(['permission:edit_meta']);

    //Pages
    Route::get('settings/pages', 'PagesController@index')->middleware(['permission:view_page']);
    Route::get('settings/page/add', 'PagesController@add')->middleware(['permission:add_page']);
    Route::post('settings/page/store', 'PagesController@store');
    Route::get('settings/page/edit/{id}/{language}', ['uses' => 'PagesController@edit', 'as' => 'admin.page.edit'])->middleware(['permission:edit_page']);
    Route::post('settings/page/update', 'PagesController@update');
    Route::get('settings/page/delete/{id}', 'PagesController@delete')->middleware(['permission:delete_page']);

    //Notifications
    Route::get('settings/notification-types', 'NotificationTypeController@index');
    Route::get('settings/notification-types/edit/{id}', 'NotificationTypeController@edit');
    Route::post('settings/notification-types/update/{id}', 'NotificationTypeController@update');
    Route::post('settings/notification-type-name/check', 'NotificationTypeController@uniqueNotificationTypeNameCheck');
    Route::get('settings/notification-settings/{type}', 'NotificationSettingController@index');
    Route::post('settings/notification-settings/update', 'NotificationSettingController@update');
    
    // Maintainance
    Route::get('maintainance-settings', 'MaintainanceController@index');
    Route::get('add-maintainance-settings', 'MaintainanceController@add');
    Route::post('store-maintainance-settings', 'MaintainanceController@store');
    Route::get('edit-maintainance-settings/{id}', 'MaintainanceController@edit');
    Route::post('update-maintainance-settings/{id}', 'MaintainanceController@update');
    Route::get('delete-maintainance-settings/{id}', 'MaintainanceController@delete');
    Route::get('remind-maintainance-settings/{id}', 'MaintainanceController@remind');
    Route::get('remind-maintainance-settings-sms/{id}', 'MaintainanceController@remind_sms');
    Route::post('remind-maintainance-settings-sms-send/{id}', 'MaintainanceController@remind_sms_send');
    
    // Survey
    Route::get('survey', 'SurveyController@index');
    Route::get('add-survey', 'SurveyController@add');
    Route::post('store-survey', 'SurveyController@store');
    Route::get('edit-survey/{id}', 'SurveyController@edit');
    Route::post('update-survey/{id}', 'SurveyController@update');
    Route::get('delete-survey/{id}', 'SurveyController@delete');
    Route::get('remind-survey/{id}', 'SurveyController@remind');

    // Subscription
    Route::get('subscriptions', 'SubscriptionController@index');
    Route::get('add-subscription', 'SubscriptionController@add');
    Route::post('store-subscription', 'SubscriptionController@store');
    Route::get('edit-subscription/{id}', 'SubscriptionController@edit');
    Route::post('update-subscription/{id}', 'SubscriptionController@update');
    Route::get('delete-subscription/{id}', 'SubscriptionController@delete');

    // PaymentMethod
    Route::get('paymentmethods', 'PaymentMethodController@index');
    Route::get('add-paymentmethod', 'PaymentMethodController@add');
    Route::post('store-paymentmethod', 'PaymentMethodController@store');
    Route::get('edit-paymentmethod/{id}', 'PaymentMethodController@edit');
    Route::post('update-paymentmethod/{id}', 'PaymentMethodController@update');
    Route::get('delete-paymentmethod/{id}', 'PaymentMethodController@delete');

    // TransactionType
    Route::get('transactiontypes', 'TransactionTypeController@index');
    Route::get('add-transactiontype', 'TransactionTypeController@add');
    Route::post('store-transactiontype', 'TransactionTypeController@store');
    Route::get('edit-transactiontype/{id}', 'TransactionTypeController@edit');
    Route::post('update-transactiontype/{id}', 'TransactionTypeController@update');
    Route::get('delete-transactiontype/{id}', 'TransactionTypeController@delete');

    // AmbassadorCodes
    Route::get('ambassador-codes', 'AmbassadorCodeController@index');
    Route::get('add-ambassador-code', 'AmbassadorCodeController@add');
    Route::post('store-ambassador-code', 'AmbassadorCodeController@store');
    Route::get('edit-ambassador-code/{id}', 'AmbassadorCodeController@edit');
    Route::post('update-ambassador-code/{id}', 'AmbassadorCodeController@update');
    Route::get('delete-ambassador-code/{id}', 'AmbassadorCodeController@delete');

    // Check enabled currencies in preference
    Route::group(['middleware' => ['check-enabled-currencies-preference']], function ()
    {
        //Admin Crypto Send
        Route::match(array('GET', 'POST'), 'users/crypto/send/{id}', 'UserController@eachUserCryptoSend');
        Route::get('users/deposit/crypto/send/get-merchant-user-network-address-with-merchant-balance', 'UserController@getMerchantUserNetworkAddressWithMerchantBalance');
        Route::get('users/deposit/crypto/validate-merchant-address-balance', 'UserController@validateMerchantAddressBalanceAgainstAmount');
        Route::post('users/deposit/crypto/send/success', 'UserController@eachUserCryptoSendSuccess');
        Route::get('users/deposit/crypto/send-receive/print/{id}', 'UserController@merchantCryptoSentReceivedTransactionPrintPdf');

        //Admin Crypto Receive
        Route::match(array('GET', 'POST'), 'users/crypto/receive/{id}', 'UserController@eachUserCryptoReceive');
        Route::get('users/deposit/crypto/receive/get-user-network-address-balance-with-merchant-address', 'UserController@getUserNetworkAddressBalanceWithMerchantNetworkAddress');
        Route::get('users/deposit/crypto/receive/validate-user-address-balance', 'UserController@validateUserAddressBalanceAgainstAmount');
        Route::post('users/deposit/crypto/receive/success', 'UserController@eachUserCryptoReceiveSuccess');

        // Crypto Send Transactions
        Route::get('crypto-sent-transactions', 'CryptoSentTransactionController@index')->middleware(['permission:view_crypto_transactions']);
        Route::get('crypto-sent-transactions/csv', 'CryptoSentTransactionController@cryptoSentTransactionsCsv');
        Route::get('crypto-sent-transactions/pdf', 'CryptoSentTransactionController@cryptoSentTransactionsPdf');
        Route::get('crypto-sent-transactions/view/{id}', 'CryptoSentTransactionController@view')->middleware(['permission:view_crypto_transactions']);
        Route::get('crypto-sent-transactions/search/user', 'CryptoSentTransactionController@cryptoSentTransactionsSearchUser');

        // Crypto Receive Transactions
        Route::get('crypto-received-transactions', 'CryptoReceivedTransactionController@index')->middleware(['permission:view_crypto_transactions']);
        Route::get('crypto-received-transactions/csv', 'CryptoReceivedTransactionController@cryptoReceivedTransactionsCsv');
        Route::get('crypto-received-transactions/pdf', 'CryptoReceivedTransactionController@cryptoReceivedTransactionsPdf');
        Route::get('crypto-received-transactions/view/{id}', 'CryptoReceivedTransactionController@view')->middleware(['permission:view_crypto_transactions']);
        Route::get('crypto-received-transactions/search/user', 'CryptoReceivedTransactionController@cryptoReceivedTransactionsSearchUser');

        //BlockIO Settings
        Route::post('settings/crypto-currencies-settings/update', 'CryptoCurrenciesSettingController@updateBlockIoSettings')->middleware(['permission:edit_blockio_settings']);
        Route::get('settings/crypto-currencies-settings/check-merhant-network-address', 'CryptoCurrenciesSettingController@checkMerchantNetworkAddress');
        //this must be at the end - as optional parameter route will execute first if given above other routes
        Route::get('settings/crypto-currencies-settings/{type}', 'CryptoCurrenciesSettingController@viewBlockIoSettings')->middleware(['permission:view_blockio_settings']);
    });
    
    // Addon
    Route::match(array('GET', 'POST'), '/custom/addons', 'AddonController@index');
    Route::get('/custom/addon/activation/{status}/{id}', 'AddonController@activation');

    // ModuleManager
    Route::get('module-manager/addons', 'ModuleManagerController@index')->middleware(['permission:view_addon_manager']);

    // Crypto Providers
    Route::get('crypto-providers/{provider?}', 'CryptoProviderController@index')->name('admin.crypto_providers.list')->middleware(['permission:view_crypto_provider']);
    Route::post('crypto-provider/{provider}/status-change', 'CryptoProviderController@statusChange')->name('admin.crypto_providers.status_change')->middleware(['permission:edit_crypto_provider']);

    // System info
    Route::get('system-info', 'SystemInfoController@index')->name('systemInfo.index');
});