<?php	
require_once(APPPATH.'/libraries/REST_Controller.php');

class Gcm extends REST_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	function get_all_get()
	{
		$data = $this->gcm_token->get_all()->result();

		$this->response(array(
			'status'=>'success',
			'data'	=>$data
		));
	}

	function register_post()
	{
		$data = $this->post();
		
		$platform_name = $this->post('platformName');
		
		if ( !$platform_name ) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Platform')
			);
		}
		
		if ($platform_name == "android") {
			
			if ( $data == null ) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Invalid JSON')
				);
			}
	
			if ( ! array_key_exists( 'reg_id', $data )) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Required Token ID')
				);
			}
			
			$user_data = array(
				'reg_id' => $data['reg_id']
			);
	
			if ( $this->gcm_token->exists( $user_data )) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Token already exist')
				);
			} else {
				
				$user_data = array(
					'reg_id' => $data['reg_id'],
					'os_type' => "ANDROID",
					'device_id' => ""
				);
				
				$this->gcm_token->save( $user_data,"android");
				$this->response(array(
					'status'=>'success',
					'data'	=> 'Token successfully added.')
				);
			}
			
		} else {
			if ( $data == null ) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Invalid JSON')
				);
			}
	
			if ( ! array_key_exists( 'reg_id', $data )) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Required Token ID')
				);
			}
	
			if ( ! array_key_exists( 'device_id', $data )) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Required Device ID')
				);
			}
	
			if ( ! array_key_exists( 'os_type', $data )) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Required OS Type')
				);
			}
			
			$user_data = array(
				'reg_id' => $data['reg_id'],
				'os_type' => $data['os_type'],
				'device_id' => $data['device_id']
			);
	
			if ( $this->gcm_token->exists( $user_data )) {
				$this->response(array(
					'status'=>'error',
					'data'	=> 'Token already exist')
				);
			} else {
				if ( $this->gcm_token->save( $user_data,"ios", $data['device_id'] ) ) {
					$this->response(array(
						'status'=>'success',
						'data'	=> 'Token successfully added.')
					);	
				} else {
					$this->response(array(
						'status'=>'error',
						'data'	=> 'Error occured in inserting.')
					);
				}
			}
		}
		
		
	}

	function unregister_post()
	{
		$data = $this->post();

		if ( $data == null ) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Invalid JSON')
			);
		}

		if ( ! array_key_exists( 'reg_id', $data )) {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Required Token ID')
			);
		}
		
		$user_data = array(
			'reg_id' => $data['reg_id']
		);

		if ( $this->gcm_token->exists( $user_data )) {
			$this->gcm_token->delete_by( $user_data );
			$this->response(array(
				'status'=>'success',
				'data'	=> 'Token successfully removed.')
			);
		} else {
			$this->response(array(
				'status'=>'error',
				'data'	=> 'Token already exist')
			);
		}
	}

}

?>