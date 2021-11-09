<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 *
 */
	

	if (!function_exists('user_roles')) {
		function user_roles(){
			$CI = & get_instance();
			return $CI->Common->find(USER_ROLES, 'id, title, slug', '', "status = 'A'");
		}
	}

	if (!function_exists('categories_block')) {
		function categories_block($type = 'product', $selected = [], $inputType = 'c', $parentId = NULL){			
			$input_type = 'checkbox';
			$input_name = 'category[]';

			if($inputType == 'r'){
				$input_type = 'radio';
				$input_name = 'category';
			}
			$resualt = "";
			$conditions = "cat_type = '{$type}' AND status = 'A'";
			$conditions .= ($parentId) ? " AND parent_id = '{$parentId}'" : " AND parent_id IS NULL";
			$CI = & get_instance();
			$data = $CI->Common->find(CATEGORIES, "id, slug, title", "", $conditions, "title ASC");
			
			if(count($data) > 0){
				$resualt .= "<ul>";
				foreach($data AS $key => $eachData){
					$checked = (in_array($eachData['id'], $selected)) ? "checked" : "";
					$resualt .= "<li><label><input type='{$input_type}' name='category[]' value='{$eachData['id']}' {$checked}> {$eachData['title']}</label>";
					$sub = categories_block($type, $selected, $inputType, @$eachData['id']);
					if($sub){
						$resualt .= $sub;
					}
					$resualt .= "</li>";
				}
				$resualt .= "</ul>";
			}

			return $resualt;
		}
	}

	if (!function_exists('local_time')) {
		function local_time($time = NULL, $format = "Y-m-d H:i:s"){
			$CI 		= & get_instance();
			$time 		= ($time) ? $time : gmdate('Y-m-d H:i:s');
			$timezone 	= (@$CI->session->userdata('timezone')) ? $CI->session->userdata('timezone') : "+0";
			return date($format, strtotime($time.$timezone." minutes"));
		}
	}

	if ( ! function_exists('fetch_image')) { 
		function fetch_image($img_id = NULL, $h = 100, $w = 100) {
			$timbthumbPath = PUBLIC_PATH."timthumb.php?"."h=$h&w=$w&zc=1q=100&src=";  
			$CI = & get_instance();
			$img_path = $timbthumbPath.UPLOAD_PATH."images/default/default.jpg";
			if($img_id != ""){
				$imgDtls = $CI->Common->findById(IMAGES, $img_id);
				if(!empty($imgDtls)){
					if(file_exists(UPLOADS_REAL_PATH.$imgDtls['file_path'])){
		                $img_path = $timbthumbPath.UPLOAD_PATH.$imgDtls['file_path'];
		            }
		        }
			}

			return $img_path;
		}
	}

	if ( ! function_exists('show_image')) { 
		function show_image($img_db_path = NULL, $h = 100, $w = 100) {
			$timbthumbPath = PUBLIC_PATH."timthumb.php?"."h=$h&w=$w&zc=1q=100&src=";  
			$CI = & get_instance();
			$img_path = $timbthumbPath.UPLOAD_PATH."images/default/default.jpg";
			if($img_db_path != ""){
				if(file_exists(UPLOADS_REAL_PATH.$img_db_path)){
	                $img_path = $timbthumbPath.UPLOAD_PATH.$img_db_path;
	            }
			}

			return $img_path;
		}
	}

	if ( ! function_exists('get_content')) { 
		function get_content($slug = NULL, $only_content = TRUE) {
			$CI = & get_instance();
			$content = $CI->Common->find(CONTENTS, "", "", "status = 'A' AND (id='{$slug}' OR slug='{$slug}')");
			if(!empty($content)){
				if($only_content){
					return @$content[0]['content'];
				}else{
					return @$content[0];
				}
			}
			return NULL;
		}
	}
	if (!function_exists('content_types')) {
		function content_types(){
			$CI = & get_instance();
			return $CI->Common->find(CONTENT_TYPE, "", "", "status = 'A'");
		}
	}

/* End of file bpnz_helper.php */
/* Location: ./application/helpers/common_helper.php */