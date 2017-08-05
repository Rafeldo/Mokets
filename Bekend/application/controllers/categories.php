<?php
require_once('main.php');
class Categories extends Main
{
	function __construct()
	{
		parent::__construct('categories');
		$this->load->library('uploader');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
	
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('categories/index');
		$pag['total_rows'] = $this->category->count_all($this->get_current_shop()->id);
		
		$data['categories'] = $this->category->get_all($this->get_current_shop()->id, $pag['per_page'],$this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('categories/view',$data,true);		
		
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
					$category_data = array(
					'name' => htmlentities($this->input->post('name')),
					'ordering' => htmlentities($this->input->post('ordering')),
					'shop_id' => $this->get_current_shop()->id,
					'is_published' => 1
				);
				
				if ($this->category->save($category_data)) {
					foreach ($upload_data as $upload) {
						$image = array(
							'parent_id'=>$category_data['id'],
							'type' => 'category',
							'description' => "",
							'path' => $upload['file_name'],
							'width'=>$upload['image_width'],
							'height'=>$upload['image_height']
						);
						$this->image->save($image);
					}
								
					$this->session->set_flashdata('success','Category is successfully added.');
				} else {
					$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
				}
				
				redirect(site_url('categories'));
			} else {
				$data['error'] = $upload_data['error'];
			}
		}
		
		$content['content'] = $this->load->view('categories/add',array(),true);		

		$this->load_template($content);
	}
	
	function search()
	{
		$search_term = $this->searchterm_handler(htmlentities($this->input->post('searchterm')));
		
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('categories/search');
		$pag['total_rows'] = $this->category->count_all_by($this->get_current_shop()->id, array('searchterm'=>$search_term));
		
		$data['searchterm'] = $search_term;
		$data['categories'] = $this->category->get_all_by(
			$this->get_current_shop()->id, 
			array('searchterm'=>$search_term),
			$pag['per_page'],
			$this->uri->segment(3)
		);
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('categories/search', $data, true);	
		
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
	
	function edit($category_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
	
		if ($this->input->server('REQUEST_METHOD')=='POST') {
			$category_data = array(
				'name' => htmlentities($this->input->post('name')),
				'ordering' => htmlentities($this->input->post('ordering')),
				'shop_id' => $this->get_current_shop()->id
			);
			
			if($this->category->save($category_data, $category_id)) {
				$this->session->set_flashdata('success','Category is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('categories'));
		}
		
		$data['category'] = $this->category->get_info($category_id);
		
		$content['content'] = $this->load->view('categories/edit',$data,true);		
		
		$this->load_template($content);
	}
	
	function publish($category_id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('publish');
		}
		
		$category_data = array(
			'is_published'=> 1
		);
			
		if ($this->category->save($category_data, $category_id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	function unpublish($category_id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('publish');
		}
		
		$category_data = array(
			'is_published'=> 0
		);
		
		if ($this->category->save($category_data,$category_id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	function delete($category_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		if($this->category->delete($category_id)) {
			
			unlink('./uploads/'.$this->image->get_info_parent_type($category_id,'category')->path);
			unlink('./uploads/thumbnail/'.$this->image->get_info_parent_type($category_id,'category')->path);
			$this->image->delete_by_parent($category_id, 'category');
		
			$this->session->set_flashdata('success','The category is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('categories'));
	}

	function delete_items($category_id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		if ($this->category->delete($category_id)) {
			unlink('./uploads/'.$this->image->get_info_parent_type($category_id,'category')->path);
			unlink('./uploads/thumbnail/'.$this->image->get_info_parent_type($category_id,'category')->path);
			$this->image->delete_by_parent($category_id, 'category');
			
			if($this->delete_sub_categories($category_id)){
				if ($this->delete_items_images($category_id)) {
					$this->session->set_flashdata('success','The category is successfully deleted.');
				} else {
					$this->session->set_flashdata('error','Database error occured in items.Please contact your system administrator.');
				}
			} else {
				$this->session->set_flashdata('error','Database error occured in sub categories.Please contact your system administrator.');
			}
		} else {
			$this->session->set_flashdata('error','Database error occured in categories.Please contact your system administrator.');
		}
		redirect(site_url('categories'));
	}
	
	function delete_sub_categories($category_id)
	{
		$sub_cats = $this->sub_category->get_all_by_cat($category_id);
		
		foreach ($sub_cats->result() as $sub_cat) {
			unlink('./uploads/'.$this->image->get_info_parent_type($sub_cat->id,'sub_category')->path);
			unlink('./uploads/thumbnail/'.$this->image->get_info_parent_type($sub_cat->id,'sub_category')->path);
			$this->image->delete_by_parent($sub_cat->id, 'sub_category');
		}
		
		$this->sub_category->delete_by_cat($category_id);
		
		return true;
	}
	
	function delete_items_images($category_id)
	{
		$items = $this->item->get_all_by_cat($category_id);
		
		foreach ($items->result() as $item) {
			$images = $this->image->get_all_by_type($item->id, 'item');
			foreach ($images->result() as $image) {
				unlink('./uploads/'.$image->path);
			}
		
			$this->image->delete_by_parent($item->id, 'item');
		}
		$this->item->delete_by_cat($category_id);
		
		return true;
	}
	
	function delete_image($category_id, $image_id, $image_name)
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
		redirect(site_url('categories/edit/' . $category_id));
	}
	
	function exists($shop_id=0)
	{
		$name = $_REQUEST['name'];
		if (strtolower($this->category->get_info($category_id)->name) == strtolower($name)) {
			echo "true";
		} else if ($this->category->exists(array('name'=>$_REQUEST['name'],'shop_id' => $shop_id))) {
			echo "false";
		} else {
			echo "true";
		}
	}
	
	function upload($category_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		$upload_data = $this->uploader->upload($_FILES);
		
		if (!isset($upload_data['error'])) {
	
			unlink('./uploads/'.$this->image->get_info_parent_type($category_id,'category')->path);
			unlink('./uploads/thumbnail/'.$this->image->get_info_parent_type($category_id,'category')->path);
			$this->image->delete_by_parent($category_id,'category');
			
			foreach ($upload_data as $upload) {
				$image = array(
					'parent_id'=> $category_id,
					'type' => 'category',
					'description' => "",
					'path' => $upload['file_name'],
					'width'=>$upload['image_width'],
					'height'=>$upload['image_height']
				);
				$this->image->save($image);
				redirect(site_url('categories/edit/' . $category_id));
			}
			
		} else {
			$data['error'] = $upload_data['error'];
		}
		
		$data['category'] = $this->category->get_info($category_id);
		
		$content['content'] = $this->load->view('categories/edit',$data,true);		
		$this->load_template($content);
	}
}
?>