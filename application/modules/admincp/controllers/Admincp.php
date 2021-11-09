<?php

defined('BASEPATH') OR exit('No direct script access allowed');



class Admincp extends MX_Controller {



    private $data;

    public function __construct()  

    { 

    	parent::__construct();

    	//$this->layout->set("default");

    	$this->data = [];

    }



    public function index(){

        #$this->layout->set("admin-login");

        $breadcrumb = [

            [

                'page' => "Starter"

            ]

        ];

       

        $this->data['user']=$this->countSet("st_users",['role'=>2]);
        $today = date('Y-m-d');
        $this->data['today_booking']=$this->countSet("st_bookings",['booking_date'=>date('Y-m-d')]);
        $today_earning = $this->Common->find([
            'table'     => 'st_bookings', 
            'select'    => "SUM(total_cost) AS today_earning",
            'where'     => "status = 'C' AND booking_date= '{$today}'",
            'query'     => "first"
            ]);
        $this->data['today_earning']=number_format($today_earning['today_earning'],2);
        $this->data['total_booking']=$this->countSet("st_bookings",['status!='=>'D']);

        // $this->data['category']=$this->countSet("rr_categories");

        // $this->data['order']=$this->countSet("rr_orders");

        // $this->data['attributes']=$this->countSet("rr_product_attributes");

       // print_r($this->data); exit;

        

        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("admin-panel");

    	$this->layout->set("admin-panel");

    	$this->layout->view('welcome', $this->data);

    }

    



    public function countSet($table,$where="")

    {

        if($where=="")

        {

            $data=$this->db->select("*")->from($table)->get()->result_array();

        }

        else

        {

            $data=$this->db->select("*")->from($table)->where($where)->get()->result_array();

        }

        

        return count($data); 

    }



    public function login(){

        $this->layout->set("admin-login");

        $this->layout->set_title("Login");

        $this->layout->view('admin-login', $this->data);

    }



    



    /**

     * ADMIN LOGIN

     * params : {email_address, password}

     */

    public function check_admin_login(){

        if(!empty($this->input->post())){

            $error = TRUE;

            $this->form_validation->set_rules('email_address', 'email address', 'trim|required|valid_email|min_length[3]|max_length[128]');

            $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[6]|max_length[64]');



            if ($this->form_validation->run() == FALSE){

                die(json_encode(['status' => 0, 'msg' => validation_errors()]));

            }

            $inputs = $this->input->post();

            $userDtls = $this->User->findBy("email", $inputs['email_address']);

            if(!empty($userDtls)){

                if(passwordmatch($inputs['password'], $userDtls['password'])){

                    $error = FALSE;

                    $this->session->set_userdata(ADMIN_SESS, [

                                                                'status'    => 1, 

                                                                'id'        => $userDtls['id'], 

                                                                'data'      =>  [

                                                                                    'name'  => $userDtls['name'], 

                                                                                    'email' => $userDtls['email'],

                                                                                    'phone' => $userDtls['phone']

                                                                                ],

                                                                                'role'=>$userDtls['role'],

                                                            ]);

                }

            }



            if($error == TRUE){

                die(json_encode(['status' => 0, 'msg' => "Invalid email or password entered"]));

            }else{

                die(json_encode(['status' => 1, 'msg' => "Login successful"]));

            }

        }



        die(json_encode(['status' => 1, 'msg' => "Invalid request"]));

    }



    /* Change Password */

    public function changePassword(){

        $this->layout->set("admin-login");

        $this->layout->set_title("Login");

        $this->layout->view('admin-newpassword', $this->data);



    }



    public function admin_logout(){

        $this->session->unset_userdata(ADMIN_SESS);

        redirect(ADMIN_URL.'login');

    }



    public function general_settings()

    {

        $breadcrumb = [

            [

                'page' => "General Settings"

            ]

        ];

        $this->data['content']=$this->db->select("*")->from("rr_general_settings")->where(['id'=>1])->get()->result_array();

        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("admin-panel");

    	$this->layout->set("admin-panel");

    	$this->layout->view('general-settings',$this->data);

    }



    public function general_settings_post()

    {

        if(!empty($this->input->post()))

        {

            $post=$this->input->post();

            $this->db->where(['id'=>1]);

            $this->db->update("rr_general_settings",$post);

            $this->session->set_flashdata("success","Settings Updated");

            redirect("admincp/settings/general");

        }

        else

        {

            echo "Bad Request";

        }

    }





    public function review()

    {

        $breadcrumb = [

            [

                'page' => "Review Management"

            ]

        ];

        $this->data['review']=$this->db

                                ->select("rr_product_review.*,rr_users.name,rr_users.email,rr_products.title")

                                ->from("rr_product_review")

                                ->join("rr_products","rr_product_review.product_id=rr_products.id")

                                ->join("rr_users","rr_users.id=rr_product_review.user_id")

                                ->order_by("rr_product_review.id","DESC")

                                ->get()

                                ->result_array();

        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("admin-panel");

    	$this->layout->set("admin-panel");

    	$this->layout->view('review',$this->data);

    }



   

    public function coupon()

    {

        $breadcrumb = [

            [

                'page' => "Coupon Management"

            ]

        ];

        $this->data['coupons']=$this->db->select("*")->from("rr_coupons")->order_by("id","DESC")->get()->result_array();

        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("admin-panel");

    	$this->layout->set("admin-panel");

    	$this->layout->view('coupon',$this->data);

    }



    public function coupon_edit($id)

    {

        $breadcrumb = [

            [

                'page' => "Coupon Management"

            ]

        ];

        $this->data['content']=$this->db->select("*")->from("rr_coupons")->where("id",$id)->get()->result_array();

        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("admin-panel");

        $this->layout->set("admin-panel");

        $this->layout->view('coupon-edit',$this->data);

        

    }



    public function coupon_post()

    {

        if(!empty($this->input->post()))

        {

            $post=$this->input->post();

            if($post['id']=="")

            {

                $this->db->insert("rr_coupons",$post);

                $this->session->set_flashdata("success","Coupon Addedd Updated");

            }

            else

            {

                $this->db->where(['id'=>$post['id']]);

                $this->db->update("rr_coupons",$post);

            }

            redirect("admincp/coupon");

        }

        else

        {

            echo "Bad Request";

        }

    }

    

    public function statusChange()

    {

        $this->db->where("id",$_GET['id']);

        $this->db->update($_GET['table'],['status'=>$_GET['status']]);

        echo last_query();

    }





    public function faq()

    {

        $breadcrumb = [

            [

                'page' => "Faq Management"

            ]

        ];

        $this->data['faq']=$this->db->select("*")->from("rr_faq")->where('status!=','D')->get()->result_array();

        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("admin-panel");

        $this->layout->set("admin-panel");

        $this->layout->view('faq',$this->data);

    }



    public function faq_post()

    {

        if(!empty($this->input->post()))

        {

            $post=$this->input->post();

            $this->db->insert("rr_faq",$post);

            $this->session->set_flashdata("success","FAQ Addedd Updated");

            redirect("admincp/faq");

        }

        else

        {

            echo "Bad Request";

        }

    }

    







}

