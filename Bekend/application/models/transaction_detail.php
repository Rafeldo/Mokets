<?php 
class Transaction_Detail extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_transaction_detail';
	}
	
	function save(&$data)
	{
		if ($this->db->insert($this->table_name,$data)) {
			return true;
		}
				
		return $false;
	}
	
	function count_all($item_id=false)
	{
		$this->db->from($this->table_name);
		
		if ($item_id) {
			$this->db->where('item_id',$item_id);
		}
		
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
	
	function get_all_by_header($header_id)
	{
		$this->db->from($this->table_name);
		$this->db->where('transaction_header_id',$header_id);
		$this->db->order_by('id','asc');
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
}
?>