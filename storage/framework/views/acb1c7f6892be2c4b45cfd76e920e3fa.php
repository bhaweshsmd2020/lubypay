<?php
    $companyName = settings('name');
?>



<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('public/frontend/templates/css/prism.min.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Hero section -->
    <div class="standards-hero-section">
        <div class="px-240">
            <div class="d-flex flex-column align-items-start">
                <nav class="customize-bcrm">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>"><?php echo e(__('Home')); ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo e(__('Developer')); ?></li>
                    </ol>
                </nav>
                <div class="btn-section">
                    <button class="btn btn-dark btn-lg"><?php echo e(__('Developer')); ?></button>
                </div>
                <div class="merchant-text">
                    <p><?php echo e(__('With :x Standard and Express, you can easily and safely receive online payments from your customer.', ['x' => $companyName])); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Merchant tab -->
    <?php echo $__env->make('frontend.pages.merchant_tab', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


    <!--Paymoney code-snippet-section-->
    <div class="px-240 code-snippet-section">
        <div class="snippet-module">
            <div class="snippet-text">
                <div class="standard-title-text mb-28">
                    <h3><?php echo e($companyName); ?> <?php echo e(__('Express Payment Gateway Documentation.')); ?></h3>
                </div>
            <span><?php echo e(__('Payer')); ?></span>
                <p><?php echo e(__('If payer wants to fund payments using :x, set payer to :x.(Other payment method ex: paypal, stripe, coin payments etc not available yet).', ['x' => $companyName])); ?></p>
            </div>
            <div class="language-container">
                <div class="snippet">
                    <pre class="language-php thin-scrollbar">
                        <code>
                            //Payer Object 
                            $payer = new Payer(); 
                            $payer->setPaymentMethod('PayMoney'); //preferably, your system name, example - PayMoney
                        </code>
                    </pre>
                </div>
            </div>
        </div>
        <div class="snippet-module">
            <div class="snippet-text">
                <span><?php echo e(__('Amount')); ?></span>
                <p><?php echo e(__('Specify a payment amount and the currency.')); ?></p>
            </div>
            <div class="language-container">
                <div class="snippet line-numbers">
                    <pre class="language-php thin-scrollbar">
                        <code>
                            //Amount Object 
                            $amountIns = new Amount(); 
                            $amountIns->setTotal(20)->setCurrency('USD'); //must give a valid currency code and must exist in merchant wallet list 
                        </code>
                    </pre>
                </div>
            </div>
        </div>
		<div class="snippet-module">
            <div class="snippet-text">
                <span><?php echo e(__("Transaction")); ?></span>
                <p><?php echo e(__("It’s a Transaction resource where amount object has to set.")); ?></p>
            </div>
            <div class="language-container">
                <div class="snippet line-numbers">
                    <pre class="language-php thin-scrollbar">
                        <code>
                            //Transaction Object
                            $trans = new Transaction();
                            $trans->setAmount($amountIns);
                        </code>
                    </pre>
                </div>
            </div>
        </div>
		<div class="snippet-module">
            <div class="snippet-text">
            <span><?php echo e(__('RedirectUrls')); ?></span>
            <p><?php echo e(__('Set the urls where buyer should redirect after transaction is completed or cancelled.')); ?></p>
            </div>
            <div class="language-container">
                <div class="snippet line-numbers">
                    <pre class="language-php thin-scrollbar">
                        <code>
                            //RedirectUrls Object
                            $urls = new RedirectUrls();
                            $urls->setSuccessUrl('http://your-merchant-domain.com/example-success.php') //success url - the merchant domain page, to redirect after successful payment, see sample example-success.php file in  sdk root, example - http://techvill.net/PayMoney_sdk/example-success.php
                            ->setCancelUrl('http://your-merchant-domain.com/');//cancel url - the merchant domain page, to redirect after cancellation of payment, example - http://techvill.net/PayMoney_sdk/
                        </code>
                    </pre>
                </div>
            </div>
        </div>
        <div class="snippet-module mb-0">
            <div class="snippet-text">
                <span><?php echo e(__("Payment")); ?></span>
                <p><?php echo e(__("It’s a payment resource where all Payer, Amount, RedirectUrls and Credentials of merchant (Client ID and Client Secret) have to set. After initialized into payment object, need to call create method. It will generate a redirect URL. Users have to redirect into this URL to complete the transaction.")); ?></p>
            </div>
            <div class="language-container">
                <div class="snippet line-numbers">
                    <pre class="language-php thin-scrollbar">
                        <code>
                            //Payment Object
                            $payment = new Payment();
                            $payment->setCredentials([ //client id & client secret, see merchants->setting(gear icon)
                            'client_id' => 'place your client id here',  //must provide correct client id of an express merchant
                            'client_secret' => 'place your client secret here' //must provide correct client secret of an express merchant
                            ])->setRedirectUrls($urls)
                            ->setPayer($payer) 
                            ->setTransaction($trans);
                            
                            try {
                            $payment->create(); //create payment
                            header("Location: ".$payment->getApprovedUrl()); //checkout url
                            } catch (Exception $ex) { 
                            print $ex; 
                            exit; }
                        </code>
                    </pre>
                </div>
            </div>
        </div>
		<div class="snippet-module mb-0">
            <div class="snippet-text run-code">
                <div class="standard-title-text m-width mb-28">
                    <h3><?php echo e(__('A few steps on how to run this code on your device')); ?>:</h3>
                </div>
                <span><?php echo e(__('1st')); ?> :</span>
                <p><?php echo e(__('Click download for the package')); ?> </p>
                <div class="download-btn mt-12">
                    <a href="<?php echo e(url('download/package')); ?>" class="btn btn-sm btn-primary"><?php echo e(__('Download')); ?></a>
                </div>
            </div>
        </div>
		<div class="snippet-module">
            <div class="snippet-text run-code mt-1">
            <span><?php echo e(__('2nd')); ?> :</span>
            <p class="download-desc"><?php echo e(__('Now, go to')); ?> php-sdk/src/PayMoney/Rest/Connection.php, <?php echo e(__('then change')); ?> BASE_URL <?php echo e(__("value to your domain name(i.e: If the domain is - 'your-domain.com' then,")); ?> define( 'BASE_URL' , 'http://your-domain.com/' ) )</p>
            </div>
            <div class="language-container">
                <div class="snippet">
                    <pre class="language-php thin-scrollbar pt-76">
                        <div class="example">
                            <span class="left-example-text"><?php echo e(__('Example code')); ?></span>
                        </div>
                        <code>
                            require 'vendor/autoload.php';
                            //if you want to change the namespace/path from 'PayMoney' - lines[1-5] - 
                            //to your desired name, i.e. (use PayMoney\Api\Amount; 
                            //to use MyDomain\Api\Amount;), then you must change the folders name that holds the API classes 
                            //as well as change the property 'PayMoney' in (autoload->psr-0) of (php-sdk/composer.json) file to your 
                            //desired name and run "composer dump-autoload" command from sdk root
                            use PayMoney\Api\Payer; 
                            use PayMoney\Api\Amount; 
                            use PayMoney\Api\Transaction; 
                            use PayMoney\Api\RedirectUrls; 
                            use PayMoney\Api\Payment;
                            //Payer Object 
                            $payer = new Payer(); 
                            $payer->setPaymentMethod('PayMoney'); //preferably, your system name, example - PayMoney
                            //Amount Object 
                            $amountIns = new Amount(); 
                            $amountIns->setTotal(20)->setCurrency('USD'); //must give a valid currency code and must exist in merchant wallet list 
                            //Transaction Object
                            $trans = new Transaction();
                            $trans->setAmount($amountIns);
                        </code>
                    </pre>
                </div>
            </div>
        </div>
		<div class="snippet-module">
            <div class="snippet-text optional">
                <div class="standard-title-text">
                    <h3 class="mt-0"><?php echo e(__('Optional Instructions')); ?></h3>
                </div>
                <p><?php echo e(__('If you don\'t see changes after configuring and extracting SDK, go to your SDK root and run the commands below')); ?>:-</p>
            </div>
            <div class="option-container">
                <ul>
                    <li><?php echo e(__('Composer clear-cache')); ?></li>
                    <li><?php echo e(__('Composer install')); ?></li>
                    <li><?php echo e(__('Composer dump-autoload')); ?></li>
                </ul>
            </div>
        </div>    
   </div> 
<?php $__env->stopSection(); ?>

<?php $__env->startSection('js'); ?>
    <script src="<?php echo e(asset('public/frontend/templates/js/prism.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('frontend.layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/lubypay/public_html/accounts/resources/views/frontend/pages/express.blade.php ENDPATH**/ ?>