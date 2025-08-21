<?php

namespace App\Http\Controllers\Users;

use App\DataTables\Admin\InsuranceDataTable;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Common;
use App\Models\Insurance;
use Illuminate\Http\Request;




class GiftCardController extends Controller
{
    protected $helper;

    public function __construct()
    {
        $this->main_url       = "https://giftcards-sandbox.reloadly.com"; // Main BASE URL
        $this->helper = new Common();
    }
    
    
    public function generateToken() {
        $url = "https://auth.reloadly.com/oauth/token";

        $fields =json_encode(
                 array( 
                	"client_id"=>"pE32BAhIyRq9Y68R9ssePOBD2tBchOm3",
                	"client_secret"=>"MKnesw3daD-6zstIzVAwXmBZWIl15j-DLsL5Eot3MGr5DX5YOqXehjPocRuQuSp",
                	"grant_type"=>"client_credentials",
                	"audience"=>$this->main_url
                 )
             );

        $method = "POST";
        $data = $this->run_curl($url, $fields, $method, false, true);
        $this->my_token = $data->access_token;
    }
    
    /*********************Generate Token Function END***************************/
    /***********************RUN CURL Function START***************************/
    public function run_curl ($url, $fields, $method, $header = false, $auth = false) {
        $ch = curl_init();
        if($auth == false) {
        $url = $this->main_url . $url;
        }
        if($header == true) {
            
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Accept: application/com.giftcards.topups-v1+json","Authorization: Bearer ".$this->my_token)); // Live Token
        } else {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); // Live
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if($method=='POST')
        {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields); 
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
        
       return json_decode($result);
    }
    
     /***********************RUN CURL Function END***************************/
     
     /***********************GET Countries Function START***************************/
    public function getCountries(Request $request)
    {
        $this->generateToken();
        $url = "/countries";
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://giftcards-sandbox.reloadly.com/countries',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$this->my_token,
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        echo $response;
    }
    
    
    public function allgiftcard(Request $request)
    {
        $this->generateToken();
        $iso = $request->iso;
        $product = $request->product;
        $page = $request->page;
        $curl = curl_init();
        
        curl_setopt_array($curl, [
        	CURLOPT_URL => "https://giftcards-sandbox.reloadly.com/products?size=100&page=$page&productName=$product&countryCode=$iso&includeRange=true&includeFixed=true",
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_ENCODING => "",
        	CURLOPT_MAXREDIRS => 10,
        	CURLOPT_TIMEOUT => 30,
        	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	CURLOPT_CUSTOMREQUEST => "GET",
        	CURLOPT_HTTPHEADER => [
        		"Accept: application/com.reloadly.giftcards-v1+json",
        		"Authorization: Bearer ".$this->my_token ],
        ]);
        
        $result = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $response = json_decode($result);
        $contents = $response->content;
        // dd($response);
        $i = 0;
        foreach ($contents as $content){
            $product[$i]['productId']                           = $content->productId;
            $product[$i]['productName']                         = $content->productName;
            $product[$i]['global']                              = $content->global;
            $product[$i]['senderFee']                           = $content->senderFee;
            $product[$i]['senderFeePercentage']                 = $content->senderFeePercentage;
            $product[$i]['discountPercentage']                  = $content->discountPercentage;
            $product[$i]['denominationType']                    = $content->denominationType;
            $product[$i]['recipientCurrencyCode']               = $content->recipientCurrencyCode;
            $product[$i]['minRecipientDenomination']            = $content->minRecipientDenomination;
            $product[$i]['senderCurrencyCode']                  = $content->senderCurrencyCode;
            $product[$i]['minSenderDenomination']               = $content->minSenderDenomination;
            $product[$i]['maxSenderDenomination']               = $content->maxSenderDenomination;
            $product[$i]['fixedRecipientDenominations']         = $content->fixedRecipientDenominations;
            $product[$i]['fixedSenderDenominations']            = $content->fixedSenderDenominations;
            $product[$i]['logoUrls']                            = $content->logoUrls;
            // $product[$i]['brandName']                           = $content->brandName;
            $product[$i]['redeemInstruction']                   = $content->redeemInstruction;
            $product[$i]['maxSenderDenomination']               = $content->maxSenderDenomination;
             $i++;
        }
        
// dd($product);
        
        return view('user_dashboard.giftcard.giftcard', ['product' => $product], ['contents' => $contents], ['i' => $i]);
        // echo $response;
    }
    
    public function giftcarddetails(Request $request)
    {
        $this->generateToken();
        $id = $request->id;
        $curl = curl_init();
        
        curl_setopt_array($curl, [
        	CURLOPT_URL => "https://giftcards-sandbox.reloadly.com/products/$id",
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_ENCODING => "",
        	CURLOPT_MAXREDIRS => 10,
        	CURLOPT_TIMEOUT => 30,
        	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	CURLOPT_CUSTOMREQUEST => "GET",
        	CURLOPT_HTTPHEADER => [
        		"Accept: application/com.reloadly.giftcards-v1+json",
        		"Authorization: Bearer ".$this->my_token ,
        	],
        ]);
        
        $response = curl_exec($curl);
        
        $product_details = json_decode($response);
        // dd($product_details);
        $fixedRecipientDenominations=$product_details->fixedRecipientDenominations;
        $denominationType=$product_details->denominationType;
        $err = curl_error($curl);
        
        curl_close($curl);
        return view('user_dashboard.giftcard.gift-card-details', ['product_details' => $product_details,'fixedRecipientDenominations'=>$fixedRecipientDenominations,'denominationType'=>$denominationType]);

    }
    
    public function order_gift_card(Request $request)
    {
        
        
        $product_id = $request->product_id;
        $countryCode = $request->iso;
        $quantity = $request->quantity;
        $unitPrice = $request->unitPrice;
        $identifier = $request->identifier;
        $senderName = $request->senderName;
        $recipientEmail = $request->recipientEmail;
        
        $postfield = [
            'countryCode'=> $countryCode,
            'productId'=> $product_id,
            'customIdentifier'=> $identifier,
            'quantity'=> $quantity,
            'senderName' => $senderName,
            'recipientEmail'=>$recipientEmail,
            'unitPrice'=> $unitPrice,
            ];
        dd($postfield);
        $curl = curl_init();
        
        curl_setopt_array($curl, [
        	CURLOPT_URL => "https://giftcards-sandbox.reloadly.com/orders",
        	CURLOPT_RETURNTRANSFER => true,
        	CURLOPT_ENCODING => "",
        	CURLOPT_MAXREDIRS => 10,
        	CURLOPT_TIMEOUT => 30,
        	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        	CURLOPT_CUSTOMREQUEST => "POST",
        	CURLOPT_POSTFIELDS => $postfield,
        	CURLOPT_HTTPHEADER => [
        		"Accept: application/com.reloadly.giftcards-v1+json",
        		"Authorization: Bearer ".$this->my_token ,
        		"Content-Type: application/json"
        	],
        ]);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
        	echo "cURL Error #:" . $err;
        } else {
        	echo $response;
        }
    }
    
       




  
}