<?php 
class Role extends Base_Model
{
	protected $table_name;
	protected $role_access_table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = "be_roles";
		$this->role_access_table_name = "be_role_access";
	}

	function get_name($role_id)
	{
		$query = $this->db->get_where($this->table_name,array('role_id'=>$role_id));
		
		if ($query->num_rows()==1) {
			$row = $query->row();
			return $row->role_desc;
		}
		
		return "Unknown Module";
	}

	function get_all()
	{
		return $this->db->get($this->table_name);
	}
	
	function get_info_by_name($module_name)
	{
		$query = $this->db->get_where($this->table_name,array('role_name'=>$role_name));
		
		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}

	function get_allowed_accesses($role_id)
	{
		$query = $this->db->get_where($this->role_access_table_name, array('role_id'=>$role_id));
		$accesses = array();
		foreach ($query->result() as $access) {
			$accesses[] = $access->action_id;
		}
		
		return $accesses;
	}
}
?>