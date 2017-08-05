<?php
class Appuser extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_appusers';
	}
	
	function exists($data)
	{
		$this->db->from($this->table_name);
		
		if (isset($data['id'])) {
			$this->db->where('id',$data['id']);
		}
		
		if (isset($data['email'])) {
			$this->db->where('email',$data['email']);
		}
		
		$query = $this->db->get();
		return ($query->num_rows()==1);
	}
	
	function save(&$data, $id=false)
	{
		//if there is no data with this id, create new
		if (!$id && !$this->exists(array('id'=>$id))) {
			if ($this->db->insert($this->table_name,$data)) {
				$data['id'] = $this->db->insert_id();
				return true;
			}
		} else {
			//else update the data
			$this->db->where('id',$id);
			return $this->db->update($this->table_name,$data);
		}
		
		return $false;
	}
	
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
	
	function get_info($id)
	{
		$query = $this->db->get_where($this->table_name,array('id'=>$id));
		
		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}
	
	function get_info_by_email($email)
	{
		$query = $this->db->get_where($this->table_name,array('email'=>$email));
		
		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}
	
	function get_multiple_info($user_ids)
	{
		$this->db->from($this->table_name);
		$this->db->where_in($user_ids);
		return $this->db->get();
	}

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
			$this->db->like('username',$conditions['searchterm']);
			$this->db->or_like('username',$conditions['searchterm']);
		}
			
		$this->db->where('status',1);
		return $this->db->count_all_results();
	}
	
	function get_all_by($conditions=array(),$limit=false,$offset=false)
	{
		$this->db->from($this->table_name);
		
		if (isset($conditions['searchterm'])) {
			$this->db->like('username',$conditions['searchterm']);
			$this->db->or_like('email',$conditions['searchterm']);
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

	function login($user_name,$user_pass)
	{
		$query = $this->db->get_where($this->table_name,array('email'=>$user_name,'password'=>md5($user_pass),'status'=>1),1);
		if ($query->num_rows()==1) {
			return $query->row();
		}
		return false;
	}
}
?>