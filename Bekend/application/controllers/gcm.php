<?php
require_once('main.php');

class gcm extends Main 
{
	function __construct() 
	{
		parent::__construct("gcm");
	}

	function index() 
	{
		if ( $this->input->server( 'REQUEST_METHOD' ) == "POST" ) {
			$message = htmlentities($this->input->post( 'message' ));

			$devices = $this->gcm_token->get_all()->result();;

			$reg_ids = array();
			if ( count( $devices ) > 0 ) {
				foreach ( $devices as $device ) {
					$reg_ids[] = $device->reg_id;
				}
			}

			$status = $this->sendMessageThroughGCM( $reg_ids, array( "m" => $message ));

			$this->session->set_flashdata( 'success', "Successfully Sent Push Notification" );

			redirect( 'gcm' );
		}
		
		$content['content'] = $this->load->view( 'gcm/form', array(), true );
		
		$this->load_template($content, false);
	}

	

}

?>