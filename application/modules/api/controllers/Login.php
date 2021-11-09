<?php



use Restserver\Libraries\REST_Controller;



defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

require APPPATH . 'libraries/Format.php';



class Login extends REST_Controller {



    private $result = [];

    private $userId = "";

    private $token  = "";

    private $user   = "";

    private $headers= [];

    private $params = [];



    /**

     * @param Authorization : TOKEN | REQUIRED

     */

    function __construct() {

        parent::__construct();

        # Default response

        $this->result = [

            'status'    => FALSE, 

            'message'   => "", 

            'data'      => []

        ];



        # Request params

        $this->params = $this->post();
        var_dump($result);

    }

    

    /**

     *------------------------------------------------

     * TEST METHOD

     *------------------------------------------------

     * @method GET

     * @return json array()

     */

    public function test_get(){

        $this->result['status']     = TRUE;

        $this->result['message']    = "This is test function";

        return $this->response($this->result, REST_Controller::HTTP_OK);

    }



    /**

     * -------------------------------------

     * PRODUCT CATEGORY LIST

     * -------------------------------------

     * @method POST

     * @return json array()

     */

    public function category_list_post(){

        $this->result['status'] = TRUE;



        $categosieslist= $this->Common->find([

            'table'     => CATEGORIES, 

            'select'    => "id, title, image,downloadable",

            'where'     => "status = 'A'"

        ]);



         $catList = [];

        if(!empty($categosieslist)){

            foreach($categosieslist AS $key => $eachCat){

                $catList[$key] = $eachCat;

                $catList[$key]['image'] = timthumb($catList[$key]['image'], 200, 200);

            }

        }



        $this->result['status'] = TRUE;

        $this->result['data']   = [

           

            'categories' => $catList

        ];



        return $this->response($this->result, REST_Controller::HTTP_OK);

    }



   



    /**

     * -------------------------------------

     * PRODUCT DEPENDING ON CATEGORY

     * -------------------------------------

     * @method POST

     *

     * @param category_id   : (number) | REQUIRED

     * @param page          : (number) | NULLABLE

     * @param rec_per_page  : (number) | NULLABLE

     *

     * @return json array()

     */

    public function login_post($value='')
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        // $users = $this->Common->find([

        //     'table'     => USERS,

        //     'select'    => "*",

        //     'where'     => "status = 'A'",

        //     // 'query'     => 'first'

        // ]);

