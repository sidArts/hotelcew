<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**

 *

 */

	

	if ( ! function_exists('createpassword')) { 

		function createpassword($pass) {  

			$options = ['cost' => 13,  ];  

			return password_hash($pass, PASSWORD_BCRYPT, $options); 

		}

	}



	if ( ! function_exists('passwordmatch')) { 

		function passwordmatch($pass, $hashpassword) {  

			return password_verify($pass, $hashpassword); 

		}

	}



	if ( ! function_exists('toastr')) { 

		function set_toastr($msg = NULl, $type = 1, $sess_name = "flash_msg") {

			$CI =& get_instance(); 

			$bgColor = ($type) ? 'success' : 'error';

			$toast_msg = "<script>toastr.".$bgColor."('".$msg."')</script>";

			$CI->session->set_flashdata($sess_name, $toast_msg);

		}

	}



	if ( ! function_exists('flash_message')) { 

		function flash_message($msg = NULl, $type = 1) { // success/danger

			$bgColor = ($type) ? 'success' : 'danger';

			$msgStat = ($type) ? 'Success' : 'Fail';

			return '<div class="alert alert-block alert-'.$bgColor.'" id="alertFlash"><b>'.$msgStat.' !</b> &ensp;'.$msg.'</div>';

		}

	}



	if ( ! function_exists('set_flash_message')) { 

		function set_flash_message($msg = NULl, $type = 1, $sess_name = "flash_msg") { // success/danger

			$CI =& get_instance();

			$bgColor = ($type) ? 'success' : 'danger';

			$msgStat = ($type) ? 'Success' : 'Fail';

			$msg = '<div class="alert alert-block alert-'.$bgColor.'" id="alertFlash"><b>'.$msgStat.' !</b> &ensp;'.$msg.'</div>';

			$CI->session->set_flashdata($sess_name, $msg);

		}

	}



	if ( ! function_exists('get_flash_message')) { 

		function get_flash_message($sess_name = "flash_msg") {

			$CI =& get_instance();

			return $CI->session->flashdata($sess_name);

		}

	}



	if ( ! function_exists('encrypt')) { 

		function encrypt($string = NULL, $key=5) {

			$result = '';

			for($i=0, $k= strlen($string); $i<$k; $i++) {

				$char = substr($string, $i, 1);

				$keychar = substr($key, ($i % strlen($key))-1, 1);

				$char = chr(ord($char)+ord($keychar));

				$result .= $char;

			}

			return base64_encode($result);

		}

	}



	if ( ! function_exists('get_number')) { 

		function get_number($string = NULL) {

			return preg_replace('/[^0-9]+/', '', $string);

		}

	}



	if ( ! function_exists('decrypt')) { 

		function decrypt($string, $key=5) {

			$result = '';

			$string = base64_decode($string);

			for($i=0,$k=strlen($string); $i< $k ; $i++) {

				$char = substr($string, $i, 1);

				$keychar = substr($key, ($i % strlen($key))-1, 1);

				$char = chr(ord($char)-ord($keychar));

				$result.=$char;

			}

			return $result;

		}

	}



	if ( ! function_exists('get_dir')) { 

		function get_dir($file_path, $base_path = NULL) { 



			$base_path = ($base_path) ? $base_path : UPLOADS_REAL_PATH;

			$pathArr 	= explode("/", $file_path);

			$realPath 	= $base_path;



			foreach($pathArr AS $path){

				$newpath = $realPath.'/'.$path;

				if (!file_exists($newpath)) {

				    mkdir($newpath, 0777);

				}

				$realPath = realpath($newpath); 

			}



			return $realPath;

		}

	}



	/*if ( ! function_exists('send_sms')) { 

		function send_sms($to = NULL, $msg = NULL, $country_code = "") {



      		$api_key 		= SMS_AUTH_KEY;

      		$country_code 	= ($country_code) ? $country_code : COUNTRY_CODE;

  			$to 			= $country_code.$to;



			$ch = curl_init();  

	        curl_setopt($ch, CURLOPT_URL,"http://2factor.in/API/V1/{$api_key}/ADDON_SERVICES/SEND/PSMS");  

	        curl_setopt($ch, CURLOPT_POST, 1);  

	        curl_setopt($ch, CURLOPT_POSTFIELDS,"From=VERIFY&To={$to}&Msg={$msg}");  

	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  

	        $server_output = curl_exec ($ch); 

	      	

	        curl_close ($ch);

		}

	}*/



	/*if ( ! function_exists('send_otp')) { 

		function send_otp($to = NULL, $otp = NULL, $type = 0) {

      		$api_key="d7b83732-ada2-11e7-94da-0200cd936042";

      		$type_arr = [0 => 'Register', 1 => 'reset password'];

      		

  			$to = (strlen($to) == 10) ? "91".$to : $to;

			$ch = curl_init();  

	        curl_setopt($ch, CURLOPT_URL,"https://2factor.in/API/V1/{$api_key}/SMS/{$to}/{$otp}/{$type_arr[$type]}");  

	        curl_setopt($ch, CURLOPT_POST, 1);  

	        curl_setopt($ch, CURLOPT_POSTFIELDS,"");  

	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



	        $server_output = curl_exec ($ch);

	        curl_close ($ch);

		}

	}*/





	if ( ! function_exists('timthumb')) { 

		function timthumb($path = NULl, $height = NULL, $width = NULL) { 

			

			if (strpos(substr($path, 0,6), 'htt') === false) {

				$path = if_exist_file($path);



			}



			$imgPath = "";

			if($path != ""){

				$imgPath = PUBLIC_PATH.'timthumb.php?src='.$path;

			}



			if($height != ""){

				$imgPath .= "&h=".$height;

			}



			if($width != ""){

				$imgPath .= "&w=".$width;

			}

			

			$imgPath .= "&zc=1q=100";



			return $imgPath;

		}

	}

	

	if (!function_exists('pre')) {

	    function pre($arr = [], $die = 0) {

	        echo "<pre>"; if(is_array($arr)){ print_r($arr); } else{ echo $arr; } echo "</pre>";

	        if ($die) { exit();}

	    }

	}

	

	if (!function_exists('reCaptcha')) {

	    function reCaptcha() {

	        return '<div class="g-recaptcha" data-sitekey="'.CAPTCHA_SITE_KEY.'"></div>';

	    }

	}



	if ( ! function_exists('captcha')) { 

		function captcha($txt = NULL, $sess = 'captcha') {

			$CI =& get_instance();



			$input_text = ($txt != "") ? $txt : rand("11111", '99999'); #base64_encode(rand(111, 991).rand(111, 999));

		    $width      = (strlen($input_text)*9)+20;

		    $height     = 28;

		    

		    $textImage  = imagecreate($width, $height);

		    $color      = imagecolorallocate($textImage, 0, 0, 0);

		    

		    $CI->session->set_userdata($sess, $input_text);



		    imagecolortransparent($textImage, $color);

		    imagestring($textImage, 5, 10, 5, $input_text, 0xFFFFFF);

		    

		    // create background image layer

		    $background = imagecreatefromjpeg(PUBLIC_PATH.'images/captcha.jpeg');

		    

		    // Merge background image and text image layers

		    imagecopymerge($background, $textImage, 15, 15, 0, 0, $width, $height, 100);

		    

		    $output = imagecreatetruecolor($width, $height);

		    imagecopy($output, $background, 0, 0, 20, 13, $width, $height);

		    

		    ob_start();

		    imagepng($output);

		    $capImg = '<img id="output" src="data:image/png;base64,'.base64_encode(ob_get_clean()).'" />';

		    return $capImg;

		    /*printf('<img id="output" src="data:image/png;base64,%s" />', base64_encode(ob_get_clean()));*/

		}

	}





	if ( ! function_exists('upload_image')) { 

		function upload_image($path = NULL, $imageCode =NULL, $file_name = NULL) {

			try {

				list($type, $imageCode) = explode(';', $imageCode);

				list(, $imageCode)      = explode(',', $imageCode);

				$imageCode 				= base64_decode($imageCode);



				$file_name = ($file_name) ? $file_name : time().rand(111, 999);

				file_put_contents(get_dir($path).'/'.$file_name.".jpg", $imageCode);

				return $file_name.".jpg";

			}

			catch(Exception $e) {

			  	echo 'Message: ' .$e->getMessage();

			}

		}

	}



	if ( ! function_exists('if_exist_file')) { 

		function if_exist_file($imgPath = NULL, $file = NULL) {

			$img = DEFAULT_IMAGE;

			if($file != "" && $imgPath != ""){

	            if(file_exists(UPLOADS_REAL_PATH.$imgPath.$file)){

	                $img = UPLOAD_PATH.$imgPath.$file;

	            }

	        }

            return $img;

		}

	}







	if ( ! function_exists('email')) { 

		function email($from = "", $to = NULL, $subject = NULL, $message = NULL) {

			try { 

				// To send HTML mail, the Content-type header must be set

				$headers  = 'MIME-Version: 1.0' . "\r\n";

				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

				 

				// Create email headers

				$headers .= 'From: '.$from."\r\n".

				    		'Reply-To: '.$from."\r\n" .

				    		'X-Priority: 1 (Highest)'."\r\n".

				    		'X-MSMail-Priority: High'."\r\n".

				    		'X-Mailer: PHP/' . phpversion();

				 

				// Sending email

				if(mail($to, $subject, $message, $headers)){

				    return true;

				} else{

				    return false;

				}

			}

			catch(Exception $e) {

			  	echo 'Message: ' .$e->getMessage();

			}

		}

	}



	if ( ! function_exists('clean')) { 

		function clean($str = NULL, $separator = '-') {

			$str 	= preg_replace('/[^A-Za-z0-9]/', $separator, $str);

			$strArr = explode($separator, $str);

  			$strArr = array_filter($strArr);

  			return strtolower(implode($separator, $strArr));

		}

	}



	if (!function_exists('slug')) {

	    function slug($tableName = '', $coloumn = '', $value = '') {

	        $CI 	= & get_instance();

	        $slugV 	= clean($value);

	        $data 	= $CI->Common->find($tableName, "count('id') AS Total", "", "{$coloumn} = '{$value}' OR {$coloumn} LIKE '{$slugV}%'");

	        //pre($data, 1);

	        if(@$data[0]['Total'] > 0){

	        	$data = $data[0];

	        	if(isset($data['Total']) && $data['Total'] > 0){

	        		return $slugV."-".$data['Total'];

	        	}

	        }else{

	        	return $slugV;

	        }

	    }

	}



	if ( ! function_exists('random_pass')) { 

		function random_pass($length = 6) {  

			$output = "";

			$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";

	       	$size = strlen( $chars );

	       	for( $i = 0; $i < $length; $i++ ) {

	            $str= $chars[ rand( 0, $size - 1 ) ];

	            $output .= $str;

	       	}

	       	return $output;

		}

	}



	if ( ! function_exists('paypal')) { 

		function paypal($inputs = []) {  

			$action 		= (SANDBOX == TRUE) ? "https://www.sandbox.paypal.com/cgi-bin/webscr" : "https://www.paypal.com/cgi-bin/webscr";

			$amount 		= @$inputs['amount'];

			$success_url 	= BASE_URL.@$inputs['success'];

			$notify_url 	= BASE_URL.@$inputs['notify_url'];

			$cancel_url 	= BASE_URL.@$inputs['cancel_url'];

			$item_name 		= @$inputs['item_name'];

			$item_number 	= @$inputs['item_number'];

			$custom         =@$inputs['custom'];

			//echo $success_url; exit();

			//onload='document.frm_customer_detail.submit()'

			echo $form = "<html>

								<body onload='document.frm_customer_detail.submit()'>

									<form name='frm_customer_detail' id='frm_customer_detail' action='".$action."' method='POST'>

									    <input type='hidden'name='business' value='".BUSINESS_ID."'>

									    <input type='hidden' name='item_name' id='item_name' value='".$item_name."'>

									    <input type='hidden' name='item_number' id='item_number' value='".$item_number."'>

									    <input type='hidden' name='amount' id='amount' value='".$amount."'>

									    <input type='hidden' name='currency_code' value='".CURRENCY_CODE."'>

									    <input type='hidden' name='quantity' value='1'>

									    <input type='hidden' name='return'value='".$success_url."'>

									    <input type='hidden' name='notify_url' value='".$notify_url."'>

									     <input type='hidden' name='custom' value='".$custom."'>

									    <input type='hidden' name='cancel_return' value='".$cancel_url."' />

									    <input type='hidden' name='cmd' value='_xclick'>

									</form>

								</body>

							</html>";

		}

	}



	if ( ! function_exists('settings')) { 

		function settings($slug = NULL) {

			$CI =& get_instance();

			$setArr = [];

			if($slug != ""){

				$settingsData = $CI->Common->findBy(SETTINGS, 'slug', $slug);

				if(!empty($settingsData)){

					return $settingsData['value'];

				}else{

					return "";

				}

			}else{

				$settingsData = $CI->Common->find(SETTINGS, "", "", "status = 'A'");

				if(!empty($settingsData)){

					foreach($settingsData AS $key => $eachSet){

						$setArr[$eachSet['slug']] = $eachSet['value'];

					}

					return $setArr;

				}else{

					return [];

				}

			}

		}

	}



	if ( ! function_exists('information_icon')) { 

		function information_icon($slug = NULL, $position = 'right') {

			$CI =& get_instance();

			if($slug != ""){

				$settingsData = $CI->Common->findBy(INFO, 'slug', $slug);

				if(!empty($settingsData)){

					if($settingsData['value'] != ""){

						return '<em><a data-toggle="tooltip" data-placement="'.$position.'" title="'.stripslashes($settingsData['value']).'"><i class="fa fa-info-circle" aria-hidden="true"></i></a></em>';

					}else{

						return "";

					}

				}else{

					return "";

				}

			}

		}

	}



	if ( ! function_exists('last_query')) { 

		function last_query() {  

			$CI =& get_instance();

			return $CI->db->last_query(); 

		}

	}



	if ( ! function_exists('sql_time')) { 

		function sql_time($date = NULL) {  

			$qry = "SELECT NOW()";

		

			if($date == 'current_time'){

				$qry = "SELECT CURRENT_TIME";

			} elseif ($date == 'current_timestamp'){

				$qry = "SELECT CURRENT_TIMESTAMP";

			} elseif ($date == 'current_date'){

				$qry = "SELECT CURRENT_DATE";

			}



			$res 	= $this->db->query($qry." as result");

			$show 	= $res->row_array();

			echo $show['result'];

		}

	}



	if ( ! function_exists('invalid_request')) { 

		function invalid_request($msg = "Invalid request") {  

			set_toastr($msg, 0);

			redirect($_SERVER['HTTP_REFERER']);

		}

	}

	

	if ( ! function_exists('my_ip')) { 

		function my_ip() { 

			return ($_SERVER['REMOTE_ADDR'] == '::1') ? '45.64.237.141':$_SERVER['REMOTE_ADDR'];

		}

	}



	if ( ! function_exists('strike')) { 

		function strike($string = NULL, $validate = NULL) { 

			return ($validate) ? "<strike>".$string."</strike>" : $string;

		}

	}



	if ( ! function_exists('cropper')) { 

		function cropper($propertise = []) {  

			$imgSrc = DEFAULT_IMAGE;

			$dltBtn = 'none';

			if(@$propertise['src']){

				if(file_exists(UPLOADS_REAL_PATH.@$propertise['src'])){

					$imgSrc = UPLOAD_PATH.$propertise['src'];

					$dltBtn = 'block';

				}

			}

			$imageId 	= (@$propertise['id'] != "") 	? $propertise['id'] 	: "";

			$ratio 		= (@$propertise['ratio']) 		? $propertise['ratio'] 	: 0;

			$width 		= (@$propertise['width']) 		? $propertise['width'] 	: 0;

			$height 	= (@$propertise['height']) 		? $propertise['height'] : 0;

			$uniqueId 	= uniqid();



			$cropperString = '<div class="corpper-image">

								<label>

								    <input type="hidden" id="image_id-'.$uniqueId.'" name="cropper_image_id" value="'.@$imageId.'">

								    <input type="hidden" id="delete-'.$uniqueId.'" name="cropper_delete" value="0">

								    <input type="hidden" class="crop-config" data-width="'.$width.'" data-height="'.$height.'" data-ratio="'.$ratio.'">

								    <input type="file" id="file-input" name="" style="height: 0; opacity: 0;" >

								    <input type="hidden" name="image_code" id="image_code" value="">

								    <img id="place-add-image" src="'.timthumb($imgSrc, '', 300).'">

								    <a href="javascript:void(0)" data-unique="'.$uniqueId.'" title="Delete" data-imageId="'.@$imageId.'" class="btn-corpper-delete" style="display: '.$dltBtn.'"><i class="fa fa-trash"></i></a>

									<span class="choose-file">Choose..</span>

								</label>

							</div>';

			return $cropperString;

		}

		

	}

/* End of file common_helper.php */

/* Location: ./application/helpers/common_helper.php */