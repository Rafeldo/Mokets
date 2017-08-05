<?php
require_once('main.php');

class Transactions extends Main
{
	function __construct()
	{
		parent::__construct('transactions');
		$this->load->library('common');
	}
	
	function index()
	{
		$shop_id = $this->get_current_shop()->id;
		
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('transactions/index');
		$pag['total_rows'] = $this->transaction_header->count_all_by($shop_id);
		$data['transactions'] = $this->transaction_header->get_all_by(
											$shop_id,
											array(), 
											$pag['per_page'], 
											$this->uri->segment(3)
										)->result();
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('transactions/view',$data,true);		
		
		$this->load_template($content);
	}
	
	function search()
	{
		$search_term = $this->searchterm_handler(array(
								'searchterm' => htmlentities($this->input->post('searchterm')),
								'start_date' => htmlentities($this->input->post('start_date')),
								'end_date' => htmlentities($this->input->post('end_date'))
							));
		
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('transactions/search');
		$pag['total_rows'] = $this->transaction_header->count_all_by(
										$this->get_current_shop()->id, 
										$search_term
									);
		$data = $search_term;
		$data['transactions'] = $this->transaction_header->get_all_by(
											$this->get_current_shop()->id, 
											$search_term,
											$pag['per_page'],
											$this->uri->segment(3)
										)->result();
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('transactions/search',$data,true);
		
		$this->load_template($content);	
	}
	
	function update()
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('add');
		}
		if($this->transaction_header->update_status(htmlentities($this->input->post('transaction_status')),htmlentities($this->input->post('header_id')))){
			$this->session->set_flashdata('success','Transaction status is successfully updated.');
			redirect(site_url('transactions'));	
		}
	}
	
	function searchterm_handler($searchterm)
	{
	    if ($searchterm) {
	        $this->session->set_userdata('searchterm', $searchterm);
	        return $searchterm;
	    } elseif ($this->session->userdata('searchterm')) {
	        $searchterm = $this->session->userdata('searchterm');
	        return $searchterm;
	    } else {
	        $searchterm ="";
	        return $searchterm;
	    }
	}
}
?>