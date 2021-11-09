<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class MY_Model extends CI_Model {
	private $status_active;
	private $status_inactive;
	private $status_deleted;

    public function __construct($status=[]) {
        parent::__construct();
        $this->status_active 	= (@$status['active']) ? $status['active'] : 'A';
        $this->status_inactive 	= (@$status['inactive']) ? $status['inactive'] : 'I';
        $this->status_deleted 	= (@$status['deleted']) ? $status['deleted'] : 'D';
    }

	/*-======================= CUSTOM FUNCTIONS ===========================*/

	function save_record($tbl, $dataArray = [], $condition = NULL) {
		//echo "<pre>"; print_r($dataArray); echo "</pre>"; exit;
		//write_log($condition);exit();
		$primaryKey = $this->primaryKey($tbl);
		//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Add Created / Modified column ~~~~~~~~~~~~~~~~~~
		if(@$dataArray['id'] || $condition != "" || @$dataArray[$primaryKey] != "")	{
			$dataArray['modified'] = date("Y-m-d H:i:s");
		}
        else	{
			$dataArray['created'] = date("Y-m-d H:i:s");
			$dataArray['modified'] = date("Y-m-d H:i:s");
		}
		//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ Add Created / Modified column ~~~~~~~~~~~~~~~~~~

		//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ delete unnessery column ~~~~~~~~~~~~~~~~~~
		$colArray 	= $this->db->query("DESC `".$tbl.'`'); 
		$cols 		= array();
		
		foreach($colArray->result() as $col)	{
			array_push($cols, $col->Field);
		}
		
		foreach($dataArray as $key => $val)	{
			if(!in_array($key, $cols))	{
				if($key == 'id' && $key != $primaryKey){
					$dataArray[$primaryKey] = $val;
				}
				unset($dataArray[$key]);
			}
		}

		//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ delete unnessery column ~~~~~~~~~~~~~~~~~~
		
		if($condition != ""){			
			/* ===== IF UPDATE ON CUSTOM CONDITIONS ======*/
			$record = $this->find_record([
				'table' 	=> $tbl, 
				'select' 	=> $primaryKey, 
				'where' 	=> $condition, 
				'query' 	=> 'first'
			]);

			$this->db->where($condition);
			$this->db->update($tbl, $dataArray);
			return $record[$primaryKey];
		} 
		else if(isset($dataArray[$primaryKey]) && @$dataArray[$primaryKey] != ""){
			/* ===== IF UPDATE ON PRIMARY KEY ======*/
			$primaryKeyVal = $dataArray[$primaryKey];
			unset($dataArray[$primaryKey]);
			$this->db->where($primaryKey, $primaryKeyVal)->update($tbl, $dataArray);
			return @$primaryKeyVal;
		}
		else{
			$this->db->insert($tbl, $dataArray);
			return $this->db->insert_id();
		}

		
        //echo $this->db->last_query(); exit(0);
    }
	
	function find_record($tbl, $fields = NULL, $join = [], $conditions = NULL, $order = NULL, $group = NULL, $limit = NULL, $exe = NULL) {
		if(is_array($tbl) && !empty($tbl)){
			$params 	= $tbl;
			$tbl 		= @$params['table'];
			$fields 	= (@$params['select']) 		? @$params['select'] 		: "";
			$join 		= (@$params['join']) 		? @$params['join'] 			: "";
			$conditions = (@$params['where']) 		? @$params['where'] 		: "";
			$order 		= (@$params['order']) 		? @$params['order'] 		: "";
			$group 		= (@$params['group']) 		? @$params['group'] 		: "";
			$limit 		= (@$params['limit']) 		? @$params['limit'] 		: "";
			$exe 		= (@$params['query']) 		? @$params['query'] 		: "";
			
			# pagination
			if(@$params['page'] != "" && is_numeric((@$params['page']))){
				# Assign record per page, by default it is 0.
				$rec_per_page = 10;
				if(@$params['per_page'] != "" && is_numeric(@$params['per_page']) && @$params['per_page'] > 0){
					$rec_per_page = $params['per_page'];
				}

				#page should be gretter than 0.
				$params['page'] = ($params['page'] < 1) ? 1 : $params['page'];
				$start = (($params['page'] - 1) * $rec_per_page);

				$limit = "{$start}, {$rec_per_page}";
			}
		}

		if(empty($tbl)) return [];
		
		$this->db->select($fields);											// Select column
        $this->db->from($tbl);												// From Table
        
		if(!empty($join)){
			foreach($join as $eachVal)	{
				$joinTbl 		= (@$eachVal['table']) 		? $eachVal['table'] 		: $eachVal[0];
				$joinAlias 		= (@$eachVal['alias']) 		? $eachVal['alias'] 		: $eachVal[1];
				$joinType 		= (@$eachVal['type']) 		? $eachVal['type'] 			: $eachVal[2];
				$joinConditions = (@$eachVal['conditions']) ? $eachVal['conditions'] 	: $eachVal[3];

				$this->db->join($joinTbl.' '.$joinAlias, $joinConditions, $joinType);
			}
		}
		
		$tableName = explode(' ', $tbl);

		$tblForAl = (@$tableName[1] != "") ? $tableName[1] : $tableName[0];
		if($this->columns($tableName[0], 'status')){
			// No deleted record should be display
			$this->db->where($tblForAl.".status != '".$this->status_deleted."'");
		}									
        if($conditions 	!= '') $this->db->where($conditions, "", false);							// Where conditions
		if($order 		!= '') $this->db->order_by($order);						// Order by
		if($group 		!= '') $this->db->group_by($group);						// Group by
		
		if($limit)	{														// Limit
			$limitOfset = explode(',', $limit); 
			if(count($limitOfset) == 2)
				$this->db->limit($limitOfset[1],$limitOfset[0]);
			else
				$this->db->limit($limit);
		}
        
        $query = $this->db->get();
        
        //echo $this->db->last_query(); //exit(0);
        
		//echo "<pre>"; print_r($query->result_array()); echo "</pre>"; //exit;		
        
    	if($exe == 'first'){
    		return $query->row_array();
    	}else if($exe == 'count'){
    		return $query->num_rows();
    	}else{
        	return $query->result_array();
    	}
    	
    }
	
	function find_all_record($tbl, $fields, $join =array(), $extra='', $order = '', $group = '', $limit = '')
    {
		$this->db->select($fields);											// Select column
        $this->db->from($tbl);												// From Table
        
		if(!empty($join))
			foreach($join as $eachVal)	{
				$this->db->join($eachVal['table'].' '.$eachVal['alias'], $eachVal['conditions'], $eachVal['type']);
			}
		$tableName = explode(' ', $tbl);
			
        if($extra != '') $this->db->where($extra);							// Where conditions
		if($order != '') $this->db->order_by($order);						// Order by
		if($group != '') $this->db->group_by($group);						// Group by
		
		if($limit)	{														// Limit
			$limitOfset = explode(',', $limit); 
			if(count($limitOfset) == 2)
				$this->db->limit($limitOfset[1],$limitOfset[0]);
			else
				$this->db->limit($limit);
		}
        
        $query = $this->db->get();
        
        //echo $this->db->last_query(); //exit(0);
        
		//echo "<pre>"; print_r($query->result_array()); echo "</pre>"; //exit;
		
        if($query->num_rows() > 0)
            return $query->result_array();
        else
            return [];
    }
	
	function findById_record($tbl, $id)	{
		if($this->columns($tbl, 'id')){
			$this->db->where('id', $id);
		}else{
			$pk = $this->primaryKey($tbl);
			$this->db->where($pk, $id);
		}
		$query = $this->db->get($tbl);
		//echo $this->db->last_query();
		
		if($query->num_rows() > 0)
			return $query->row_array();
		else
			return [];
	}
	
	function findBy_record($tbl, $field, $value, $order = '')	{
		
		$this->db->where($field, $value);
		if($order != '') $this->db->order_by($order);				// optional order
		$query = $this->db->get($tbl);
		//echo $this->db->last_query();
		
		if($query->num_rows() > 0)
			return $query->row_array();
		else
			return [];
	}
	
	function count_record($tbl, $condition = '', $join = [])	{
		if($this->columns($tbl, 'status')){
			$this->db->where("status != '".$this->status_active."'");
		}

		if(!empty($join)){
			foreach($join as $eachVal)	{
				$this->db->join($eachVal['table'].' '.$eachVal['alias'], $eachVal['conditions'], $eachVal['type']);
			}
		}
		
		if($condition) $this->db->where($condition);
		$query = $this->db->get($tbl);
		return $query->num_rows();
	}
	
	function change_status_record($tbl, $id, $custom = [])	{
		$current_status = $this->status_inactive;
		if(empty($custom)){
			$record = $this->findById_record($tbl, $id);
			$current_status = ($record['status'] == $this->status_inactive) ? $this->status_active : $this->status_inactive;
		}
		if($this->columns($tbl, 'id')){
			$status = $this->db->where('id', $id);
		}else{
			$status = $this->db->where($this->primaryKey($tbl), $id);
		}

		if(!empty(@$custom)){
			return $status = $status->update($tbl, $custom);
		}
		else if($this->columns($tbl, 'status')){
			return $status->update($tbl, ['status' => $current_status]);
		} else{
			return NULL;
		}
		//return $query = $this->db->delete($tbl);
	}
	
	function delete_record($tbl, $id, $custom = [])	{
		if($this->columns($tbl, 'id')){
			$this->db->where('id', $id);
		}else{
			$this->db->where($this->primaryKey($tbl), $id);
		}

		if($this->columns($tbl, 'status')){
			return $this->db->update($tbl, ['status' => $this->status_deleted]);
		}else if(!empty(@$custom)){
			return $this->db->update($tbl, $custom);
		}else{
			return NULL;
		}
		//return $query = $this->db->delete($tbl);
	}
	
	function drop_record($tbl,$id)	{
		if($this->columns($tbl, 'id')){
			$this->db->where('id', $id);
		}else{
			$this->db->where($this->primaryKey($tbl), $id);
		}
		return $query = $this->db->delete($tbl);
	}
	
	function dropAll_record($tbl,$condition = '')	{
		
		if($condition)	{
			$this->db->where($condition);
			return $query = $this->db->delete($tbl);
		}
		else return NULL;
	}
	
	function deleteAll_record($tbl,$condition = '', $custom = [])	{
		if($this->columns($tbl, 'status')){
			if($condition)	{
				$this->db->where($condition);
				return $this->db->update($tbl, ['status' => $this->status_deleted]);
			}
		}else if(!empty(@$custom)){
			return $this->db->update($tbl, $custom);
		}else{
			return NULL;
		}
	}
	
	function columns($tbl = NULL, $column = NULL)	{
		if($tbl){
			$cond = ($column) ? " AND `COLUMN_NAME` = '{$column}'" : "";
			$colArray   = $this->db->query("SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`='".$this->db->database."' AND `TABLE_NAME`='{$tbl}' ".$cond); 
	        if($column){
	        	return $colArray->num_rows();
	        }else{
	        	$cols = [];
	        	$res = $colArray->result_array();
	        	if(!empty($res)){
	        		foreach($res AS $eachCol){
	        			$cols = $eachCol['COLUMN_NAME'];
	        		}
	        	}
	        	return $cols;	
	        }
	    }else{
	    	return NULL;
	    }
	}
	
	function primaryKey($table = '', $all = 0)	{
		$tableArr = explode(' ', $table);
		$table = @$tableArr[0];
		$res = $this->db->query("SHOW KEYS FROM `".$table."` WHERE Key_name = 'PRIMARY'");
		if($all){
			$result = $res->result_array();
		}else{
			$resu = $res->row_array();
			$result = @$resu['Column_name'];
		}
		return $result;
	}
}