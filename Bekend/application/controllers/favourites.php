<?php
require_once('main.php');

class Favourites extends Main
{
	function __construct()
	{
		parent::__construct('favourites');
		$this->load->library('common');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('favourites/index');
		$pag['total_rows'] = $this->favourite->count_all($this->get_current_shop()->id);
		$data['favourites'] = $this->favourite->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		$content['content'] = $this->load->view('favourites/view', $data, true);		
		
		$this->load_template($content);
	}
}
?>