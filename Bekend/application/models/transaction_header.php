<?php 
class Transaction_Header extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_transaction_header';
	}
	
	function get_all_by($shop_id, $conditions = array(), $limit=false, $offset=false)
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		if ($offset) {
			$this->db->offset($offset);
		}
		
		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
			$this->db->like(delivery_address, $conditions['searchterm']);
			$this->db->or_like(billing_address, $conditions['searchterm']);
		}
		
		if (isset($conditions['start_date']) && isset($conditions['end_date'])) {
			if($conditions['start_date'] != "" && $conditions['end_date'] != ""){
				$this->db->where('added BETWEEN "'. date('Y-m-d', strtotime($conditions['start_date'])). '" and "'. date('Y-m-d', strtotime($conditions['end_date'])).'"');
			}
		}
		
		$this->db->order_by('id', 'desc');
		
		return $this->db->get();
		
	}
	
	function count_all_by($shop_id, $conditions = array())
	{
		$this->db->from($this->table_name);
		$this->db->where('shop_id', $shop_id);
		
		if (isset($conditions['searchterm']) && trim($conditions['searchterm']) != "") {
			$this->db->like(delivery_address, $conditions['searchterm']);
			$this->db->or_like(billing_address, $conditions['searchterm']);
		}
		
		if (isset($conditions['start_date']) && isset($conditions['end_date'])) {
			$this->db->where('added BETWEEN "'. date('Y-m-d', strtotime($conditions['start_date'])). '" and "'. date('Y-m-d', strtotime($conditions['end_date'])).'"');
		}
		
		return $this->db->count_all_results();
	}
	
	function save(&$data)
	{
		if ($this->db->insert($this->table_name,$data)) {
			$data['id'] = $this->db->insert_id();
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
	
	function get_all_by_user($user_id)
	{
		$this->db->from($this->table_name);
		$this->db->where('user_id',$user_id);
		$this->db->order_by('id','desc');
		return $this->db->get();
	}
	
	function update_status($status_id=0,$header_id=0)
	{
		$data = array('transaction_status' => $status_id);
		$this->db->where('id', $header_id);
		return $this->db->update($this->table_name, $data); 
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