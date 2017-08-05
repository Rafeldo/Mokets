<?php  
class Code extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = "mk_codes";
	}

	function exists($data)
	{
		$this->db->from($this->table_name);
		
		if (isset($data['code'])) {
			$this->db->where('code',$data['code']);
		}
		
		if (isset($data['user_id'])) {
			$this->db->where('user_id',$data['user_id']);
		}
		
		$query = $this->db->get();
		return ($query->num_rows()==1);
	}

	function save(&$data,$user_id=false)
	{
		if (!!$user_id && !$this->exists(array('user_id'=>$user_id))) {
			if ($this->db->insert($this->table_name,$data)) {
				$data['id'] = $this->db->insert_id();
				return true;
			}
		} else {
			$this->db->where('user_id',$user_id);
			return $this->db->update($this->table_name,$data);
		}
		
		return false;
	}

	function get_by_code($code)
	{
		$query = $this->db->get_where($this->table_name,array('code'=>$code));
		
		if ($query->num_rows()==1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}

	function delete($user_id)
	{
		$this->db->where('user_id',$user_id);
		return $this->db->delete($this->table_name);
	}
}
?>