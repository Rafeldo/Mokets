<?php 
require_once(APPPATH.'/libraries/REST_Controller.php');

class Transactions extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		
		$this->load->library('email',array(
       	'mailtype'  => 'html',
        	'newline'   => '\r\n'
		));
		
	}
	
	function add_post()
	{
		
		$send_user = false;
		$send_shop = false;
		
		$data = $this->post('orders');
		$decode_data_array = json_decode($data);
		
		if(count($decode_data_array)>0) {
		
			if($decode_data_array[0]->payment_method == "stripe") {
				
				//For Transaction Header
				$header_data = array(
					'shop_id'           => $decode_data_array[0]->shop_id,
					'user_id'           => $decode_data_array[0]->user_id,
					'payment_trans_id'  => $decode_data_array[0]->payment_trans_id,
					'delivery_address'  => $decode_data_array[0]->delivery_address,
					'billing_address'   => $decode_data_array[0]->billing_address,
					'total_amount'      => $decode_data_array[0]->total_amount,
					'transaction_status'=> 2,
					'email'             => $decode_data_array[0]->email,
					'phone'             => $decode_data_array[0]->phone,
					'payment_method'    => $decode_data_array[0]->payment_method		
				);
				$this->transaction_header->save($header_data);
				$transaction_header_id = $header_data['id'];
				
				//For Transaction Detail
				for($i=0;$i<count($decode_data_array);$i++) 
				{
									
					$detail_data = array(
						'transaction_header_id' => $transaction_header_id,
						'shop_id'               => $decode_data_array[$i]->shop_id,
						'item_id'               => $decode_data_array[$i]->item_id,
						'item_name'             => $decode_data_array[$i]->name,
						'item_attribute'        => $decode_data_array[$i]->basket_item_attribute,
						'unit_price'            => $decode_data_array[$i]->unit_price,
						'qty'                   => $decode_data_array[$i]->qty,
						'discount_percent'      => $decode_data_array[$i]->discount_percent		
					);
					$this->transaction_detail->save($detail_data);				
				}
				
				//$this->response(array('success'=>'Order is successfully inserted.'));
				$user_id          = $decode_data_array[0]->user_id;
				$payment_method   = "stripe";
				$shop_id          = $decode_data_array[0]->shop_id;
				$delivery_address = $decode_data_array[0]->delivery_address;
				$billing_address  = $decode_data_array[0]->billing_address;
				$email            = $decode_data_array[0]->email;
				$phone            = $decode_data_array[0]->phone;
				
				
				if($this->send_email_to_user($user_id,$payment_method,$shop_id,$transaction_header_id,$decode_data_array))
				{
					$send_user = true;
				}
				
				if($this->send_email_to_shop($user_id,$payment_method,$shop_id,$delivery_address,$billing_address,$email,$phone,$transaction_header_id,$decode_data_array))
				{
					$send_shop = true;
				}
				
//				$this->response(array(
//					'status'=>'success',
//					'data'	=> "Order is successfully inserted.")
//				);

				if($send_user && $send_shop) {
				
					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted. Email successfully sent to User and Shop")
					);
					//Order is successfully inserted.
				} else if($send_user && !$send_shop){

					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted. Email successfully sent to User Only")
					);
					//Order is successfully submitted but email cannot send to shop.
				} else if(!$send_user && $send_shop){

					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted. Email successfully sent to Shop Only")
					);
					//Order is successfully submitted but email cannot send to user.
				} else if(!$send_user && !$send_shop){

					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted.But Email cannot send to both User and Shop. Please check sender email account from Shop Setting.")
					);
					//Order is successfully submitted but email cannot send to both user and shop.
				} else {
					//$this->response(array('error'=>'order_submit_error'));
					$this->response(array(
						'status'=>'error',
						'data'	=> "order_submit_error")
					);
				}
				
			
			} else if($decode_data_array[0]->payment_method == "cod" || $decode_data_array[0]->payment_method == "bank") {
				
				
				
				
				$payment_method   = $decode_data_array[0]->payment_method;
				$shop_id          = $decode_data_array[0]->shop_id;
				$delivery_address = $decode_data_array[0]->delivery_address;
				$billing_address  = $decode_data_array[0]->billing_address;
				$email            = $decode_data_array[0]->email;
				$phone            = $decode_data_array[0]->phone;
				$user_id          = $decode_data_array[0]->user_id;
				//For Transaction Header
				$header_data = array(
					'shop_id'           => $decode_data_array[0]->shop_id,
					'user_id'           => $decode_data_array[0]->user_id,
					'payment_trans_id'  => $decode_data_array[0]->payment_trans_id,
					'delivery_address'  => $decode_data_array[0]->delivery_address,
					'billing_address'   => $decode_data_array[0]->billing_address,
					'total_amount'      => $decode_data_array[0]->total_amount,
					'transaction_status'=> 1,
					'email'             => $decode_data_array[0]->email,
					'phone'             => $decode_data_array[0]->phone,
					'payment_method'    => $decode_data_array[0]->payment_method		
				);
				$this->transaction_header->save($header_data);
				$transaction_header_id = $header_data['id'];
				
				//For Transaction Detail
				for($i=0;$i<count($decode_data_array);$i++) 
				{
									
					$detail_data = array(
						'transaction_header_id' => $transaction_header_id,
						'shop_id'               => $decode_data_array[$i]->shop_id,
						'item_id'               => $decode_data_array[$i]->item_id,
						'item_name'             => $decode_data_array[$i]->name,
						'item_attribute'        => $decode_data_array[$i]->basket_item_attribute,
						'unit_price'            => $decode_data_array[$i]->unit_price,
						'qty'                   => $decode_data_array[$i]->qty,
						'discount_percent'      => $decode_data_array[$i]->discount_percent		
					);
					
					$this->transaction_detail->save($detail_data);				
				}
				
				
				
				
				
				if($this->send_email_to_user($user_id,$payment_method,$shop_id,$transaction_header_id,$decode_data_array))
				{
					$send_user = true;
				}
				
				if($this->send_email_to_shop($user_id,$payment_method,$shop_id,$delivery_address,$billing_address,$email,$phone,$transaction_header_id,$decode_data_array))
				{
					$send_shop = true;
				}
				
				

				
				if($send_user && $send_shop) {

					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted. Email successfully sent to User and Shop")
					);
					//Order is successfully inserted.
				} else if($send_user && !$send_shop){

					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted. Email successfully sent to User Only")
					);
					//Order is successfully submitted but email cannot send to shop.
				} else if(!$send_user && $send_shop){

					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted. Email successfully sent to Shop Only")
					);
					//Order is successfully submitted but email cannot send to user.
				} else if(!$send_user && !$send_shop){

					$this->response(array(
						'status'=>'success',
						'data'	=> "Order is successfully inserted.But Email cannot send to both User and Shop. Please check sender email account from Shop Setting.")
					);
					//Order is successfully submitted but email cannot send to both user and shop.
				} else {
					//$this->response(array('error'=>'order_submit_error'));
					$this->response(array(
						'status'=>'error',
						'data'	=> "order_submit_error")
					);
				}
				
				
				
				
				
			}
			
		} else {
			//$this->response(array('error' => array('message' => 'invalid_data')));
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'invalid_data')
			);
		}
