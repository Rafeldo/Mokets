<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Fileupload extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function upload()
    {
 		session_start();
     	error_reporting(E_ALL | E_STRICT);
     	$this->load->library("UploadHandler");
    }
}
?>