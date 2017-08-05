<?php
class Uploader extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('file');
		
		$this->load->library('upload');
					
		$config['upload_path'] = './uploads';
		$config['allowed_types'] = '*';
		$config['overwrite'] = FALSE;
		
		$this->upload->initialize($config);
		$this->load->library('image_lib');
	}
	
	function upload($files,$userId=0,$type="")
	{
		$data = array();
		
		
		foreach ($files as $field=>$file) {
	      	if ($userId==0) {	
	      		//$_FILES[$field]['name'] = time().$_FILES[$field]['name'];
	      		$_FILES[$field]['name'] = $_FILES[$field]['name'];
	      	} else {
      		//$temp = explode(".", $_FILES[$field]["name"]);
      		//$extension = end($temp);
      		$extension = '.jpg';
      		$_FILES[$field]['name'] = $userId . "-" . $type . $extension;
      		
      		if (file_exists(".uploads/".$_FILES[$field]['name'])) { 
      		   unlink(".uploads/".$_FILES[$field]['name']);
      		}
      	}
      	
      	
      	// No problems with the file
      	if ($file['error'] == 0) {	
        		// So lets upload
        		
        		if ($this->upload->do_upload($field)) {
            	$data[] = $this->upload->data();
            	
            	//createing thumbnails
            	$this->image_lib->clear();
            	$image_data = $this->upload->data();
            	$config = array(
            				'source_image' => $image_data['full_path'],
            				'new_image' => './uploads/thumbnail',
            				'maintain_ration' => true,
            				'width' => 150,
            				'height' => 100
            			);
            	$this->image_lib->initialize($config);
            	$this->image_lib->resize();
        		} else {
            		$data['error'] = $this->upload->display_errors();
        		}
      	}
      }
      
      return $data;
	}
}
?>