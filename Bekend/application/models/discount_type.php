<?php 
class Discount_Type extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_discount_type';
	}
	
	function exists($data)
	{
		$this->db->from($this->table_name);
		
		if (isset($data['id'])) {
			$this->db->where('id',$data['id']);
		}
		
		if (isset($data['name'])) {
			$this->db->where('name',$data['name']);
		}
		
		if (isset($data['shop_id'])) {
			$this->db->where('shop_id',$data['shop_id']);
		}
		
		$query = $this->db->get();
		return ($query->num_rows()==1);
	}

	function save(&$data, $id=false)
	{
		if (!$id && !$this->exists(array('id' => $id, 'shop_id' => $data['shop_id']))) {
			if ($this->db->insert($this->table_name,$data)) {
				$data['id'] = $this->db->insert_id();
				return true;
			}
		} else {
			$this->db->where('id',$id);
			return $this->db->update($this->table_name,$data);
		}		
		return false;
	}

	function get_all($shop_id, $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		return $this->db->get();
	}
	
	function get_info($id)
	{
		$query = $this->db->get_where($this->table_name,array('id' => $id));
		
		if ($query->num_rows() == 1) {
			return $query->row();
		} else {
			return $this->get_empty_object($this->table_name);
		}
	}

	function get_multiple_info($ids)
	{
		$this->db->from($this->table_name);
		$this->db->where_in($ids);
		return $this->db->get();
	}

	function count_all($shop_id)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		return $this->db->count_all_results();
	}
	
	function delete($id)
	{
		$this->db->where('id',$id);
		return $this->db->delete($this->table_name);
 	}

	function delete_by_shop($shop_id)
	{
		$this->db->where('shop_id', $shop_id);
		return $this->db->delete($this->table_name);
 	}
 	
 	function get_discount_by_shop_id($shop_id)
 	{
 		$query = $this->db->get_where($this->table_name,array('shop_id'=> $shop_id));
 		
 		if ($query->num_rows() == 1) {
 			return $query->row();
 		} else {
 			return $this->get_empty_object($this->table_name);
 		}
 	}
 	
 	function count_all_by($shop_id, $conditions=array())
 	{
 		$this->db->from($this->table_name);
 		$this->db->where('shop_id', $shop_id);
 		
 		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
 			$this->db->like('name', $conditions['searchterm']);
 		}
 		return $this->db->count_all_results();
 	}
 	
 	function get_all_by($shop_id, $conditions=array(), $limit=false, $offset=false)
 	{
 		$this->db->from($this->table_name);
 		$this->db->where('shop_id', $shop_id);
 		
 		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
 			$this->db->like('name', $conditions['searchterm']);
 		}
 		
 		if ($limit) {
 			$this->db->limit($limit);
 		}
 		
 		if ($offset) {
 			$this->db->offset($offset);
 		}
 		
 		
 		return $this->db->get();
 	}
}
?>