        # Check validation rule

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }else {

            # Fetch inputs

            $inputs     = $this->input->post();

            $userDtls   = $this->Common->find([

                'table' => USERS,

                'where' => "email = '{$inputs['email']}'",

                'query' => "first"

            ]);
            $userDtls = $this->User->findBy("email", $inputs['email']);

            if(!empty($userDtls) && passwordmatch($inputs['password'], $userDtls['password'])) {

                

                # Check user active status

                if ($userDtls['status'] == 'inactive') {

                    $this->result['message'] = "Your account has been blocked";

                    return $this->set_response($this->result, REST_Controller::HTTP_OK);

                }



                # Check if varified email address

                if ($userDtls['email_verified'] == 'false') {

                    verify_email($userDtls['id']);

                    $this->result['message'] = "Please verify your email address";

                    return $this->set_response($this->result, REST_Controller::HTTP_OK);

                }



                # Check if varified email address

                if ($userDtls['mobile_verified'] == 'false') {

                    $otp = rand(1111, 9999);

                    $this->Common->save(OTPS, [

                        'user_id'   => @$userDtls['id'], 

                        'mobile'    => @$userDtls['mobile'],

                        'otp'       => $otp,

                        'device_id' => @$data['fcmToken']

                    ]);

                    $otpMsg = "OTP has been sent to your mobile number";



                    #$email_message = $this->newotpemail_temp(@$userDtls['id'], $otp);

                    #$emailSent = email(DOMAIN_MAIL, @$userDtls['email'], "Blothru OTP ", $email_message);





                    $dial_code = (@$userDtls['dial_code'] != "") ? @$userDtls['dial_code'] : DEFAULT_DIAL_CODE;

                    $this->load->library('twilio');

                    $this->twilio->send($dial_code.@$userDtls['mobile'], otp_message($otp));

                }



                $this->result = [

                    'status'    => TRUE,

                    'data'      =>   get_customer_data($userDtls['id']),

                    'message'   => "Login Successfully"

                ];



                # storing login details

                $this->Common->save(USER_LOGINS, [

                    'user_id'       => $userDtls['id'],

                    'ip_address'    => $_SERVER['REMOTE_ADDR'],

                    'user_agent'    => @$this->headers['User-Agent'],

                    'fcmToken'      => (@$inputs['fcmToken']) ? $inputs['fcmToken'] : @$this->headers['Postman-Token'],

                    'login_token'   => $this->result['data']['token'],

                    'status'        => 'active'

                ]);



                if($otpMsg){

                    $this->result['status'] = TRUE;

                    $this->result['message'] = $otpMsg;

                }

                return $this->response($this->result, REST_Controller::HTTP_OK);

            } else {



                $this->result['message'] = "Wrong email address or password entered";

                return $this->set_response($this->result, REST_Controller::HTTP_UNAUTHORIZED);

            }

        }

        var_dump($users);
    }

    public function categorywise_product_list_post(){

        

        $this->form_validation->set_rules('category_id', 'Category ID', 'trim|required|numeric');

        $this->form_validation->set_rules('page', 'Page No', 'trim|numeric');

        $this->form_validation->set_rules('rec_per_page', 'Record per page', 'trim|numeric');

        

        # Check validation rule

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        #$category = $this->Common->findById(CATEGORIES, $this->params['category_id']);

        $category = $this->Common->find([

            'table'     => CATEGORIES,

            'select'    => "id, title",

            'where'     => "id = {$this->params['category_id']} AND status = 'A'",

            'query'     => 'first'

        ]);



        # Check if category exist

        if(empty($category)){

            $this->result['message'] = "Invalid category selected";

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        # Product list

        $products = $this->Common->find([

            'table'     => PRODUCT_CATEGORIES.' rpc',

            'select'    => "rpc.product_id, rpc.category_id, 

                            rp.title, rp.pimg, rp.sort_desc, rp.music_category,

                            rp.status, rp.id, rp.features, 

                            rps.sku, rps.price, rps.sale_price, rps.qty",

            'join'      => [

                                [PRODUCTS, 'rp', 'INNER', "rpc.product_id = rp.id"],

                                [PRODUCT_SKUS, 'rps', 'INNER', "rps.product_id = rp.id"],

                            ],

            'where'     => "rpc.category_id = {$this->params['category_id']} AND rp.status = 'A'",

            'page'      => @$this->params['page'],

            'per_page'  => @$this->params['rec_per_page'],

        ]);



        # Total Product

        $products_count = $this->Common->find([

            'table'     => PRODUCT_CATEGORIES.' rpc',

            'join'      => [

                                [PRODUCTS, 'rp', 'INNER', "rpc.product_id = rp.id"],

                                [PRODUCT_SKUS, 'rps', 'INNER', "rps.product_id = rp.id"],

                            ],

            'where'     => "rpc.category_id = {$this->params['category_id']} AND rp.status = 'A'",

            'query'     => 'count'

        ]);



        $productList = [];

        if(!empty($products)){

            foreach($products AS $key => $eachProduct){

                $productList[$key] = $eachProduct;

                $productList[$key]['pimg'] = timthumb($productList[$key]['pimg'], 200, 200);

            }

        }



        $this->result['status'] = TRUE;

        $this->result['data']   = [

            'category' => $category,

            'total'    => $products_count,

            'products' => $productList

        ];

        return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

    }



    /**

     * -------------------------------------

     * PRODUCT DETAILS

     * -------------------------------------

     * @method POST

     *

     * @param product_id   : (number) | REQUIRED    

     *

     * @return json array()

     */



    public function product_details_post(){  

       $this->form_validation->set_rules('product_id', 'Product ID', 'trim|required|numeric');



        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        $product = $this->Common->find([

            'table'     => PRODUCTS.' rp',

            'select'    => "rp.title, rp.pimg, rp.sort_desc, 

                            rp.status, rp.id, rp.features, rp.music_category,

                            rps.sku, rps.price, rps.sale_price, rps.qty",

            'join'      => [                               

                                [PRODUCT_SKUS, 'rps', 'INNER', "rps.product_id = rp.id"],

                            ],

            'where'     => "rp.id = {$this->params['product_id']}",

            'query'     => 'first'           

        ]);



        if(empty($product)){

            $this->result['message'] = "Invalid product selected";

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        # Update image path

        $product['pimg'] = timthumb($product['pimg'], 200, 200);



        $this->result['status']             = TRUE;

        $this->result['data']['product']    = $product;



        return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

    }    





       /**

     * -------------------------------------

     * ORDER PLACE

     * -------------------------------------

     * @method POST

     *

     * @param user_id   : (number) | REQUIRED

     * @param total_price          :  REQUIRED

     * 

     *

     * @return json array()

     */

    public function order_place_post(){



        $this->form_validation->set_rules('user_id', 'User ID', 'required|numeric');

        $this->form_validation->set_rules('total_price', 'Total Price', 'required');



         # Check validation rule

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        #Order Array Insert

        $shipping_address="6";

        $billing_address="6";

        $coupon_id="1";

        $discount_amount="00.00";

        $tax_amount="00.00";

        $shipping_charge="00.00";

        $payment_mode='O';

        $payment_status="P";



        $data_Order=array(



                'user_id'=>$this->params['user_id'],

                'orderID'=>'RR-'.rand(00000,99999).'-'.date('Y-m-d'),

                'shipping_address'=>$shipping_address,

                'billing_address'=>$billing_address,

                'coupon_id'=>$coupon_id,

                'discount_amount'=>$discount_amount,

                'tax_amount'=>$tax_amount,

                'shipping_charge'=>$shipping_charge,

                'payment_mode'=>$payment_mode,

                'payment_status'=>$payment_status,

                'total_amount'=>$this->params['total_price'],

                'created'=>date('Y-m-d')



        );



         $orderId = $this->Common->save(ORDERS, $data_Order);



         

         $OrderDetails = $this->Common->find([

            'table'     => CART_ITEMS.' ct',

            'select'    => "ct.product_id, ct.sku_id, ct.qty, 

                            ct.user_id",

            

            'where'     => "ct.user_id = {$this->params['user_id']}",

                      

        ]);



         //print_r($OrderDetails);exit;



          #OrderDetails Array Check



          if(empty($OrderDetails)){

            $this->result['message'] = "Invalid User Id";

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        #OrderDetails Array Insert

        foreach($OrderDetails as $details)

        {

             $data_OrderDetails=array(

            'order_id'=>$orderId,

            'product_id'=>@$details['product_id'],

            'sku_id'=>@$details['sku_id'],

            'qty'=>@$details['qty'],

            'rate'=>"00.00",

            

            );



            $this->Common->save(ORDER_ITEMS, $data_OrderDetails);

        }

        $this->db->where('user_id',$this->params['user_id']);

        $this->db->delete(CART_ITEMS);



        //$this->Common->deleteAll(CART_ITEMS,'user_id',$this->params['user_id']);

        $this->result['status']             = TRUE;

       

 

        return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);



   



    }

    

}





