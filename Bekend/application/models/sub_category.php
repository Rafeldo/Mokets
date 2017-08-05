<?php 
class Sub_Category extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_sub_categories';
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
		
		if (isset($data['cat_id'])) {
			$this->db->where('cat_id', $data['cat_id']);
		}
		
		if (isset($data['shop_id'])) {
			$this->db->where('shop_id',$data['shop_id']);
		}
		
		$query = $this->db->get();
		return ($query->num_rows() == 1);
	}

	function save(&$data, $id=false)
	{
		if (!$id && !$this->exists(array('id' => $id))) {
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

	function get_all($shop_id, $limit = false, $offset = false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by('cat_id','asc');
		//$this->db->order_by('cat_id','asc');
		print_r($this->db->get_last);
		return $this->db->get();
	}
	
	function get_sub_cat_name_by_id($id)
	{	
		$this->db->from($this->table_name);
		$this->db->where('id', $id);
		$query = $this->db->get();
		foreach ($query->result() as $row) {
		    return $row->name;
		}
	}
	
	function get_only_publish($shop_id, $limit=false,$offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		$this->db->where('is_published', 1);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by('ordering','asc');
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
		$this->db->where('is_published', 1);
		return $this->db->count_all_results();
	}

	function count_all_by($shop_id, $conditions=array())
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if (isset($conditions['searchterm'])) {
			$this->db->like('name', $conditions['searchterm']);
		}
			
		$this->db->where('is_published',1);
		return $this->db->count_all_results();
	}
	
	function get_all_by($shop_id, $conditions=array(), $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if (isset($conditions['searchterm'])) {
			$this->db->like('name',$conditions['searchterm']);
		}
			
		$this->db->where('is_published', 1);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by('added','desc');
		return $this->db->get();
	}
	
	function get_all_by_cat($cat_id)
	{
		$this->db->from($this->table_name);
		$this->db->where('cat_id', $cat_id);
		return $this->db->get();
	}
	
	function get_all_by_cat_id($cat_id,$order_by='added',$order_type='desc',$limit=false,$offset=false) {
		$this->db->from($this->table_name);
		$this->db->where('cat_id', $cat_id);
		$this->db->where('is_published', 1);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by($order_by,$order_type);
		
		return $this->db->get();
	}

	function delete($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete($this->table_name);
 	}
 	
 	function delete_by_shop($shop_id)
 	{
 		$this->db->where('shop_id', $shop_id);
 		return $this->db->delete($this->table_name);
 	}
 	
 	function delete_by_cat($cat_id)
	{
		$this->db->where('cat_id', $cat_id);
		return $this->db->delete($this->table_name);
	}
}
?>