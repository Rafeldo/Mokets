<?php
require_once('main.php');

class Items extends Main
{
	function __construct()
	{
		parent::__construct('items');
		$this->load->library('uploader');
	}
	
	function index()
	{
		$this->session->unset_userdata(array(
			"searchterm" => "",
			"sub_cat_id" => "",
			"cat_id" => "",
			"discount_type_id" => ""
		));
	
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('items/index');
		$pag['total_rows'] = $this->item->count_all($this->get_current_shop()->id);
		
		$data['items'] = $this->item->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('items/view', $data, true);		
		
		$this->load_template($content);
	}
	
	function add()
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('add');
		}
		
		$action = "save";
		unset($_POST['save']);
		if (htmlentities($this->input->post('gallery'))) {
			$action = "gallery";
			unset($_POST['gallery']);
		}
		
		if ($this->input->server('REQUEST_METHOD')=='POST') {

			$item_data = array();
			foreach ( $this->input->post() as $key=>$value) {
				$item_data[$key] = htmlentities($value);
			}

			$item_data['shop_id'] = $this->get_current_shop()->id;
			$item_data['is_published'] = 1;
			
			//unset($item_data['cat_id']);
			
			if ($this->item->save($item_data)) {			
				$this->session->set_flashdata('success','Item is successfully added.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			
			if ($action == "gallery") {
				redirect(site_url('items/gallery/'.$item_data['id']));
			} else {
				redirect(site_url('items'));
			}
		}
		
		$cat_count = $this->category->count_all($this->get_current_shop()->id);
		$sub_cat_count = $this->sub_category->count_all($this->get_current_shop()->id);
		
		if($cat_count <= 0 && $sub_cat_count <= 0) {
			$this->session->set_flashdata('error','Oops! Please create the category and sub category first before you create items.');
			redirect(site_url('items'));	
		} else {
			if($cat_count <= 0) {
				$this->session->set_flashdata('error','Oops! Please create the category first before you create items.');
				redirect(site_url('items'));
			} else if ($sub_cat_count <= 0) {
				$this->session->set_flashdata('error','Oops! Please create the sub category first before you create items.');
				redirect(site_url('items'));
			}
		}
		
		$content['content'] = $this->load->view('items/add',array(),true);
		$this->load_template($content);
	}
		
	function search()
	{
		$search_arr = array(
			"searchterm" => htmlentities($this->input->post('searchterm')),
			"sub_cat_id" => htmlentities($this->input->post('sub_cat_id')),
			"cat_id" => htmlentities($this->input->post('cat_id')),
			"discount_type_id" => htmlentities($this->input->post('discount_type_id'))
		);
		
		$search_term = $this->searchterm_handler($search_arr);
		$data = $search_term;
		
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('items/search');
		$pag['total_rows'] = $this->item->count_all_by($this->get_current_shop()->id, $search_term);
		
		$data['items'] = $this->item->get_all_by($this->get_current_shop()->id, $search_term, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('items/search',$data,true);		
		
		$this->load_template($content);
	}

	function searchterm_handler($searchterms = array())
	{
		$data = array();
		
		if ($this->input->server('REQUEST_METHOD') == 'POST') {
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
	
	function edit($item_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		if ($this->input->server('REQUEST_METHOD')=='POST') {

			$item_data = array();
			foreach ( $this->input->post() as $key=>$value) {
				$item_data[$key] = htmlentities($value);
			}
			
			if(!htmlentities($this->input->post('is_published'))) {
				$item_data['is_published'] = 0;
			}
			
			if ($this->item->save($item_data, $item_id)) {
				$this->session->set_flashdata('success','Item is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('items'));
		}
		
		$data['item'] = $this->item->get_info($item_id);
		
		$content['content'] = $this->load->view('items/edit',$data,true);		
		
		$this->load_template($content);
	}
	
	function gallery($id)
	{
		session_start();
		$_SESSION['parent_id'] = $id;
		$_SESSION['type'] = 'item';
    	$content['content'] = $this->load->view('items/gallery', array('id' => $id), true);
    	
    	$this->load_template($content);
	}
	
	function upload($item_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		$upload_data = $this->uploader->upload($_FILES);
		
		if (!isset($upload_data['error'])) {
			foreach ($upload_data as $upload) {
				$image = array(
								'item_id'=>$item_id,
								'path' => $upload['file_name'],
								'width'=>$upload['image_width'],
								'height'=>$upload['image_height']
							);
				$this->image->save($image);
			}
		} else {
			$data['error'] = $upload_data['error'];
		}
		
		$data['item'] = $this->item->get_info($item_id);
		
		$content['content'] = $this->load->view('items/edit',$data,true);		
		
		$this->load_template($content);
	}
	
	function publish($id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('publish');
		}
		
		$item_data = array(
			'is_published'=> 1
		);
			
		if ($this->item->save($item_data, $id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	function unpublish($id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('publish');
		}
		
		$item_data = array(
			'is_published'=> 0
		);
			
		if ($this->item->save($item_data, $id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	function delete($item_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		$images = $this->image->get_all_by_type($item_id, 'item');
		foreach ($images->result() as $image) {
			$this->image->delete($image->id);
			unlink('./uploads/'.$image->path);
		}
		
		if ($this->item->delete($item_id)) {
			$this->attribute_header->delete_by_item($item_id);
			$this->attribute_detail->delete_by_item($item_id);
			$this->session->set_flashdata('success','The item is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('items'));
	}

	function delete_image($item_id, $image_id, $image_name)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		if ($this->image->delete($image_id)) {
			unlink('./uploads/'.$image_name);
			$this->session->set_flashdata('success','The image is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('items/edit/'.$item_id));
	}
	
	function get_sub_cats($cat_id) 
	{
		$sub_categories = $this->sub_category->get_all_by_cat_id($cat_id);
		echo json_encode($sub_categories->result());
	}
	
	function exists($item_id = 0)
	{
		$name = trim($_REQUEST['name']);
		$cat_id = $_REQUEST['cat_id'];
		$sub_cat_id = $_REQUEST['sub_cat_id'];
		
		if (trim(strtolower($this->item->get_info($item_id)->name)) == strtolower($name)) {
			echo "true";
		} else if($this->item->exists(array(
			'name'=> $name,
			'sub_cat_id' => $sub_cat_id
		))) {
			echo "false";
		} else {
			echo "true";
		}
	}
}
?>