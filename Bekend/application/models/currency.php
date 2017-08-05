<?php 
class Currency extends Base_Model
{
	protected $table_name;

	function __construct()
	{
		parent::__construct();
		$this->table_name = 'mk_shops_currency';
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

	function save(&$data, $id=false)
	{
		$this->db->where('id', $id);
		return $this->db->update($this->table_name, $data);
	}
}
?>