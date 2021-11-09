<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/** load the CI class for Modular Extensions **/
require dirname(__FILE__).'/Base.php';

/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library replaces the CodeIgniter Controller class
 * and adds features allowing use of modules and the HMVC design pattern.
 *
 * Install this file as application/third_party/MX/Controller.php
 *
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.5
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 **/
class MX_Controller 
{
	public $autoload = array();
	
	public function __construct() 
	{
		$class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));
		log_message('debug', $class." MX_Controller Initialized");
		Modules::$registry[strtolower($class)] = $this;	
		
		/* copy a loader instance and initialize */
		$this->load = clone load_class('Loader');
		$this->load->initialize($this);	
		
		/* autoload module items */
		$this->load->_autoloader($this->autoload);
	}
	
	public function __get($class) 
	{
		return CI::$APP->$class;
	}
}


trait fileHandler
{
	public static function uploadImage($MX_CONTROLLER,$name,$target,$supportFile=['jpg','jpeg','png'],$size=5000)
    {
			$upload_dir = $target; 
			$allowed_types = $supportFile;
			
			// Define maxsize for files i.e 2MB 
			$maxsize = 2 * 1024 * 1024;  
		
			// Checks if user sent an empty form  
			if(!empty(array_filter($_FILES[$name]['name']))) { 
		
				// Loop through each file in files[] array 
				foreach ($_FILES[$name]['tmp_name'] as $key => $value) { 
					
					$file_tmpname = $_FILES[$name]['tmp_name'][$key]; 
					$file_name = $_FILES[$name]['name'][$key]; 
					$file_size = $_FILES[$name]['size'][$key]; 
					$file_ext = pathinfo($file_name, PATHINFO_EXTENSION); 
		
					// Set upload file path 
					$filepath = $upload_dir.$file_name; 
		
					// Check file type is allowed or not 
					if(in_array(strtolower($file_ext), $allowed_types)) { 
		
						// Verify file size - 2MB max  
						if ($file_size > $maxsize)  
						{
							return "Error: File size is larger than the allowed limit.";  
							exit;
						} 
						
						$file_name=time().rand(111,99999999999999).$file_name;
						$filepath = $upload_dir.$file_name; 
						if( move_uploaded_file($file_tmpname, $filepath)) { 

							$imgeData['image_name']=$file_name;
							$imgeData['file_path']="images/products/".$file_name;
							$imageContent['is_default']=0;
							$imageContent['status']="A";
							$lastid[] = $MX_CONTROLLER->Common->save(IMAGES, $imgeData);
						} 
						else {                      
							return "Error uploading {$file_name} <br />";  
							exit;
						} 	
		
						
					} 
					else { 
						
						// If file extention not valid 
						return "Error uploading {$file_name} "; 
						exit;
					}  
				} 

				return $lastid;
			}  
			else { 
				
				// If no files selected 
				return "No files selected."; 
			} 
    }
}


