<?php
require_once('main.php');
class Currencies extends Main
{
	function __construct()
	{
		parent::__construct('currencies');
	}
	
	function index()
	{
		$data['currency'] = $this->currency->get_info(1);
		$content['content'] = $this->load->view('currency/edit',$data,true);		
		$this->load_template($content,false);
	}
	
	function update($currency_id=1) 
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		if ($this->input->server('REQUEST_METHOD')=='POST') {

			$tmp = array();
			foreach ( $this->input->post() as $key => $value ) {
				$tmp[$key] = htmlentities($value);
			}

			if ( $this->currency->save( $tmp, $currency_id )) {
				$this->session->set_flashdata('success','Currency is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('currencies'));
		}
		
		//$this->index();
			
	}
	
}