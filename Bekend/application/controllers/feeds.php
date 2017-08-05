<?php
require_once('main.php');
class Feeds extends Main
{
	function __construct()
	{
		parent::__construct('feeds');
		$this->load->library('uploader');
	}
	
	function index()
	{
		$this->session->unset_userdata('searchterm');
		
		$pag = $this->config->item('pagination');
		$pag['base_url'] = site_url('feeds/index');
		$pag['total_rows'] = $this->feed->count_all($this->get_current_shop()->id);
		
		$data['feeds'] = $this->feed->get_all($this->get_current_shop()->id, $pag['per_page'], $this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('feeds/view',$data,true);		
		
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

			$feed_data = array();
			foreach ( $this->input->post() as $key=>$value) {
				$feed_data[$key] = htmlentities($value);
			}

			$feed_data['shop_id'] = $this->get_current_shop()->id;
			$feed_data['is_published'] = 1;
			if ($this->feed->save($feed_data)) {			
				$this->session->set_flashdata('success','Feed is successfully added.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			
			if ($action == "gallery") {
				redirect(site_url('feeds/gallery/'.$feed_data['id']));
			} else {
				redirect(site_url('feeds'));
			}
		}
		
		$content['content'] = $this->load->view('feeds/add',array(),true);
		
		$this->load_template($content);
	}
		
	function search()
	{
		$search_term = $this->searchterm_handler(array(
			"searchterm"=>htmlentities($this->input->post('searchterm'))
		));
		$data = $search_term;
		
		$pag = $this->config->item('pagination');
		
		$pag['base_url'] = site_url('feeds/search');
		$pag['total_rows'] = $this->feed->count_all_by($this->get_current_shop()->id, $search_term);
		
		$data['feeds'] = $this->feed->get_all_by($this->get_current_shop()->id, $search_term,$pag['per_page'],$this->uri->segment(3));
		$data['pag'] = $pag;
		
		$content['content'] = $this->load->view('feeds/search',$data,true);		
		
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
	
	function edit($feed_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		if ($this->input->server('REQUEST_METHOD')=='POST') {

			$feed_data = array();
			foreach ( $this->input->post() as $key=>$value) {
				$feed_data[$key] = htmlentities($value);
			}

			if ( $this->feed->save( $feed_data, $feed_id )) {
				$this->session->set_flashdata('success','Feed is successfully updated.');
			} else {
				$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
			}
			redirect(site_url('feeds'));
		}
		
		$data['feed'] = $this->feed->get_info($feed_id);
		
		$content['content'] = $this->load->view('feeds/edit',$data,true);		
		
		$this->load_template($content);
	}
	
	function gallery($id)
	{
		session_start();
		$_SESSION['parent_id'] = $id;
		$_SESSION['type'] = 'feed';
    	$content['content'] = $this->load->view('feeds/gallery', array('id' => $id), true);
    	
    	$this->load_template($content);
	}
	
	function upload($feed_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		$upload_data = $this->uploader->upload($_FILES);
		
		if (!isset($upload_data['error'])) {
			foreach ($upload_data as $upload) {
				$image = array(
								'item_id'=> $feed_id,
								'type' => 'feed',
								'path' => $upload['file_name'],
								'width'=>$upload['image_width'],
								'height'=>$upload['image_height']
							);
				$this->image->save($image);
			}
		} else {
			$data['error'] = $upload_data['error'];
		}
		
		$data['feed'] = $this->feed->get_info($feed_id);
		
		$content['content'] = $this->load->view('feeds/edit',$data,true);		
		
		$this->load_template($content);
	}
	
	function publish($id = 0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
			$this->check_access('publish');
		}
		
		$feed_data = array(
			'is_published'=> 1
		);
			
		if ($this->feed->save($feed_data,$id)) {
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
		
		$feed_data = array(
			'is_published'=> 0
		);
			
		if ($this->feed->save($feed_data,$id)) {
			echo 'true';
		} else {
			echo 'false';
		}
	}

	function delete($feed_id=0)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		     $this->check_access('delete');
		}
		
		$images = $this->image->get_all_by_type($feed_id, 'feed');
		foreach ($images->result() as $image) {
			$this->image->delete($image->id);
			unlink('./uploads/'.$image->path);
		}
		
		if ($this->feed->delete($feed_id)) {
			$this->session->set_flashdata('success','Feed is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('feeds'));
	}
	
	function delete_image($feed_id,$image_id,$image_name)
	{
		if(!$this->session->userdata('is_shop_admin')) {
		    $this->check_access('edit');
		}
		
		if ($this->image->delete($image_id)) {
			unlink('./uploads/'.$image_name);
			$this->session->set_flashdata('success','Image is successfully deleted.');
		} else {
			$this->session->set_flashdata('error','Database error occured.Please contact your system administrator.');
		}
		redirect(site_url('feeds/edit/'.$feed_id));
	}
}
?>