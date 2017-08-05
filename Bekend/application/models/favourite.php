<?php
class Favourite extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_favourites';
	}
	
	function exists($data)
	{
		$this->db->from($this->table_name);
		
		if (isset($data['id'])) {
			$this->db->where('id',$data['id']);
		}
		
		if (isset($data['item_id'])) {
			$this->db->where('item_id',$data['item_id']);
		}
		
		if (isset($data['appuser_id'])) {
			$this->db->where('appuser_id',$data['appuser_id']);
		}
		
		if (isset($data['shop_id'])) {
			$this->db->where('shop_id', $data['shop_id']);
		}
		
		$query = $this->db->get();
		return ($query->num_rows()>=1);
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
		
		return $false;
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
		
		$this->db->order_by('added','desc');
		return $this->db->get();
	}
	
	function get_by_user_id($uid, $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('appuser_id',$uid);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		$this->db->order_by('added','desc');
		return $this->db->get();
	}

	function count_all($shop_id, $item_id=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if ($item_id) {
			$this->db->where('item_id',$item_id);
		}
		
		return $this->db->count_all_results();
	}
	
	function delete_by_shop($shop_id)
	{
		$this->db->where('shop_id', $shop_id);
		return $this->db->delete($this->table_name);
	}
	
	function un_favourite($data)
	{
		$this->db->where('shop_id', $data['shop_id']);
		$this->db->where('item_id', $data['item_id']);
		$this->db->where('appuser_id', $data['appuser_id']);
		return $this->db->delete($this->table_name);
	}
}
?>
