<?php
require_once('main.php');

class Likes extends Main
{
	function __construct()
	{
		parent::__construct('likes');
		$this->load->library('common');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('likes/index');
		$pag['total_rows'] = $this->like->count_all($this->get_current_shop()->id);
		$data['likes'] = $this->like->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		$content['content'] = $this->load->view('likes/view',$data,true);		
		
		$this->load_template($content);
	}
}
?>