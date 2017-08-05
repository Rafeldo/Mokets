<?php
require_once('main.php');

class Follows extends Main
{
	function __construct()
	{
		parent::__construct('follows');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('follows/index');
		$pag['total_rows'] = $this->follow->count_all($this->get_current_shop()->id);
		$data['follows'] = $this->follow->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		$content['content'] = $this->load->view('follows/view',$data,true);		
		
		$this->load_template($content);
	}
}
?>