<?php
require_once('main.php');
class Attributes extends Main
{
	function __construct()
	{
		parent::__construct('attributes');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
	
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('attributes/index');
		$pag['total_rows'] = $this->attribute_header->count_all($this->get_current_shop()->id);
		
		$data['attributes_header'] = $this->attribute_header->get_all($this->get_current_shop()->id, $pag['per_page'],$this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('attributes/view',$data,true);		
		
		$this->load_template($content);
	}
	
	function view_detail($attribute_header_id=0)
	{
		$this->session->unset_userdata('searchterm');
		
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('attributes/index');
		$pag['total_rows'] = $this->attribute_detail->count_all_by_header($attribute_header_id,$this->get_current_shop()->id);
		
		$data['attributes_detail'] = $this->attribute_detail->get_all_by_header($attribute_header_id,$this->get_current_shop()->id, $pag['per_page'],$this->uri->segment(4));
		$data['pag'] = $pag;
		$data['attribute_header_id'] = $attribute_header_id;
		$data['attribute_header_name'] = $this->attribute_header->get_info($attribute_header_id)->name;
		
		$content['content'] = $this->load->view('attributes/view_detail',$data,true);		
		
		$this->load_template($content);
	}
	
	function add()
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('add');
		}
		
		if ($this->input->server('REQUEST_METHOD')=='POST') {			
			
			$attribute_header_data = array(
				'item_id' => $this->input->post('item_id'),
				'shop_id' => $this->get_current_shop()->id,
				'name' => $this->input->post('name')
				);
			
			if ($this->attribute_header->save($attribute_header_data)) {
				$this->session->set_flashdata('success','Attribute is successfully added.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('attributes'));
		}
		
		if($this->item->count_all($this->get_current_shop()->id) > 0){
			$content['content'] = $this->load->view('attributes/add', array(), true);
			$this->load_template($content);
		} else {
			$this->session->set_flashdata('success','Oops! Please create a item first before you create any item attributes.');
			redirect(site_url('attributes'));
		}
	}
	
	function add_detail($attribute_header_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('add');
		}
		if ($this->input->server('REQUEST_METHOD')=='POST') {			
			
			$attribute_detail_data = array(
				'header_id' => $attribute_header_id,
				'shop_id' => $this->get_current_shop()->id,
				'name' => $this->input->post('name'),
				'item_id' => $this->input->post('item_id'),
				'additional_price' => $this->input->post('additional_price')
				);
			
			if ($this->attribute_detail->save($attribute_detail_data)) {
				$this->session->set_flashdata('success','Attribute Detail is successfully added.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('attributes'));
		}
		
		$data['attributes_header'] = $this->attribute_header->get_info($attribute_header_id);
		$content['content'] = $this->load->view('attributes/add_detail', $data, true);
		$this->load_template($content);
		
	}
	
	function edit($attribute_header_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('edit');
		}
	
		if ($this->input->server('REQUEST_METHOD')=='POST') {
			
			$attribute_header_data = array(
				'item_id' => $this->input->post('item_id'),
				'shop_id' => $this->get_current_shop()->id,
				'name'    => $this->input->post('name')
			);
			
			if($this->attribute_header->save($attribute_header_data, $attribute_header_id)) {
				$this->session->set_flashdata('success','Attribute is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('attributes'));
		}
		
		$data['attributes_header'] = $this->attribute_header->get_info($attribute_header_id);
		$content['content'] = $this->load->view('attributes/edit',$data,true);		
		$this->load_template($content);
	}
	
	function edit_detail($attribute_detail_id = 0) 
	{
		if(!$this->session->userdata('is_shop_admin')) {
			      $this->check_access('edit');
		}
	
		if ($this->input->server('REQUEST_METHOD')=='POST') {
			
			$attribute_detail_data = array(
				'header_id' => $this->input->post('header_id'),
				'shop_id' => $this->get_current_shop()->id,
				'name' => $this->input->post('name'),
				'item_id' => $this->input->post('item_id'),
				'additional_price' => $this->input->post('additional_price')
				);
			
			if ($this->attribute_detail->save($attribute_detail_data,$attribute_detail_id)) {
				$this->session->set_flashdata('success','Attribute Detail is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('attributes/view_detail/' . $this->input->post('header_id')));
			
		}
		
		$data['attributes_detail'] = $this->attribute_detail->get_info($attribute_detail_id);
		$content['content'] = $this->load->view('attributes/edit_detail',$data,true);		
		$this->load_template($content);
	}
	
	function delete($attribute_header_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('delete');
		}
		
		if($this->attribute_header->delete($attribute_header_id)) {
			$this->attribute_detail->delete_by_header($attribute_header_id);
			$this->session->set_flashdata('success','The attribute is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('attributes'));
	}
	
	function delete_detail($attribute_detail_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('delete');
		}
		
		$header_id = $this->attribute_detail->get_info($attribute_detail_id)->header_id;
		
		if($this->attribute_detail->delete($attribute_detail_id)) {
			$this->session->set_flashdata('success','The attribute detail is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('attributes/view_detail/' . $header_id));
	}
	
	function search()
	{
		$search_term = $this->searchterm_handler(array(
			"searchterm"=>$this->input->post('searchterm')
		));
		$data = $search_term;
		
		
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('attributes/search');
		$pag['total_rows'] = $this->attribute_header->count_all_by_search($this->get_current_shop()->id,$search_term);
		
		$data['attributes_header'] = $this->attribute_header->get_all_by_search($this->get_current_shop()->id, $search_term,$pag['per_page'],$this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('attributes/search',$data,true);		
		
		$this->load_template($content);
		
	}
	
	function searchterm_handler($searchterms = array())
	{
		$data = array();
		
		if ($this->input->server('REQUEST_METHOD')=='POST') {
			foreach ($searchterms as $name=>$term) {
				if ($term && trim($term) != " ") {
					$this->session->set_userdata($name,$term);
					$data[$name] = $term;
				} else {
					$this->session->unset_userdata($term);
					$data[$name] = "";
				}
			}
		} else {
			foreach ($searchterms as $name=>$term) {
				if ($this->session->userdata($name)) {
					$data[$name] = $this->session->userdata($name);
				} else { 
					$data[$name] = "";
				}
			}
		}
		return $data;
	}
	
	function search_detail($attribute_header_id=0)
	{
		$search_term = $this->searchterm_handler(array(
			"searchterm"=>$this->input->post('searchterm')
		));
		$data = $search_term;
	
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('attributes/search');
		$pag['total_rows'] = $this->attribute_detail->count_all_by_search($attribute_header_id,$this->get_current_shop()->id,$search_term);
		
		$data['attributes_detail'] = $this->attribute_detail->get_all_by_search($attribute_header_id,$this->get_current_shop()->id, $search_term,$pag['per_page'],$this->uri->segment(4));
		$data['pag'] = $pag;
		
		$data['attribute_header_id'] = $attribute_header_id;
		$content['content'] = $this->load->view('attributes/search_detail',$data,true);		
		
		$this->load_template($content);
		
	}
	
	function exists($shop_id=0)
	{
		$name = $_REQUEST['name'];
		$item_id = $_REQUEST["item_id"];
		
		if (strtolower($this->attribute_header->get_info($attribute_id)->name) == strtolower($name)) {
			echo "true";
		} else if ($this->attribute_header->exists(array('name'=>$_REQUEST['name'],'item_id' => $item_id,'shop_id' => $shop_id))) {
			echo "false";
		} else {
			echo "true";
		}
	}
	
	function exists_detail($shop_id=0)
	{
		$name = $_REQUEST['name'];
		$header_id = $_REQUEST["header_id"];
		
		if (strtolower($this->attribute_header->get_info($attribute_id)->name) == strtolower($name)) {
			echo "true";
		} else if ($this->attribute_detail->exists(array('name'=>$_REQUEST['name'],'shop_id' => $shop_id, 'header_id'=>$header_id))) {
			echo "false";
		} else {
			echo "true";
		}
	}
	
}
?>