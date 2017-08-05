<?php
class Moket extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('email',array(
										       	'mailtype'  => 'html',
										        	'newline'   => '\r\n'
												));
	}
	
	function login()
	{
		if ($this->user->is_logged_in()) {
			redirect(site_url());
		} else {
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				$user_name = htmlentities($this->input->post('user_name'));
				$user_password = htmlentities($this->input->post('user_pass'));
				if ($this->user->login($user_name,$user_password)) {
					if($this->session->userdata('is_shop_admin')) {
						redirect(site_url() . "/dashboard/index/" . $this->session->userdata('allow_shop_id'));
					} else {
						redirect(site_url());
					}
					
				} else {
					$this->session->set_flashdata('error','Username and password do not match.');
					redirect(site_url('login'));
				}
			} else {
				$this->load->view('login');	
			}
		}
	}

	function logout()
	{
		$this->user->logout();
	}
	
	function reset($code = false)
	{
		if (!$code || !$this->code->exists(array('code'=>$code))) {
			redirect(site_url('login'));
		}
		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$code = $this->code->get_by_code($code);
			if ($code->is_systemuser == 1) {
				$data = array(
								'user_pass' => md5($this->input->post('password'))
							);
				if ($this->user->update_profile($data,$code->user_id)) {
					$this->code->delete($code->user_id);
					$this->session->set_flashdata('success','Password is successfully reset.');
					redirect(site_url('login'));
				}
			} else {
				$data = array(
								'password' => md5($this->input->post('password'))
							);
				if ($this->appuser->save($data,$code->user_id)) {
					$this->code->delete($code->user_id);
					$this->session->set_flashdata('success','Password is successfully reset.');
					redirect(site_url('login'));
				}
			}
		}
		
		$data['code'] = $code;
		$this->load->view('reset/reset',$data);
	}
	
	function forgot()
	{
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$email = htmlentities($this->input->post('user_email'));
			$user = $this->user->get_info_by_email($email);
			
			if ($user->user_id == "") {
				$this->session->set_flashdata('error','Email does not exist in the system.');
			} else {
				$code = md5(time().'teamps');
				$data = array(
								'user_id'=>$user->user_id,
								'code'=> $code,
								'is_systemuser'=>1
								);
				if ($this->code->save($data,$user->user_id)) {
					$sender_email = $this->config->item('sender_email');
					$sender_name = $this->config->item('sender_name');
					$to = $user->user_email;
				   $subject = 'Password Reset';
					$html = "<p>Hi,".$user->user_name."</p>".
								"<p>Please click the following link to reset your password<br/>".
								"<a href='".site_url('reset/'.$code)."'>Reset Password</a></p>".
								"<p>Best Regards,<br/>".$sender_name."</p>";
								
					$this->email->from($sender_email,$sender_name);
					$this->email->to($to); 
					$this->email->subject($subject);
					$this->email->message($html);	
					$this->email->send();
					
					$this->session->set_flashdata('success','Password reset email already sent!');
					redirect(site_url('login'));
				} else {
					$this->session->set_flashdata('error','System error occured. Please contact your system administrator.');
				}
			}
		}
		
		$this->load->view('reset/forgot');
	}
}
?>