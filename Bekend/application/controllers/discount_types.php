<?php
require_once('main.php');

class Discount_Types extends Main
{
	function __construct()
	{
		parent::__construct('discount_types');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
	
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('discount_types/index');
		$pag['total_rows'] = $this->discount_type->count_all($this->get_current_shop()->id);
		
		$data['discount_types'] = $this->discount_type->get_all(
												$this->get_current_shop()->id, $pag['per_page'], 
												$this->uri->segment(3)
											);
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('discount_types/view',$data,true);		
		
		$this->load_template($content);
	}
	
	function add()
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('add');
		}
	
		if ($this->input->server('REQUEST_METHOD') == 'POST') {	
			$discount_type_data = array(
				'name' => htmlentities($this->input->post('name')),
				'percent' => htmlentities($this->input->post('percent')),
				'shop_id' => $this->get_current_shop()->id
			);
			
			if ($this->discount_type->save($discount_type_data)) {
				$items = $_POST['items'];
				
				if(count($items)>0) {
					$this->item->remove_discount_type($discount_type_data['id']);
					$this->item->add_discount_type($discount_type_data['id'], $items);
				}
				
				$this->session->set_flashdata('success','Discount Type is successfully added.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			
			redirect(site_url('discount_types'));
		}
		
		$content['content'] = $this->load->view('discount_types/add',array(),true);
		
		$this->load_template($content);
	}
	
	function search()
	{
		$search_term = $this->searchterm_handler(htmlentities($this->input->post('searchterm')));

		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('discount_types/search');
		$pag['total_rows'] = $this->discount_type->count_all_by(
								$this->get_current_shop()->id, 
								array('searchterm'=>$search_term)
							);
		
		$data['searchterm'] = $search_term;
		$data['discount_types'] = $this->discount_type->get_all_by(
									$this->get_current_shop()->id, 
									array('searchterm'=>$search_term),
									$pag['per_page'],
									$this->uri->segment(3)
								);
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('discount_types/search',$data,true);		
		
		$this->load_template($content);	
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
	
	function edit($discount_type_id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
	
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
			$discount_type_data = array(
				'name' => htmlentities($this->input->post('name')),
				'percent' => htmlentities($this->input->post('percent')),
				'shop_id' => $this->get_current_shop()->id
			);
			
			if($this->discount_type->save($discount_type_data, $discount_type_id)) {
				$items = $_POST['items'];
				
				$this->item->remove_discount_type($discount_type_id);
				$this->item->add_discount_type($discount_type_id, $items);
				
				$this->session->set_flashdata('success','Discount is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			
			redirect(site_url('discount_types'));
		}
		
		$data['discount_type'] = $this->discount_type->get_info($discount_type_id);
		
		$content['content'] = $this->load->view('discount_types/edit',$data,true);		
		
		$this->load_template($content);
	}
	
	function delete($discount_type_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		if($this->discount_type->delete($discount_type_id)) {
			$this->item->remove_discount_type($discount_type_id);
			$this->session->set_flashdata('success','Discount is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		
		redirect(site_url('discount_types'));
	}
	
	function exists($shop_id = 0)
	{
		$name = $_REQUEST['name'];
		if (strtolower($this->discount_type->get_discount_by_shop_id($shop_id)->name) == strtolower($name)) {
			echo "true";
		} else if ($this->discount_type->exists(array('name'=>$_REQUEST['name'],'shop_id' => $shop_id))) {
			echo "false";
		} else {
			echo "true";
		}
	}
}
?>