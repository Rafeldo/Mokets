<?php
require_once('main.php');

class Ratings extends Main
{
	function __construct()
	{
		parent::__construct('ratings');
		$this->load->library('common');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('likes/index');
		$pag['total_rows'] = $this->rating->count_all($this->get_current_shop()->id);
		$data['ratings'] = $this->rating->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		$content['content'] = $this->load->view('ratings/view',$data,true);		
		
		$this->load_template($content);
	}
}
?>