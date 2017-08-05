<?php
require_once('main.php');
class Shops extends Main
{
	function __construct()
	{
		parent::__construct( NO_ACCESS_CONTROL );
		$this->load->library('uploader');
	}
	
	function index() 
	{
		$this->session->unset_userdata('shop_id');
	
		$shops = array();
		if ($this->input->server('REQUEST_METHOD')=='POST') {
			$searchterm = htmlentities($this->input->post('searchterm'));
			$shops = $this->shop->get_all_by(array('searchterm' => $searchterm))->result();
			$data['searchterm'] = $searchterm;
		} else {
			$shops = $this->shop->get_all()->result();
		}
		
		$temp_shops_arr = array();
		foreach ($shops as $shop) {
			$img = $this->image->get_all_by_type($shop->id, 'shop')->result();
			$shop->image = $img[0]->path;
			$temp_shops_arr[] = $shop;
		}
		$data['shops'] = $temp_shops_arr;
	
		$this->load->view('shops/view', $data);
	}
	
	function create()
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('add');
		}
		if ($this->input->server('REQUEST_METHOD')=='POST') {			
						
			$upload_data = $this->uploader->upload($_FILES);
			
			if (!isset($upload_data['error'])) {

				$shop_data = array();
				foreach ( $this->input->post() as $key=>$value) {
					$shop_data[$key] = htmlentities($value);
				}
				$shop_data['id'] = 	$this->shop->get_latest_id() + 1;
				
				$img_desc = $shop_data['image_desc'];
				unset($shop_data['image_desc']);
				unset($shop_data['images']);
				unset($shop_data['find_location']);
				
				if ($this->shop->save($shop_data)) {
					foreach ($upload_data as $upload) {
						$image = array(
							'parent_id'=>$shop_data['id'],
							'type' => 'shop',
							'description' => $img_desc,
							'path' => $upload['file_name'],
							'width'=>$upload['image_width'],
							'height'=>$upload['image_height']
						);
						$this->image->save($image);
					}
								
					$this->session->set_flashdata('success','Shop is successfully added.');
				} else {
					$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
				}
				redirect(site_url('shops/create'));
			} else {
				$data['error'] = $upload_data['error'];
			}
		}
		
		$content['content'] = $this->load->view('shops/create',array(),true);
		$this->load_template($content,false);
		
	}
	
	function edit($shop_id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('edit');
		}
		
		$this->session->set_userdata('shop_id', $shop_id);
		$this->session->set_userdata('action', 'shop_edit');
		
		if ($this->input->server('REQUEST_METHOD')=='POST') {
			
			$shop_data = array();
			foreach ( $this->input->post() as $key=>$value) {
				$shop_data[$key] = htmlentities($value);
			}

			unset($shop_data['find_location']);

			$shop_data['stripe_enabled'] = (isset($shop_data['stripe_enabled'])) ? 1: 0;
			$shop_data['cod_enabled'] = (isset($shop_data['cod_enabled'])) ? 1: 0;
			$shop_data['banktransfer_enabled'] = (isset($shop_data['banktransfer_enabled'])) ? 1: 0;

			if ($this->shop->save($shop_data, $shop_id)) {
				$this->session->set_flashdata('success','Shop Information is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('shops/edit/' . $shop_id));
		}
		
		$data['shop'] = $this->shop->get_info($shop_id);
		
		$content['content'] = $this->load->view('shops/edit',$data,true);
		$this->load_template($content,false,true);
	}
	
	function upload($shop_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		$upload_data = $this->uploader->upload($_FILES);
		
		if (!isset($upload_data['error'])) {
			unlink('./uploads/'.$this->image->get_info_parent_type($shop_id,'shop')->path);
			unlink('./uploads/thumbnail/'.$this->image->get_info_parent_type($shop_id,'shop')->path);
			$this->image->delete_by_parent($shop_id,'shop');
			
			foreach ($upload_data as $upload) {
				$image = array(
					'parent_id'=> $shop_id,
					'type' => 'shop',
					'description' => htmlentities($this->input->post('image_desc')),
					'path' => $upload['file_name'],
					'width'=>$upload['image_width'],
					'height'=>$upload['image_height']
				);
				$this->image->save($image);
				redirect(site_url('shops/edit/' . $shop_id));
			}
			
		} else {
			$data['error'] = $upload_data['error'];
		}
		
		$data['shop'] = $this->shop->get_info($shop_id);
		
		$content['content'] = $this->load->view('shops/edit',$data,true);
		$this->load_template($content);
	}
	
	
	function edit_image($shop_id, $image_id)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		$image = array(
			'description' => htmlentities($this->input->post('image_desc'))
		);
			
		if ($this->image->save($image, $image_id)) {
			$this->session->set_flashdata('success','Shop cover photo description is successfully updated.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('shops/edit/' . $shop_id));
	}

	function delete_image($shop_id,$image_id,$image_name)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		if ($this->image->delete($image_id)) {
			unlink('./uploads/'.$image_name);
			unlink('./uploads/thumbnail/'.$image_name);
			$this->session->set_flashdata('success','Shop cover photo is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('shops/edit/' . $shop_id));
	}	
	
	
	function delete_shop($shop_id)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		// shop images		
		$shop_images = $this->image->get_all_by_type( $shop_id, 'shop' );
		foreach ( $shop_images->result() as $img ) {
			if ( $this->image->delete( $img->id )) {
				@unlink('./uploads/'.$img->path);
				@unlink('./uploads/thumbnail/'.$img->path);		
			}
		}
		
		// category images
		$categories = $this->category->get_all($shop_id)->result();
		foreach ( $categories  as $category ) {
			$cat_imgs = $this->image->get_all_by_type( $category->id, 'category' )->result();
			foreach ( $cat_imgs as $img ) {
				if ($this->image->delete($img->id)) {
					@unlink('./uploads/'.$img->path);
					@unlink('./uploads/thumbnail/'.$img->path);	
				}
			}
		}
		
		// sub_category images
		$sub_categories = $this->sub_category->get_all( $shop_id )->result();
		foreach ( $sub_categories as $sub_category ) {
			$sub_cat_imgs = $this->image->get_all_by_type( $sub_category->id, 'sub_category' )->result();
			foreach ( $sub_cat_imgs as $img ) {
				if ($this->image->delete($img->id)) {
					@unlink('./uploads/'.$img->path);
					@unlink('./uploads/thumbnail/'.$img->path);	
				}
			}
		}
		
		// item images
		$items = $this->item->get_all( $shop_id )->result();
		foreach ( $items as $item ) {
			$item_imgs = $this->image->get_all_by_type( $item->id, 'item' )->result();
			foreach ( $item_imgs as $img ) {
				if ($this->image->delete($img->id)) {
					@unlink('./uploads/'.$img->path);
					@unlink('./uploads/thumbnail/'.$img->path);	
				}
			}
		}
		
		
		// feed images
		$feeds = $this->feed->get_all( $shop_id )->result();
		foreach ( $feeds as $feed ) {
			$feed_imgs = $this->image->get_all_by_type( $feed->id, 'feed' )->result();
			foreach ( $feed_imgs as $img ) {
				if ($this->image->delete($img->id)) {
					@unlink('./uploads/'.$img->path);
					@unlink('./uploads/thumbnail/'.$img->path);	
				}
			}
		}
		
		
		$this->category->delete_by_shop($shop_id);
		$this->discount_type->delete_by_shop($shop_id);
		$this->favourite->delete_by_shop($shop_id);
		$this->feed->delete_by_shop($shop_id);
		//$this->image->delete_by_shop($shop_id); //still need to delete physical images
		$this->like->delete_by_shop($shop_id);
		$this->review->delete_by_shop($shop_id);
		$this->shop->delete_by_shop($shop_id);
		$this->follow->delete_by_shop($shop_id);
		$this->sub_category->delete_by_shop($shop_id);
		$this->touch->delete_by_shop($shop_id);
		$this->transaction_detail->delete_by_shop($shop_id);
		$this->transaction_header->delete_by_shop($shop_id);
		
		$this->item->delete_by_shop($shop_id);
		$this->attribute_header->delete_by_shop($shop_id);
		$this->attribute_detail->delete_by_shop($shop_id);
		
		$this->session->set_flashdata('success','Shop is successfully deleted.');
		redirect(site_url('shops'));
	}	
	
	function send_gcm() 
	{
		if( $this->session->userdata('is_city_admin')) {
		    return;
		}
		$content['content'] = $this->load->view( 'gcm/form', array(), true );
		$this->load_template($content, false);
	}
	
	function push_message() 
		{
			if ( $this->input->server( 'REQUEST_METHOD' ) == "POST" ) {
				$message = htmlentities($this->input->post( 'message' ));
	
				$error_msg = "";
				$success_device_log = "";
	
				// Android Push Notification
				$devices = $this->gcm_token->get_all_by(array('os_type' => 'ANDROID'))->result();;
	
				$reg_ids = array();
				if ( count( $devices ) > 0 ) {
					foreach ( $devices as $device ) {
						$reg_ids[] = $device->reg_id;
					}
				}
	
				$status = $this->sendMessageThroughFCM( $reg_ids, array( "message" => $message ));
				if ( !$status ) $error_msg .= "Fail to push all android devices <br/>";
	
				// IOS Push Notification
				$devices = $this->gcm_token->get_all_by(array('os_type' => 'IOS'))->result();;
	
				if ( count( $devices ) > 0 ) {
					foreach ( $devices as $device ) {
						if ( ! $this->sendMessageThroughIOS( $device->reg_id, $message )) {
							$error_msg .= "Fail to push ios device named ". $device->reg_id ."<br/>";
							//echo $error_msg;
						} else {
							//echo " Sent to : " . $device->reg_id;
							$success_device_log .= " Device Id : " . $device->reg_id . "<br>";
						}
					}
				}
				//die;
				// response message
				if ( $status ) {
					$this->session->set_flashdata( 'success', "Successfully Sent Push Notification.<br>" . $success_device_log );
				}
	
				if ( !empty( $error_msg )) {
					$this->session->set_flashdata( 'error', $error_msg );
				}
	
				redirect( 'shops/send_gcm' );
			}
	
			$content['content'] = $this->load->view( 'gcm/form', array(), true );
			
			$this->load_template($content, false);
		}
	
		function sendMessageThroughIOS($tokenId, $message) 
		{
			ini_set('display_errors','On'); 
			//error_reporting(E_ALL);
			// Change 1 : No braces and no spaces
			$deviceToken= $tokenId;
			//'fe2df8f5200b3eb133d84f73cc3ea4b9065b420f476d53ad214472359dfa3e70'; 
			// Change 2 : If any
			$passphrase = '12345'; 
			$ctx = stream_context_create();
			// Change 3 : APNS Cert File name and location.
			stream_context_set_option($ctx, 'ssl', 'local_cert', realpath('application').'/apns/apns_cert_mokets.pem'); 
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
			// Open a connection to the APNS server
			$fp = stream_socket_client( 
			    'ssl://gateway.push.apple.com:2195', $err,
			    $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
			if (!$fp)
			    exit("Failed to connect: $err $errstr" . PHP_EOL);
			//echo 'Connected to APNS' . PHP_EOL;
			// Create the payload body
			$body['aps'] = array(
			    'alert' => $message,
			    'sound' => 'default'
			    );
			// Encode the payload as JSON
			$payload = json_encode($body);
			// Build the binary notification
			$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
			// Send it to the server
			$result = fwrite($fp, $msg, strlen($msg));
			// Close the connection to the server
			fclose($fp);
			//var_dump($result); die;
			if (!$result) 
			    //echo 'Message not delivered' . PHP_EOL;
			    return false;
	
			//echo 'Message successfully delivered' . PHP_EOL;
			return true;
		}
		
	
		//Generic php function to send GCM push notification
	   	function sendMessageThroughGCM( $registatoin_ids, $message) 
	   	{
			//Google cloud messaging GCM-API url
			$url = 'https://android.googleapis.com/gcm/send';
			$fields = array(
			    'registration_ids' => $registatoin_ids,
			    'data' => $message,
			);
			// Update your Google Cloud Messaging API Key
			//define("GOOGLE_API_KEY", "AIzaSyCCwa8O4IeMG-r_M9EJI_ZqyybIawbufgg");
			define("GOOGLE_API_KEY", $this->config->item( 'gcm_api_key' ));  	
				
			$headers = array(
			    'Authorization: key=' . GOOGLE_API_KEY,
			    'Content-Type: application/json'
			);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			$result = curl_exec($ch);				
			if ($result === FALSE) {
			    die('Curl failed: ' . curl_error($ch));
			}
			curl_close($ch);
			return $result;
	    }
	    
	    function sendMessageThroughFCM( $registatoin_ids, $message) 
	    {
	    	//Google cloud messaging GCM-API url
	    	$url = 'https://fcm.googleapis.com/fcm/send';
	    	$fields = array(
	    	    'registration_ids' => $registatoin_ids,
	    	    'data' => $message,
	    	);
	    	// Update your Google Cloud Messaging API Key
	    	//define("GOOGLE_API_KEY", "AIzaSyCCwa8O4IeMG-r_M9EJI_ZqyybIawbufgg");
	    	define("GOOGLE_API_KEY", $this->config->item( 'fcm_api_key' ));  	
	    		
	    	$headers = array(
	    	    'Authorization: key=' . GOOGLE_API_KEY,
	    	    'Content-Type: application/json'
	    	);
	    	$ch = curl_init();
	    	curl_setopt($ch, CURLOPT_URL, $url);
	    	curl_setopt($ch, CURLOPT_POST, true);
	    	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
	    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
	    	$result = curl_exec($ch);				
	    	if ($result === FALSE) {
	    	    die('Curl failed: ' . curl_error($ch));
	    	}
	    	curl_close($ch);
	    	
	    	return $result;
	    }
	
}
?>