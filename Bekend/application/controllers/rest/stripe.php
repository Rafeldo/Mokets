<?php 
require_once(APPPATH.'/libraries/REST_Controller.php');
require_once(APPPATH.'/vendor/autoload.php');


class Stripe extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index_get()
	{
		echo "Stripe Payment";
	}
	
	function check_get() {
		\Stripe\Stripe::setApiKey($this->config->item('stripe_secret_key'));
		\Stripe\Stripe::$apiBase = "https://api-tls12.stripe.com";
		try {
		  \Stripe\Charge::all();
		  echo "TLS 1.2 supported, no action required.";
		} catch (\Stripe\Error\ApiConnection $e) {
		  echo "TLS 1.2 is not supported. You will need to upgrade your integration. https://support.stripe.com/questions/how-do-i-upgrade-my-stripe-integration-from-tls-1-0-to-tls-1-2";
		}
	}
	
	function submit_post() 
	{
		
		
		
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			// Get the credit card details submitted by the form
			$token = $_POST['stripeToken'];
			$amount = $_POST['amount'];
			$currency = $_POST['currency'];
			$shop_id = $_POST['shopId'];
			
			$shop = $this->shop->get_info($shop_id);
			
			// Create the charge on Stripe's servers - this will charge the user's card
			try {
				
				# set stripe test key
				\Stripe\Stripe::setApiKey($shop->stripe_secret_key);
				
				$charge = \Stripe\Charge::create(array(
			    	"amount" => $amount * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
			    	"currency" => "usd",
			    	"source" => $token,
			    	"description" => "Mokets Order From iOS"
			    ));
			    
			    
			    //echo "ok"; die;
			} catch(\Stripe\Error\Card $e) {
			  
			    
			}
		} else {
			echo "Get Method";
		}
	}
	
	function android_submit_post() 
	{
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			
			$data = $this->post();
			
			$token 		= $data['stripeToken'];
			$amount 	= $data['amount'];
			$currency 	= $data['currency'];
			$shop_id 	= $data['shopId'];
			
			$shop = $this->shop->get_info($shop_id);
			
			try {
				
				# set stripe test key
				\Stripe\Stripe::setApiKey($shop->stripe_secret_key);
				
				$charge = \Stripe\Charge::create(array(
			    	"amount" => $amount * 100, // amount in cents, so need to multiply with 100 .. $amount * 100
			    	"currency" => $currency,
			    	"source" => $token,
			    	"description" => "Mokets Order From Android"
			    ));
			    
			    $this->response(array(
			    	'status'=>'success',
			    	'data'	=>'Stripe Payment Success!')
			    );
			    
			    
			} catch(\Stripe\Error\Card $e) {
			  
			    
			}
			
				
		} 
	}
		
}
?>