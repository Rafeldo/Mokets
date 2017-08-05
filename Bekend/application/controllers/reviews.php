<?php
require_once('main.php');

class Reviews extends Main
{
	function __construct()
	{
		parent::__construct('reviews');
		$this->load->library('common');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('reviews/index');
		$pag['total_rows'] = $this->review->count_all($this->get_current_shop()->id);
		$data['reviews'] = $this->review->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		$content['content'] = $this->load->view('reviews/view',$data,true);		
		
		$this->load_template($content);
	}
	
	function search()
	{
		$search_term = $this->searchterm_handler(htmlentities($this->input->post('searchterm')));
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('reviews/search');
		$pag['total_rows'] = $this->review->count_all_by($this->get_current_shop()->id, array('searchterm'=>$search_term));
		$data['searchterm'] = $search_term;
		$data['reviews'] = $this->review->get_all_by(
												$this->get_current_shop()->id,
												array('searchterm'=>$search_term),
												$pag['per_page'],
												$this->uri->segment(3)
											);
		$data['pag'] = $pag;
		$content['content'] = $this->load->view('reviews/search',$data,true);		
		
		$this->load_template($content);
	}

	function searchterm_handler($searchterm)
	{
	    if ($searchterm) {
	        $this->session->set_userdata('searchterm', $searchterm);
	        return $searchterm;
	    } elseif($this->session->userdata('searchterm')) {
	        $searchterm = $this->session->userdata('searchterm');
	        return $searchterm;
	    } else {
	        $searchterm ="";
	        return $searchterm;
	    }
	}
	
	function detail($review_id)
	{
		$data['review'] = $this->review->get_info($review_id);
		$content['content'] = $this->load->view('reviews/detail',$data,true);		
		
		$this->load_template($content);
	}

	function delete($review_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		if ($this->review->delete($review_id)) {
			$this->session->set_flashdata('success','The review is successfully deleted.');
		} else {
			$this->session->set_flashdata('error',
			'Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('reviews'));
	}
}
?>