<?php
require_once('main.php');
class Touches extends Main
{
	function __construct()
	{
		parent::__construct('touches');
	}
	
	//retrieve
	function index()
	{
		$this->session->unset_userdata('searchterm');
	
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('touches/index');
		$pag['total_rows'] = $this->touch->count_all();
		
		$data['touches'] = $this->touch->get_all($pag['per_page'],$this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('touches/view',$data,true);		
		
		$this->load_template($content);
	}
}