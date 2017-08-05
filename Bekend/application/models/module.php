<?php
class Module extends Base_Model
{
	protected $table_name;
	protected $permission_table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = "be_modules";
		$this->permission_table_name = "be_permissions";
	}

	function get_name($module_id)
	{
		$query = $this->db->get_where($this->table_name,array('module_id'=>$module_id));
		
		if ($query->num_rows()==1) {
			$row = $query->row();
			return $row->module_name;
		}
		
		return "Unknown Module";
	}

	function get_all()
	{
		return $this->db->get($this->table_name);
	}
	
	function get_info_by_name($module_name)
	{
		$query = $this->db->get_where($this->table_name,array('module_name'=>$module_name));
		
		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}

	function get_allowed_modules($user_id)
	{
		$this->db->from($this->table_name);
		$this->db->join($this->permission_table_name, $this->permission_table_name .".module_id = ". $this->table_name .".module_id");
		$this->db->where($this->permission_table_name .".user_id", $user_id);
		$this->db->order_by($this->table_name .'.ordering');
		return $this->db->get();
	}
	
	function get_groups_info() 
	{
		$this->db->select('group_id, group_name, group_icon, group_has_child');
		$this->db->from($this->table_name);
		$this->db->where('group_id <>',0);
		$this->db->group_by(array("group_id"));
		
		return $this->db->get();
		
	}
}
?>
