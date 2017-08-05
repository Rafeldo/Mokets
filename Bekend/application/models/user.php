<?php
class User extends Base_Model
{
	protected $table_name;
	protected $module_table_name;
	protected $permission_table_name;
	protected $role_access_table_name;
	
	function __construct()
	{
		parent::__construct();
		$this->table_name = "be_users";
		$this->module_table_name = "be_modules";
		$this->permission_table_name = "be_permissions";
		$this->role_access_table_name = "be_role_access";
	}
	
	/**
	 * exists return true if the user_id is already existed
	 * in the users table
	 * 
	 * @param array user_data
	 * @return bool
	 */
	function exists($user_data)
	{
		$this->db->from($this->table_name);
		
		if (isset($user_data['user_id'])) {
			$this->db->where('user_id',$user_data['user_id']);
		}
		
		if (isset($user_data['user_name'])) {
			$this->db->where('user_name',$user_data['user_name']);
		}
		
		$query = $this->db->get();
		return ($query->num_rows()==1);
	}
	
	/**
	 * Save function creates/updates the user data to users table.
	 * If the user_id is already exist in the users table,update user data
	 * else, the function will create new data row
	 * 
	 * @param ref array $user_data
	 * @param int $user_id
	 * @return bool
	 */
	function save(&$user_data, $permission_data, $user_id=false)
	{
		$this->db->trans_start();
		$success = false;
		//if there is no data with this id, create new
		if (!$user_id && !$this->exists(array('user_id'=>$user_id))) {
			if ($this->db->insert($this->table_name,$user_data)) {
				$user_data['user_id']= $user_id = $this->db->insert_id();
				$success = true;
			}
		} else {
			//else update the data
			$this->db->where('user_id',$user_id);
			$success = $this->db->update($this->table_name,$user_data);
		}
		
		//Insert permission
		if ($success) {
			//First lets clear out any permission the users currently has.
			$success = $this->db->delete($this->permission_table_name,array('user_id'=>$user_id));
			
			//Now insert the new permission
			if ($success) {
				foreach ($permission_data as $module) {
					$success = $this->db->insert($this->permission_table_name,
													array(
														'module_id'=>$module,
														'user_id'=>$user_id));
				}
			}
		}
		$this->db->trans_complete();
		return $success;
	}
	
	function update_profile($user_data, $user_id)
	{
		$this->db->where('user_id',$user_id);
		$success = $this->db->update($this->table_name,$user_data);
		
		return $success;
	}
	
	/**
	 * get_all function returns the array of user object
	 * 
	 * @param int $limit
	 * @param int $offset
	 * @return user object array
	 */
	function get_all($limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('status',1);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by('added','desc');
		return $this->db->get();
	}
	
	/**
	 * get_info return the user object according to the user_id
	 * 
	 * @param int user_id
	 * @return user object
	 */
	function get_info($user_id)
	{
		$query = $this->db->get_where($this->table_name,array('user_id'=>$user_id));
		
		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}
	
	/**
	 * get_multiple_info function returns array of user object 
	 * according to the user_id form user_id list
	 * 
	 * @param array user_ids
	 * @return user object
	 */
	function get_multiple_info($user_ids)
	{
		$this->db->from($this->table_name);
		$this->db->where_in($user_ids);
		return $this->db->get();
	}
	
	/**
	 * get_info return the user object according to the user_id
	 * 
	 * @param int user_id
	 * @return user object
	 */
	function get_info_by_email($email)
	{
		$query = $this->db->get_where($this->table_name,array('user_email'=>$email));
		
		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}
	
	/**
	 * Count all returns the total number of users in users table.
	 * 
	 * @return int
	 */
	function count_all()
	{
		$this->db->from($this->table_name);
		$this->db->where('status',1);
		return $this->db->count_all_results();
	}
	
	function count_all_by($conditions=array())
	{
		$this->db->from($this->table_name);
		
		if (isset($conditions['searchterm'])) {
			$this->db->like('user_name',$conditions['searchterm']);
		}
			
		$this->db->where('status',1);
		return $this->db->count_all_results();
	}
	
