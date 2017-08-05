<?php
require_once('main.php');

class Sub_Categories extends Main
{
	function __construct()
	{
		parent::__construct('sub_categories');
		$this->load->library('uploader');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
	
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('sub_categories/index');
		
		$pag['total_rows'] = $this->sub_category->count_all($this->get_current_shop()->id);
		$data['sub_categories'] = $this->sub_category->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('sub_categories/view',$data,true);		
		
		$this->load_template($content);
	}
	
	function add()
	{
		if(!$this->session->userdata('is_shop_admin')) {
		      $this->check_access('add');
		}
		if ($this->input->server('REQUEST_METHOD')=='POST') {			
			$upload_data = $this->uploader->upload($_FILES);
			
			if (!isset($upload_data['error'])) {
				$sub_category_data = array(
					'name' => htmlentities($this->input->post('name')),
					'ordering' => htmlentities($this->input->post('ordering')),
					'shop_id' => $this->get_current_shop()->id,
					'cat_id' => htmlentities($this->input->post('cat_id')),
					'is_published' => 1
				);
				
				if ($this->sub_category->save($sub_category_data)) {
					foreach ($upload_data as $upload) {
						$image = array(
							'parent_id'=>$sub_category_data['id'],
							'type' => 'sub_category',
							'description' => "",
							'path' => $upload['file_name'],
							'width'=>$upload['image_width'],
							'height'=>$upload['image_height']
						);
						$this->image->save($image);
					}		
					$this->session->set_flashdata('success','Sub Category is successfully added.');
				} else {
					$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
				}
				
				redirect(site_url('sub_categories'));
			} else {
				$data['error'] = $upload_data['error'];
			}
		}
		
		if($this->category->count_all($this->get_current_shop()->id) > 0){
			$content['content'] = $this->load->view('sub_categories/add', array(), true);
			$this->load_template($content);
		} else {
			$this->session->set_flashdata('success','Oops! Please create a category first before you create any sub category.');
			redirect(site_url('sub_categories'));
		}
		
		
	}
	
	function search()
	{
		$search_term = $this->searchterm_handler(htmlentities($this->input->post('searchterm')));
		
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('sub_categories/search');
		$pag['total_rows'] = $this->sub_category->count_all_by($this->get_current_shop()->id, array('searchterm'=>$search_term));
		
		$data['searchterm'] = $search_term;
		$data['sub_categories'] = $this->sub_category->get_all_by(
												$this->get_current_shop()->id, 
												array('searchterm'=>$search_term),
												$pag['per_page'],
												$this->uri->segment(3)
											);
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('sub_categories/search',$data,true);		
		
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
	
	function edit($sub_category_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
	
		if ($this->input->server('REQUEST_METHOD')=='POST') {
			$sub_category_data = array(
				'name' => htmlentities($this->input->post('name')),
				'ordering' => htmlentities($this->input->post('ordering')),
				'cat_id' => htmlentities($this->input->post('cat_id'))
			);
			
			if($this->sub_category->save($sub_category_data, $sub_category_id)) {
				$this->session->set_flashdata('success','Sub Category is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('sub_categories'));
		}
		
		$data['sub_category'] = $this->sub_category->get_info($sub_category_id);
		
		$content['content'] = $this->load->view('sub_categories/edit',$data,true);		
		
		$this->load_template($content);
	}
	
	function publish($sub_category_id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('publish');
		}
		
		$sub_category_data = array(
			'is_published'=> 1
		);
			
		if ($this->sub_category->save($sub_category_data, $sub_category_id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	function unpublish($sub_category_id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('publish');
		}
		
		$sub_category_data = array(
			'is_published'=> 0
		);
		
		if ($this->sub_category->save($sub_category_data, $sub_category_id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	function delete($sub_category_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		if($this->sub_category->delete($sub_category_id)) {
			
			unlink('./uploads/'.$this->image->get_info_parent_type($sub_category_id,'sub_category')->path);
			unlink('./uploads/thumbnail/'.$this->image->get_info_parent_type($sub_category_id,'sub_category')->path);
			$this->image->delete_by_parent($sub_category_id);
		
			$this->session->set_flashdata('success','The sub category is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('sub_categories'));
	}
	
	function delete_items($sub_category_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		if ($this->sub_category->delete($sub_category_id)) {
			if ($this->item->delete_by_sub_cat($sub_category_id)) {
				$this->session->set_flashdata('success','The sub category is successfully deleted.');
			} else {
				$this->session->set_flashdata('error','Database error occured in items.Please contact your system administrator.');
			}
		} else {
			$this->session->set_flashdata('error','Database error occured in categories.Please contact your system administrator.');
		}
		redirect(site_url('sub_categories'));
	}
	
	function delete_image($sub_category_id,$image_id,$image_name)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		if ($this->image->delete($image_id)) {
			unlink('./uploads/'.$image_name);
			unlink('./uploads/thumbnail/'.$image_name);
			$this->session->set_flashdata('success','Category cover photo is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('sub_categories/edit/' . $sub_category_id));
	}
	
	function exists($shop_id=0)
	{
		$name = $_REQUEST['name'];
		
		if (strtolower($this->sub_category->get_info($sub_category_id)->name) == strtolower($name)) {
			echo "true";
		} else if ($this->sub_category->exists(array('name'=>$_REQUEST['name'],'shop_id' => $shop_id))) {
			echo "false";
		} else {
			echo "true";
		}
		
	}
	
	function upload($sub_category_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		$upload_data = $this->uploader->upload($_FILES);
		
		if (!isset($upload_data['error'])) {
	
			unlink('./uploads/'.$this->image->get_info_parent_type($sub_category_id,'sub_category')->path);
			unlink('./uploads/thumbnail/'.$this->image->get_info_parent_type($sub_category_id,'sub_category')->path);
			$this->image->delete_by_parent($sub_category_id,'sub_category');
			
			foreach ($upload_data as $upload) {
				$image = array(
					'parent_id'=> $sub_category_id,
					'type' => 'sub_category',
					'description' => "",
					'path' => $upload['file_name'],
					'width'=>$upload['image_width'],
					'height'=>$upload['image_height']
				);
				$this->image->save($image);
				redirect(site_url('sub_categories/edit/' . $sub_category_id));
			}
			
		} else {
			$data['error'] = $upload_data['error'];
		}
		
		$data['sub_category'] = $this->sub_category->get_info($sub_category_id);
		
		$content['sub_content'] = $this->load->view('sub_categories/edit',$data,true);		
		$this->load_template($content);
	}
}
?>