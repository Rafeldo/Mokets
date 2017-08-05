<?php
class Base_Model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
    
    /**
    * get_empty_object returns the object with empty properties according to table name
    * e.g - floor object for floors table
    * 
    * @param mixed $table_name
    * @return stdClass
    */
    function get_empty_object($table_name)
    {   
        $obj = new stdClass();
        
        $fields = $this->db->list_fields($table_name);
        foreach ($fields as $field) {
            $obj->$field = '';
        }
        return $obj;
    }
    
    public function get_now()
    {
    	$query = $this->db->query('SELECT NOW( ) as now');
    	
    	return $query->row()->now;
    }
}
?>