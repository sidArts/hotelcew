<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Model_product extends MY_Model {
	
	function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->table = PRODUCTS;
        $this->alias = "Product";
    }
	
    function find($fields='', $join = array(), $extra = '', $order = '', $group = '', $limit = '')	{
		return parent::find_record($this->table.' '.$this->alias, $fields, $join, $extra, $order, $group, $limit);
	}
	
	function findById($id)	{
		return parent::findById_record($this->table.' '.$this->alias, $id);
	}
	
	function findBy($field, $value, $order = '')	{
		$order = ($order) ? $order : $this->alias.'.created DESC';
		return parent::findBy_record($this->table.' '.$this->alias, $field, $value, $order);
	}
	function change_status($id, $custom = [])	{
		return parent::change_status_record($this->table, $id, $custom);
	}	
	// soft delete
	function delete($id, $custom = [])	{
		return parent::delete_record($this->table.' '.$this->alias, $id, $custom);
	}
	// soft delete
	function deleteAll($condition)	{
		return parent::deleteAll_record($this->table.' '.$this->alias, $condition);
	}
	// Permanent delete
	function drop($id)	{
		return parent::drop_record($this->table.' '.$this->alias, $id);
	}
	// Permanent delete
	function dropAll($condition)	{
		return parent::dropAll_record($this->table.' '.$this->alias, $condition);
	}

	function count($condition = NULL)	{
		return parent::count_record($this->table.' '.$this->alias, $condition);
	}
	
	function save($fildArray, $condition = "")	{
		return parent::save_record($this->table, $fildArray, $condition);
	}



}