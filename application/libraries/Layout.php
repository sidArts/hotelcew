<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
  
/** 
 * Layouts Class. PHP5 only. 
 * 
 */
class Layout { 
    
  // Will hold a CodeIgniter instance 
  private $CI; 
    
  // Will hold a title for the page, NULL by default 
  private $title_for_layout = NULL; 
  
  // will hold a page title heading
  private $page_title = NULL; 

  // Will hold a layout name 
  private $layout_name = "default";
    
  // The title separator, ' | ' by default 
  private $title_separator = ' :: '; 
    
  // The site css if required
  private $site_css; 
    
  // The site js if required
  private $site_js; 
    
  // The site breadcrumb
  private $breadcrumb; 
    
    public function __construct()  
    { 
        $this->CI =& get_instance(); 
        //$this->layout_name = "default";
        //$this->view();
       
    } 
    
    public function set($title) 
    { 
        $this->layout_name = $title;
    } 
    
    public function set_breadcumb($params = array()) 
    { 
        $this->breadcrumb = $params;
    }
    
    public function set_css($param = []) 
    { 
        $this->site_css = $param;
    } 
    
    public function set_js($param = []) 
    { 
        $this->site_js = $param;
    } 
    
    public function set_title($title, $page_title = "",  $site_name = 1) 
    { 
        #$this->title_for_layout = $title; 
        if ($title !== NULL){ 
            $this->title_for_layout =  $title;
            if($site_name){
                $this->title_for_layout .= $this->title_separator.SITE_NAME;
            } 
        }else{
            $this->title_for_layout = SITE_NAME;
        }

        $this->page_title = ($page_title != "") ? $page_title: $title;
    } 
    
  public function view($view_name = "404.php", $params = array()) 
  {  
    $layout = $this->layout_name;
    // Handle the site's title. If NULL, don't add anything. If not, add a  
    // separator and append the title. 
    if ($this->title_for_layout !== NULL){ 
      $separated_title_for_layout = SITE_NAME.$this->title_separator . $this->title_for_layout; 
    }else{
        $separated_title_for_layout = SITE_NAME;
    } 
      
    // Load the view's content, with the params passed 
    $view_content = $this->CI->load->view($view_name, $params, TRUE); 
  
    // Now load the layout, and pass the view we just rendered 
    $values =   [
                    'content'       => $view_content, 
                    'site_title'    => $this->title_for_layout,
                    'page_title'    => $this->page_title,
                    'site_css'      => $this->site_css,
                    'site_js'       => $this->site_css,
                    'breadcrumb'    => $this->breadcrumb
                ];

    $this->CI->load->view('layouts/' . $layout, $values); 
  } 
}