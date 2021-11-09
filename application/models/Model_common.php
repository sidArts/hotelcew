<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Model_common extends MY_Model {

	

	function __construct()

    {

        // Call the Model constructor

        parent::__construct(['active' => 'A', 'inactive' => 'I', 'deleted' => 'D']);

    }

	

	function findAll($table='',$fields='', $join = array(), $extra = '', $order = '', $group = '', $limit = ''){

		return parent::find_all_record($table,$fields,$join,$extra,$order,$group,$limit);

	}



    function find($table = '', $fields='', $join = array(), $extra = '', $order = '', $group = '', $limit = '')	{

		return parent::find_record($table, $fields, $join, $extra, $order, $group, $limit);

	}		

	

	/*

	|---------------------------------------------------------------

	| Find single record

	|---------------------------------------------------------------

	|

	| $table 	-> Target table name (Ex. users)

	| $id 		-> Id value of the table

	| 			   Note: Generally id of the table used as a primary key,

	|			   if primary key is seperate, then use the primary key 

	|			   value

	*/

	function findById($table = '', $id)	{

		return parent::findById_record($table, $id);

	}

	

	function findBy($table = '', $field, $value, $order = '')	{

		return parent::findBy_record($table, $field, $value, $order);

	}

	function f($table = '', $id, $custom = [])	{

		return parent::change_status_record($table, $id, $custom);

	}

	// Soft delete

	function delete($table = '', $id, $custom = [])	{

		return parent::delete_record($table, $id, $custom);

	}

	// Soft delete

	function deleteAll($table = '', $condition = NULL)	{

		return parent::deleteAll_record($table, $condition);

	}	

	

	/*

	|---------------------------------------------------------------

	| Permanently delete single record

	|---------------------------------------------------------------

	|

	| $table 	-> Target table name (Ex. users)

	| $id 		-> Id value of the table

	| 			   Note: Generally id of the table used as a primary key,

				   if primary key is seperate, then use the primary key 

				   value

	*/

	function drop($table = '', $id = NULL)	{

		return parent::drop_record($table, $id);

	}	

	

	/*

	|---------------------------------------------------------------

	| Permanently delete multiple records

	|---------------------------------------------------------------

	|

	| $table 	 -> Target table name (Ex. users)

	| $condition -> Custom conditions to delete records

	| 				(Ex. status = 'I')

	*/

	function dropAll($table = NULL, $condition = NULL)	{

		return parent::dropAll_record($table, $condition);

	}

	

	/*

	|---------------------------------------------------------------

	| Count no of records found (Return int. value, Ex. 5)

	|---------------------------------------------------------------

	|

	| $table 	 -> Target table name (Ex. users)

	| $condition -> Custom conditions to find record

	| 				(Ex. status = 'A')

	| $join 	 -> In case of need to join with multiple tables

	| 				Ex. [["table" => "users", "alias" => "User", "type" => "INNER", "conditions" => "Profile.user_id = User.id"]]

	*/

	function count($table = NULL, $condition = NULL, $join = [])	{

		return parent::count_record($table, $condition, $join);

	}

	

	/*

	|---------------------------------------------------------------

	| Add new record or update existing record

	|---------------------------------------------------------------

	|

	| $table 	 -> Target table name (Ex. users)

	|

	| $fildArray -> Data array where keys are same as the table column

	| 				(Ex. ["name" => "Tanmoy Roy", "email" => "tanmoytr@gmail.com"])

	|				Note : to update simply add primary key and value of existing record 

	|				(Ex.  ["id" => 1, "name" => "Tanmoy Roy", "email" => "tanmoytr@gmail.com"])

	|

	| $condition -> In case of update when need to update multiple records 

	|				using some custom conditions instead of id. 

	|				(Ex. status = 'A')

	*/

	function save($table = '', $fildArray = [], $condition = "")	{

		return parent::save_record($table, $fildArray, $condition);

	}





}