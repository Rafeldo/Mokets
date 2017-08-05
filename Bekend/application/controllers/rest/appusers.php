<?php 
require_once(APPPATH.'/libraries/REST_Controller.php');

class Appusers extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('email',array(
       	'mailtype'  => 'html',
        	'newline'   => '\r\n'
		));
	}
	
	function login_post()
	{
		$data = $this->post();
		
		if ($data == null) {
			$this->response(array(
				'status'=>'error',
				'data'	=>'Invalid JSON')
			);
		}
			
		if (!array_key_exists('email', $data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=>'Required Email')
			);
		}
		
		if (!array_key_exists('password', $data)) {
			$this->response(array(
				'status'=>'error',
				'data'	=>'Required Password')
			);
		}
		
		if ($user = $this->appuser->login($data['email'],$data['password'])) {
			$this->response(array(
				'status'=>'success',
				'data'	=>$user)
			);
		} else {
			$this->response(array(
				'status'=>'error',
				'data'	=>'Your login credential is wrong.')
			);
		}
	}
	
	function reset_post()
	{
		$email = $this->post('email');
		if (!$email) {
			$this->response(array(
				'status'=>'error',
				'data'	=>'Required Email')
			);
		}
		
		$appuser = $this->appuser->get_info_by_email($email);
		if ($appuser->id == "") {
			$this->response(array(
				'status'=>'error',
				'data'	=>'Your email is not exist in the system.')
			);
		}
		
		$code = md5(time().'teamps');
		
		$data = array(
			'user_id'=>$appuser->id,
			'code'=> $code
		);
		
		if ($this->code->save($data,$appuser->id)) {
			$sender_email = $this->config->item('sender_email');
			$sender_name = $this->config->item('sender_name');
			$to = $appuser->email;
		    $subject = 'Password Reset';
			$html = "<p>Hi,".$appuser->username."</p>".
						"<p>Please click the following link to reset your password<br/>".
						"<a href='".site_url('reset/'.$code)."'>Reset Password</a></p>".
						"<p>Best Regards,<br/>".$sender_name."</p>";
						
			$this->email->from($sender_email,$sender_name);
			$this->email->to($to); 
			$this->email->subject($subject);
			$this->email->message($html);	
			$this->email->send();
			
			$this->response(array(
				'status'=>'success',
				'data'	=>'Password reset email already sent!')
			);
		} else {
			$this->response(array(
				'status'=>'error',
				'data'	=>'Oops! System could not manage for your request. Please try again later.')
			);
		}
	}
	
	function add_post()
	{
		$data = $this->post();
				
				if ($data == null) {
					$this->response(array(
						'status'=>'error',
						'data'	=> 'Invalid JSON')
					);
					
				}
				
				if (!array_key_exists('username', $data)) {
					$this->response(array(
						'status'=>'error',
						'data'	=> 'Required Username')
					);
				}
					
				if (!array_key_exists('email', $data)) {
					$this->response(array(
						'status'=>'error',
						'data'	=> 'Required Email')
					);
				}
				
				if (!array_key_exists('password', $data)) {
					$this->response(array(
						'status'=>'error',
						'data'	=> 'Required Password')
					);
					
				}
				
				$user_data = array(
					'username'      => $data['username'],
					'password'      => md5($data['password']),
					'email'         => $data['email'],
					'about_me'      => $data['about_me'],
					'profile_photo' => "default_user_profile.png"
				);
		
				if ($this->appuser->exists($user_data)) {
					$this->response(array(
						'status'=>'error',
						'data'	=> 'Email already exist')
					);
				} else {
					$this->appuser->save($user_data);
					$this->response(array(
						'status'=>'success',
						'data'	=> $user_data['id'])
					);
				}
	}
	
	function update_put()
	{
		
		$data = $this->put();
		
		if ( !$data['platformName'] ) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Platform')
			);
		}
		
		if ($data['platformName'] == "android") {
		
			$id = $this->get('id');
			if (!$id) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Required ID')
				);
			}
			
			$data = $this->put();
			if ($data == null) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Invalid JSON')
				);
			}
			
			$user_data = $data;
			$user_data['id'] = $id;
			if (array_key_exists('password',$data)) {
				$user_data['password'] = md5($data['password']);
			}
			
			if (array_key_exists('email',$data)) {
				if (strtolower($this->appuser->get_info($id)->email) != strtolower($user_data['email'])) {
					$cond = array('email'=>strtolower($user_data['email']));
					
					if ($this->appuser->exists($cond)) {
						$this->response(array(
							'status'=>'error',
							'data'	=> 'Email already exist')
						);
					}
				}
			}
			
			if($user_data['password'] == "") {
				$update_userdata['username']    = $user_data['username'];	
				$update_userdata['email']       = $user_data['email'];	
				$update_userdata['about_me']    = $user_data['about_me'];	
				$update_userdata['phone']             = $user_data['phone'];
				$update_userdata['delivery_address']  = $user_data['delivery_address'];
				$update_userdata['billing_address']   = $user_data['billing_address'];
			} else {
				$update_userdata['password']    = $user_data['password'];
//				$update_userdata['username']    = $user_data['username'];	
//				$update_userdata['email']       = $user_data['email'];	
//				$update_userdata['about_me']    = $user_data['about_me'];
				
			}
			
			//var_dump($user_data); die;
			$this->appuser->save($update_userdata,$id);
			$this->response(array(
				'status'=>'success',
				'data'	=> 'User profile is successfully updated')
			);
			
		
		} else {
		
			$id = $this->get('id');
			if (!$id) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Required ID')
				);
			}
			
			$data = $this->put();
			if ($data == null) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Invalid JSON')
				);
			}
			
			//$user_data = $data;
			$user_data['id'] = $id;
			if (array_key_exists('password',$data)) {
				$user_data['password'] = md5($data['password']);
			}
			
			$user_data['username'] = $data['username'];
			$user_data['about_me'] = $data['about_me'];
			$user_data['email']    = $data['email'];
			$user_data['phone']    = $data['phone'];
			$user_data['delivery_address'] = $data['delivery_address'];
			$user_data['billing_address']  = $data['billing_address'];
			
			
			$this->appuser->save($user_data,$id);
			$this->response(array(
				'status'=>'success',
				'data'	=> 'User profile is successfully updated.')
			);
		
		}
		
	}
}
?>