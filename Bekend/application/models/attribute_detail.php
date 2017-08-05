<?php 
class Attribute_Detail extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_items_attributes_detail';
	}
	
	
	function save(&$data,$id)
	{
		if (!$id && !$this->exists(array('id' => $id, 'shop_id' => $data['shop_id'],'header_id' => $data['header_id'],'name' => $data['name']))) {
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
	
	function count_all_by_header($header_id=0,$shop_id=0)
	{
		$this->db->from($this->table_name);
		$this->db->where('header_id',$header_id);
		$this->db->where('shop_id',$shop_id);
		return $this->db->count_all_results();
	}
	
	function get_all($limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		
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
	
	function get_all_by_header($header_id=0, $shop_id=0, $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('header_id',$header_id);
		$this->db->where('shop_id',$shop_id);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		$this->db->order_by('id','asc');
		return $this->db->get();
	}
	
	function delete($id)
	{
		$this->db->where('id',$id);
		return $this->db->delete($this->table_name);
	}
	
	function delete_by_header($header_id)
	{
		$this->db->where('header_id',$header_id);
		return $this->db->delete($this->table_name);
	}
		
	function delete_by_shop($shop_id)
	{
		$this->db->where('shop_id', $shop_id);
		return $this->db->delete($this->table_name);
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
 		
 		if (isset($data['header_id'])) {
 				$this->db->where('header_id',$data['header_id']);
 			}
 		
 		$query = $this->db->get();
 		return ($query->num_rows()==1);
 	}
 	
 	function count_all_by_search($header_id=0, $shop_id=0, $conditions=array())
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		$this->db->where('header_id', $header_id);
		
		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
			$this->db->where("(name LIKE '%".$conditions['searchterm']."%')", NULL, FALSE);
		}
		return $this->db->count_all_results();
	}
	
	function get_all_by_search($header_id=0, $shop_id=0, $conditions=array(), $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		$this->db->where('header_id', $header_id);
		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
			$this->db->where("(name LIKE '%".$conditions['searchterm']."%')", NULL, FALSE);
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
	
	function delete_by_item($item_id)
	{
		$this->db->where('item_id', $item_id);
		return $this->db->delete($this->table_name);
	}
	
}
?>