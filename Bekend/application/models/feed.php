<?php
class Feed extends Base_Model
{
	protected $table_name;
	
	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_feeds';
	}

	function exists($data)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if (isset($data['id'])) {
			$this->db->where('id', $data['id']);
		}
		
		if (isset($data['shop_id'])) {
			$this->db->where('shop_id', $data['shop_id']);
		}
		
		$query = $this->db->get();
		return ($query->num_rows() >= 1);
	}

	function save(&$data, $id=false)
	{
		if (!$id && !$this->exists(array('id' => $id, 'shop_id' => $data['shop_id']))) {
			if ($this->db->insert($this->table_name, $data)) {
				$data['id'] = $this->db->insert_id();
				return true;
			}
		} else {
			$this->db->where('id', $id);
			return $this->db->update($this->table_name, $data);
		}	
		return false;
	}

	function get_all($shop_id, $limit=false, $offset=false, $order_field = 'added', $order_type = 'desc')
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by($order_field,$order_type);
		return $this->db->get();
	}

	function get_info($id)
	{
		$query = $this->db->get_where($this->table_name, array('id' => $id));
		
		if ($query->num_rows()==1) {
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
	
	function count_all_by($shop_id, $conditions=array())
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
			$this->db->like(title,$conditions['searchterm']);
			$this->db->or_like(description,$conditions['searchterm']);
		}
		return $this->db->count_all_results();
	}
	
	function get_all_by($shop_id, $conditions=array(), $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
			$this->db->like(title,$conditions['searchterm']);
			$this->db->or_like(description,$conditions['searchterm']);
		}
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by('added','desc');
		return $this->db->get();
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
	
	function read_more_text($string)
	{
		$string = strip_tags($string);
		
		if (strlen($string) > 100) {
		
		    // truncate string
		    $stringCut = substr($string, 0, 100);
		
		    // make sure it ends in a word so assassinate doesn't become ass...
		    $string = substr($stringCut, 0, strrpos($stringCut, ' ')).'...'; 
		}
		return $string;
	}
}
?>