//		
		
		
	}
	
	
	function user_transactions_get()
	{
		$user_id = $this->get('user_id');
		if (!$user_id) {
			$this->response(array('error' => array('message' => 'require_user_id')));
		}
		
		$headers = $this->transaction_header->get_all_by_user($user_id)->result();
		if(count($headers)>0){
			$j = 0;
			foreach ($headers as $header) {
				$tran[$j] = $this->transaction_header->get_info($header->id);
				$tran[$j]->added = $this->ago($tran[$j]->added);
				
				$shop = $this->shop->get_info($tran[$j]->shop_id);
				$tran[$j]->currency_symbol = html_entity_decode($shop->currency_symbol);
				$tran[$j]->currency_short_form = html_entity_decode($shop->currency_short_form);
				
				$tran[$j]->transaction_status = $this->transaction_status->get_info($header->transaction_status)->title;
				$tran[$j]->details = $this->transaction_detail->get_all_by_header($header->id)->result();
				$j++;
			}
			
			$this->response(array(
				'status'=>'success',
				'data'	=>$tran)
			);
			
		} else {
			$this->response(array(
				'status'=> 'error',
				'data'	=> 'No Transactions')
			);
		}
		
	}
	
	function ago($time)
	{
		$time = mysql_to_unix($time);
		$now = mysql_to_unix($this->category->get_now());
		
	   $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	   $lengths = array("60","60","24","7","4.35","12","10");
	
	   $difference = $now - $time;
	  	$tense = "ago";
	
	   for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
	       $difference /= $lengths[$j];
	   }
	
	   $difference = round($difference);
	
	   if ($difference != 1) {
	       $periods[$j].= "s";
	   }
	
	   if ($difference==0) {
	   		return "Just Now";
	   } else {
	   		return "$difference $periods[$j] ago";
	   }
	}
	
	
	function send_email_to_user($user_id,$payment_method,$shop_id,$trans_id,$order_data)
	{
		if($payment_method == "cod" || $payment_method == "stripe"){
			$appuser = $this->appuser->get_info($user_id);
			$shop = $this->shop->get_info($shop_id);
			$order_items = "";
			$total_amount = 0;
			
			for($i=0;$i<count($order_data);$i++) 
			{
				$order_items .= $i + 1 .". " . $order_data[$i]->name . 
				" (Price : " .  $order_data[$i]->unit_price . $shop->currency_symbol . 
				", QTY : " . $order_data[$i]->qty . ") <br>";
					
					$total_amount += $order_data[$i]->unit_price * $order_data[$i]->qty;
		
			}
			
			
			
			
			
			
			$trans_info = "Please take note your transaction id is " . $trans_id . " for future inquiry to the shop.";
			
			
			
			$sender_email = trim($shop->sender_email);
			$sender_name  = $shop->name;
			$to = $appuser->email;
			$subject = 'Order Confirmation';
			$html = "<p>Hi ".$appuser->username.",</p>".
						"<p>Your order has been sent to the restaurant for the following dish at below : <br/><br/>".
						$order_items. "<br>".
						"Total Amount : " . $total_amount . " " . $shop->currency_symbol . "<br/><br/>".
						$trans_info.
						"<p>Best Regards,<br/>".$sender_name."</p>";
						
			$this->email->from($sender_email,$sender_name);
			$this->email->to($to); 
			$this->email->subject($subject);
			$this->email->message($html);	
			
			
			
			
			
			if($this->email->send()){
				return true;
			} else {
				return false;
			}
			
			
			
			
			
		} else if($payment_method == "bank") {
			
			$appuser = $this->appuser->get_info($user_id);
			$shop = $this->shop->get_info($shop_id);
			$order_items = "";
			$total_amount = 0;
			
			for($i=0;$i<count($order_data);$i++) 
			{
				$order_items .= $i + 1 .". " . $order_data[$i]->name . " (Price : " .  $order_data[$i]->unit_price . html_entity_decode($shop->currency_symbol) . ", QTY : " . $order_data[$i]->qty . ") <br>";
				
				$total_amount += $order_data[$i]->unit_price * $order_data[$i]->qty;
			}
			
			$bank_info  = "<br><br>Please take note about bank informatino for your trnasfer. <br>";
			$bank_info .= " Bank Account : " . $shop->bank_account ."<br>" ;
			$bank_info .= " Bank Name    : " . $shop->bank_name ."<br>" ;
			$bank_info .= " Bank Code    : " . $shop->bank_code ."<br>" ;
			$bank_info .= " Branch Code  : " . $shop->branch_code ."<br>" ;
			$bank_info .= " Swift Code   : " . $shop->swift_code ."<br>" ;
			
			
			$trans_info = "Please take note your transaction id is " . $trans_id . " for future inquiry to the shop.";
			
			
			$sender_email = trim($shop->sender_email);
			$sender_name  = $shop->name;
			$to = $appuser->email;
			$subject = 'Order Confirmation';
			$html = "<p>Hi ".$appuser->username.",</p>".
						"<p>Your order has been sent to the restaurant for the following dish at below : <br/><br/>".
						$order_items. "<br>".
						"Total Amount : " . $total_amount . " " . html_entity_decode($shop->currency_symbol) . "<br/><br/>".
						$trans_info.$bank_info .
						"<p>Best Regards,<br/>".$sender_name."</p>";
						
			$this->email->from($sender_email,$sender_name);
			$this->email->to($to); 
			$this->email->subject($subject);
			$this->email->message($html);	
			if($this->email->send()){
				return true;
			} else {
				return false;
			}
			
		}
		
	}
		
	function send_email_to_shop($user_id,$payment_method,$shop_id,$delivery_address,$billing_address,$email,$phone,$trans_id,$order_data)
	{
		
		$appuser = $this->appuser->get_info($user_id);
		$shop = $this->shop->get_info($shop_id);
		
		$order_items = "";
		$total_amount = 0;
		
		for($i=0;$i<count($order_data);$i++) 
		{
			$order_items .= $i + 1 .". " . $order_data[$i]->name . " (Price : " .  $order_data[$i]->unit_price . html_entity_decode($shop->currency_symbol) . ", QTY : " . $order_data[$i]->qty . ") <br>";
			
			$total_amount += $order_data[$i]->unit_price * $order_data[$i]->qty;
			
			
		}
		
		if($payment_method == "cod") {
			$payment_info = "Payment Method : Cash On Delivery";
		} else if($payment_method == "bank") {
			$payment_info = "Payment Method : Bank Transfer";
		} else if($payment_method == "stripe") {
			$payment_info = "Payment Method : Stripe";
		}
		
		
		$trans_info = "Please take note Transaction Id is " . $trans_id . " for future inquiry and reference.";
				
		$del_address = "Here is delivery address for the order.<br/>";
		$del_address = $del_address . $delivery_address;
		
		$bil_address = "Here is billing address for the order.<br/>";
		$bil_address = $bil_address . $billing_address;
		
		$cust_info  = "Here is customer information.<br/>";
		$cust_info .= "User Name : " . $appuser->username . "<br>";
		$cust_info .= "Email     : " . $email . "<br>";
		$cust_info .= "Phone     : " . $phone . "<br>";		
		
		
		$sender_email = $shop->sender_email;
		$sender_name = $shop->name;
		$to = $shop->cod_email;
		$subject = 'Order Received';
		$html = "<p>Hi ".$shop->name.",</p>".
					"<p>You have been received the order for the following item at below : <br/><br/>".
					$order_items. "<br>".
					"Total Amount : " . $total_amount . " " . html_entity_decode($shop->currency_symbol) . "<br/><br/>".
					$trans_info. "<br/><br/>" . $del_address ."<br/><br/>". 
					$bil_address ."<br/><br/>".$cust_info."<br/><br/>". $payment_info
					."<br/><br/>"."<p>Best Regards,<br/>".$sender_name."</p>";
					
		$this->email->from($sender_email,$sender_name);
		$this->email->to($to); 
		$this->email->subject($subject);
		$this->email->message($html);	
		if($this->email->send()){
			return true;
		} else {
			return false;
		}
		
		
		
	}
	
	
		
}
?>