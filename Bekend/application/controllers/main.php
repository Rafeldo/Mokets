<?php
/**
 * @author fokhwar
 */
class Main extends CI_Controller
{
	function __construct($module_name = null)
	{
		//$this->load->library('session');
		parent::__construct();
		//If user has no permission, redirect to login.
		if (!$this->user->is_logged_in()) {
			redirect(site_url('login'));
		}
		
		//If user has no permission  for this module, redirect to login.
		if ( $module_name != NO_ACCESS_CONTROL ) {
			if (!$this->user->has_permission($module_name, $this->user->get_logged_in_user_info()->user_id)) {
				redirect(site_url('login'));
			}
		}
		
		//load global data
		$logged_in_user_info = $this->user->get_logged_in_user_info();
		$data['module_groups'] = $this->module->get_groups_info();
		$data['allowed_modules'] = $this->module->get_allowed_modules($logged_in_user_info->user_id);
		$data['allowed_accesses'] = $this->role->get_allowed_accesses($logged_in_user_info->role_id);
		$data['user_info'] = $logged_in_user_info;
		$this->load->vars($data);
	}

	function check_access($action_id = null)
	{
		//If user has no permission,kick off.
		if (!$this->user->is_logged_in()) {
			redirect(site_url('login'));
		}
		
		//If user has no permission  for this module,kick off.
		if (!$this->user->has_access($action_id, $this->user->get_logged_in_user_info()->role_id)) {
			redirect(site_url('dashboard'));
		}
		
		return true;
	}
	
	function load_template($content,$sidebar=true,$edit_mode=false) 
	{
		$template_name = $this->config->item('template');
		$data['content'] = $content;
		$data['sidebar'] = $sidebar;
		$data['edit_mode'] = $edit_mode;
		$this->load->view("templates/". $template_name ."/index", $data);
	}
	
	function get_current_shop()
	{
		return $this->shop->get_current_shop();
	}
}
?>