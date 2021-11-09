<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MX_Controller {

    private $data;
    public function __construct()  
    { 
    	parent::__construct();
    	//$this->layout->set("default");
        $this->data = [];
        $this->load->model("Home_model");
        $this->data['cartCount']=$this->Home_model->cartCount();
        $this->data['categories']=$this->Home_model->category();
        $this->data['wishlist']=$this->Home_model->wisthlist_count();
    }

    public function login(){
        $breadcrumb = [
            [
                'page' => "Login"
            ]
        ];
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set("default");
        #$this->layout->set_title("Member Login");
    	$this->layout->view('member-login', $this->data);
	}

    public function sign_up(){
        $breadcrumb = [
            [
                'page' => "Signup"
            ]
        ];
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set("default");
        #$this->layout->set_title("Member Login");
    	$this->layout->view('member-signup', $this->data);
	}
}
