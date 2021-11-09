<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Welcome extends CI_Controller {



	public function __contruct()

	{

		parent::__construct();

		

		

	}



	public function index() {

		$this->load->model("Home_model");

//echo '1';

		// $data['wishlist']=$this->Home_model->wisthlist_count();

		// $data['categories']=$this->Home_model->category();

		// $data['cartCount']=$this->Home_model->cartCount();

		

		// $data['recomended_product']=$this->Home_model->recomended_product();

		// $data['products']=$this->Home_model->finest_product();
		$data=[];
		$this->layout->set('front-panel',$data);

		$this->layout->set_title(SITE_NAME);

		$this->layout->view('home',$data);

	}



	public function inner() {

		#$this->layout->set('front-panel');

		$this->layout->set('default');

		$this->layout->set_title("Inner Page");

		$this->layout->view('inner-page');

	}



	







}