	function get_all_by($conditions=array(), $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		
		if (isset($conditions['searchterm'])) {
			$this->db->like('user_name',$conditions['searchterm']);
		}
			
		$this->db->where('status',1);
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by('added','desc');
		return $this->db->get();
	}
	
	/**
	 * Delete function update 1 to deleted fields from users table
	 * according to the user id
	 * 
	 * @param int $user_id
	 * @return bool
	 */
	function delete($user_id)
	{
		$success = false;
		
		//Don't let user deleted their self
		if ($user_id==$this->get_logged_in_user_info()->user_id) {
			return false;
		}
		
		//Delete Permissions
		if ($this->db->delete($this->permission_table_name, array('user_id'=>$user_id))) {
			$this->db->where('user_id',$user_id);
			$success = $this->db->update($this->table_name,array('deleted'=>1));
		}
		
		$this->db->where('user_id',$user_id);
		$success = $this->db->update($this->table_name,array('status'=>0));
		
		return $success;
	}
	
	/**
	 * Delete list function update 1 to all fields from user table
	 * relating to the user ids of user_ids list
	 * 
	 * @param array user_ids
	 * @return bool
	 */
	function delete_list($user_ids)
	{
		$success = false;
		
		//don't let employee delete their self
		if (in_array($this->get_logged_in_user_info()->user_id,$user_ids)) {
			return false;
		}
		
		//Delete Permissions
		$this->db->where_in('user_id',$user_ids);
		if ($this->db->delete($this->permission_table_name)) {
			//delete from user talbe
			$this->db->where_in('user_id',$user_ids);
			$success = $this->db->update($this->table_name,array('status'=>0));
		}
		return $success;
	}
	
	/**
	 * Login function check the user and set session.
	 * 
	 * @param string user_name
	 * @param string user_password
	 * @return bool
	 */
	function login($user_name, $user_pass)
	{
		$query = $this->db->get_where($this->table_name,array('user_name'=>$user_name,'user_pass'=>md5($user_pass),'status'=>1),1);
		if ($query->num_rows()==1) {
			$row = $query->row();
			$this->session->set_userdata('user_id',$row->user_id);
			$this->session->set_userdata('is_owner',$row->is_owner);
			$this->session->set_userdata('is_shop_admin',0);
			$this->session->set_userdata('role_id',$row->role_id); 
			$this->session->set_userdata('allow_shop_id',$row->shop_id);
			if($row->is_shop_admin) {
				$this->session->set_userdata('allow_shop_id',$row->shop_id);
				$this->session->set_userdata('is_shop_admin',true);
			}
			
			return true;
		}
		return false;
	}
	
	/**
	 * Log Out a user by destroying all session dta and redirect to login
	 * 
	 */
	function logout()
	{
		$this->session->sess_destroy();
		redirect(site_url('login'));
	}
	
	/**
	 * is_logged_in determines if a employee is logged in
	 */
	function is_logged_in()
	{
		return $this->session->userdata('user_id')!=false;
	}
	
	/**
	 * Gets information about the currently logged in user.
	 * 
	 * @return bool
	 */
	function get_logged_in_user_info()
	{
		if ($this->is_logged_in()) {
			return $this->get_info($this->session->userdata('user_id'));
		}
		return false;
	}
	
	/**
	 * Has-permission determine whether the user has access for module
	 * 
	 * @return bool
	 */
	function has_permission($module_id, $user_id)
	{
		///if no module id is null,allow access
		if ($module_id==null) {
			return true;
		}
		$this->db->from($this->permission_table_name);
		$this->db->where('user_id',$user_id);
		$this->db->join($this->module_table_name, 
			$this->module_table_name .".module_id = ". $this->permission_table_name .".module_id");
		$this->db->where($this->module_table_name .'.module_name', $module_id);
		$query = $this->db->get();
		
		return $query->num_rows() ==1;
	}
		
	/**
	 * has_access determine whether the user has access for action
	 */
	function has_access($action_id, $role_id)
	{
		//if action is null, allow access
		if ($action_id == null) {
			return true;
		}
		
		$this->db->from($this->role_access_table_name);
		$this->db->where('role_id',$role_id);
		$this->db->where('action_id',$action_id);
		$query = $this->db->get();
		
		return $query->num_rows() == 1;
	}
}
?>