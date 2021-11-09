<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Salon extends REST_Controller {
    private $result = [];
    private $userId = "";
    private $token  = "";
    private $user   = "";
    private $headers= [];
    private $params = [];
    private $salon  = [];
    private $salonId= '';

    public function __construct() {
        parent::__construct();
        $this->load->model('Salons');
        $this->load->library('Authorization_Token');
        $this->result = ['status' => FALSE, 'message' => "", 'data' => []];

        # Input headers
        $this->headers    = $this->input->request_headers();

        # Input params
        $this->params = $this->post();
        
        # return if token not found
        if(empty(@$this->headers['Authorization'])){
            $this->result['message'] = "Token not found";
            return $this->response($this->result, REST_Controller::HTTP_UNAUTHORIZED);
        }

        # Check login token
        $token_data = $this->authorization_token->validateToken();

        if ($token_data['status'] === false) {
            $this->result['message'] = @$token_data['message'];
            return $this->response($this->result, REST_Controller::HTTP_UNAUTHORIZED);
        } else {
            # Fetch User Details
            $auth_data      = $this->authorization_token->userData();
            $this->userId   = $auth_data->id;
            $this->user     = $this->Common->findById(USERS, $this->userId);
            if(empty($this->user)){
                $this->result['message'] = "Invalid token entered";
                return $this->response($this->result, REST_Controller::HTTP_UNAUTHORIZED);
            }
            $this->token = $this->headers['Authorization'];
            $this->salon = $this->Common->find([
                'table'     => SALONS,
                'where'     => "user_id = '{$this->userId}'",
                'query'     => 'first'
            ]);
            $this->salonId = $this->salon['id'];
        }
    }


    /**
     *------------------------------------------------
     * USER DETAILS
     *------------------------------------------------
     *
     * Fetch user details depending on user token
     *
     * @return json array()
     */
    public function user_details_post(){
        $this->result['status'] = TRUE;
        $userDtls   = $this->Common->find([
            'table'     => USERS." User",
            'select'    => "User.*,  
                            Profile.fname AS first_name, 
                            Profile.lname AS last_name, 
                            Profile.gender, 
                            Profile.dob AS birth_date, 
                            Profile.zip AS zipCode,
                            Profile.state, 
                            Profile.city, 
                            Profile.country,
                            Salon.name AS salon_name, 
                            CONCAT('" . USER_IMGPATH . "',User.profile_img ) profile_image",
            'join'      => [
                                [USER_PROFILE, 'Profile', 'INNER', "User.id = Profile.user_id"],
                                [SALONS, 'Salon', 'INNER', "User.id = Salon.user_id"],
                            ],
            'where'     => "User.id = '{$this->userId}'",
            'query'     => 'first'
        ]);

        # Process response data
        $this->result['data']['profile'] = [
            'id'                => $userDtls['id'],
            'dial_code'         => $userDtls['dial_code'],
            'mobile'            => $userDtls['mobile'],
            'mobile_verified'   => $userDtls['mobile_verified'],
            'email'             => $userDtls['email'],
            'email_verified'    => $userDtls['email_verified'],
            'profile_id'        => $userDtls['profile_id'],
            'first_name'        => $userDtls['first_name'],
            'last_name'         => $userDtls['last_name'],
            'fill_name'         => $userDtls['first_name']." ".$userDtls['last_name'],
            'gender'            => $userDtls['gender'],
            'birth_date'        => $userDtls['birth_date'],
            'city'              => $userDtls['city'],
            'state'             => $userDtls['state'],
            'country'           => $userDtls['country'],
            'zip_code'          => $userDtls['zipCode'],
            'salon_name'        => $userDtls['salon_name'],
            'profile_image'     => timthumb($userDtls['profile_img'], 150, 150)
        ];

        $this->result['status']     = TRUE;
        $this->result['currency']   = CURRENCY;

        $this->result['data']['token'] = $this->authorization_token->generateToken([
                'id'            => $userDtls['id'],
                'email'         => $userDtls['email'],
                'profile_id'    => $userDtls['profile_id'],
                'mobile'        => $userDtls['mobile'],
                'time'          => time()
            ]);
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * SALON DETAILS
     *------------------------------------------------
     *
     * @return json array()
     */
    public function salon_details_post(){
        $salon = $this->Common->findById(SALONS, $this->salonId);
        $this->result['status'] = TRUE;
        if(!empty($salon)){
            $this->result['data']['salon'] = [
                'title'             => $salon['name'],
                'caption'           => $salon['caption'],
                'description'       => $salon['description'],
                'address'           => $salon['address'],
                'street_address'    => $salon['street_address'],
                'city'              => $salon['city'],
                'state'             => $salon['state'],
                'zip'               => $salon['zip'],
                'country'           => $salon['country'],
                'lattitude'         => $salon['lat'],
                'longitude'         => $salon['lng'],
                'service_area'      => $salon['area'],
                'store_img'         => timthumb($salon['store_img']),
                'dial_code'         => $salon['dial_code'],
                'contact_no'        => $salon['contact_no'],
                'email'             => $salon['email'],
                'home_service'      => $salon['home_service'],
                'on_visit'          => $salon['on_visit'],
                'rating'            => $salon['rating']
            ];
        }
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * UPDATE SALON
     *------------------------------------------------
     *
     * @param salon_name    	: (string)          | REQUIRED
     * @param caption       	: (string)          | NULL
     * @param description   	: (string)          | NULL
     * @param dial_code     	: (int)             | NULL
     * @param contact_no    	: (string)          | REQUIRED
     * @param email         	: (string)          | REQUIRED
     * @param home_service  	: (enum[true,false])| REQUIRED
     * @param on_visit      	: (enum[true,false])| REQUIRED
     * @param service_area  	: (float)           | REQUIRED
     * @param street_address    : (string) 			| REQUIRED
     * @param city              : (string) 			| REQUIRED
     * @param state             : (string) 			| REQUIRED
     * @param zip               : (string) 			| REQUIRED
     * @param country           : (string) 			| REQUIRED
     * @param lattitude         : (double) 			| REQUIRED
     * @param longitude         : (double) 			| REQUIRED
     *
     * @return json array()
     */
    public function salon_update_post(){
        # Set validation rule
        $this->form_validation->set_rules('salon_name', 'Salon name', 'trim|required|min_length[3]|max_length[128]');
        $this->form_validation->set_rules('contact_no', 'Contact number', 'trim|required|min_length[8]|max_length[15]');
        $this->form_validation->set_rules('home_service', 'Home service', 'trim|required|enum[true,false]');
        $this->form_validation->set_rules('on_visit', 'On visit', 'trim|required|enum[true,false]');
        $this->form_validation->set_rules('service_area', 'Service area', 'trim|required|numeric');
        # Address
        $this->form_validation->set_rules('street_address', 'Street Address', 'trim|required|min_length[2]|max_length[255]');
        $this->form_validation->set_rules('city', 'City', 'trim|required|min_length[2]|max_length[64]');
        $this->form_validation->set_rules('state', 'State', 'trim|required|min_length[2]|max_length[64]');
        $this->form_validation->set_rules('zip', 'Zip Code', 'trim|required|min_length[2]|max_length[16]');
        $this->form_validation->set_rules('country', 'Country', 'trim|required|min_length[2]|max_length[64]');
        $this->form_validation->set_rules('lattitude', 'Lattitude', 'trim|required|numeric');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required|numeric');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Process address
        $addressArr = [
            @$this->params['street_address'],@$this->params['city'],@$this->params['state'],
            @$this->params['zip'],@$this->params['country']
        ];

        # Process salon data
        $salonArr = [
            'id'            	=> $this->salonId,
            'name'          	=> @$this->params['salon_name'],
            'caption'       	=> @$this->params['caption'],
            'description'   	=> @$this->params['description'],
            'dial_code'     	=> @$this->params['dial_code'],
            'contact_no'    	=> @$this->params['contact_no'],
            'email'         	=> @$this->params['email'],
            'home_service'  	=> @$this->params['home_service'],
            'on_visit'      	=> @$this->params['on_visit'],
            'area'          	=> @$this->params['service_area'],

            'address'           => implode(', ', array_filter($addressArr)),
            'street_address'    => @$this->params['street_address'],
            'city'              => @$this->params['city'],
            'state'             => @$this->params['state'],
            'zip'               => @$this->params['zip'],
            'country'           => @$this->params['country'],
            'lat'               => @$this->params['lattitude'],
            'lng'               => @$this->params['longitude']
        ];

        # Save updated salon record
        if($this->Common->save(SALONS, $salonArr)){
            $this->result['status'] = TRUE;
            $this->result['message'] = "Salon updated successfully";
        } else{
            $this->result['message'] = "Oops! something went wrong";
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * UPDATE SALON ADDRESS
     *------------------------------------------------
     *
     * @param street_address    : (string) | REQUIRED
     * @param city              : (string) | REQUIRED
     * @param state             : (string) | REQUIRED
     * @param zip               : (string) | REQUIRED
     * @param country           : (string) | REQUIRED
     * @param lattitude         : (double) | REQUIRED
     * @param longitude         : (double) | REQUIRED
     *
     * @return json array()
     */
    public function update_salon_address_post(){
        # Set validation rule
        $this->form_validation->set_rules('street_address', 'Street Address', 'trim|required|min_length[2]|max_length[255]');
        $this->form_validation->set_rules('city', 'City', 'trim|required|min_length[2]|max_length[64]');
        $this->form_validation->set_rules('state', 'State', 'trim|required|min_length[2]|max_length[64]');
        $this->form_validation->set_rules('zip', 'Zip Code', 'trim|required|min_length[2]|max_length[16]');
        $this->form_validation->set_rules('country', 'Country', 'trim|required|min_length[2]|max_length[64]');
        $this->form_validation->set_rules('lattitude', 'Lattitude', 'trim|required|numeric');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required|numeric');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }
        
        # Process address
        $addressArr = [
            @$this->params['street_address'],@$this->params['city'],@$this->params['state'],
            @$this->params['zip'],@$this->params['country']
        ];

        # Process salon data
        $salonArr = [
            'id'                => $this->salonId,
            'address'           => implode(', ', array_filter($addressArr)),
            'street_address'    => @$this->params['street_address'],
            'city'              => @$this->params['city'],
            'state'             => @$this->params['state'],
            'zip'               => @$this->params['zip'],
            'country'           => @$this->params['country'],
            'lat'               => @$this->params['lattitude'],
            'lng'               => @$this->params['longitude']
        ];

        # Save updated salon record
        if($this->Common->save(SALONS, $salonArr)){
            $this->result['status'] = TRUE;
            $this->result['message'] = "Salon address updated successfully";
        } else{
            $this->result['message'] = "Oops! something went wrong";
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    
    /**
     *------------------------------------------------
     * UPLOAD SALON IMAGE
     *------------------------------------------------
     *
     * @param image_file : (file)       | REQUIRED
     * @param is_default : (boolean)    | NULL <true/false>
     *
     * @return json array()
     */
    public function upload_salon_image_post(){
        # Set validation rule
        $this->form_validation->set_rules('is_default', 'Default image', 'trim|enum[true,false]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Check if file exist
        if(empty($_FILES) || @$_FILES['image_file']['name'] == ""){
            $this->result['message'] = "Select an image file";
            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Configure image size, type
        $file_path = 'images/salons/';
        $base = get_dir($file_path);
        $imgconfig['upload_path']   = $base;
        $imgconfig['max_size']      = 1024 * 2;
        $imgconfig['allowed_types'] = 'png|jpg|jpeg';
        $imgconfig['encrypt_name']  = TRUE;
        $this->load->library('upload', $imgconfig);

        # Upload file
        if($this->upload->do_upload('image_file')) {
            $upload_data    = $this->upload->data();
            $imgname        = $upload_data['file_name'];

            $is_default = 1;

            # Check if another default image exist or not
            if(@$this->params['is_default'] != 'true'){
                $defaultImage = $this->Common->find([
                    'table'     => IMAGES,
                    'where'     => "table_const = 'SALONS' AND 
                                    table_id = '{$this->salonId}' AND 
                                    is_default = '1' AND status = 'active'",
                    'query'     => 'first'
                ]);

                # If already default image found then set to 0
                if(!empty($defaultImage)){
                    $is_default = 0;
                }
            }

            # Process image DB data
            $imageArr = [
                'image_name'    => $imgname,
                'table_const'   => 'SALONS',
                'table_id'      => @$this->salonId,
                'file_path'     => $file_path.$imgname,
                'is_default'    => $is_default
            ];
            # Save image details
            $imageId = $this->Common->save(IMAGES, $imageArr);

            if($is_default == 1){
                $salonArr = [
                    'id'        => $this->salonId,
                    'store_img' => $file_path.$imgname,
                    'image_id'  => $imageId
                ];

                $this->Common->save(SALONS, $salonArr);
            }

            $this->result['status']     = TRUE;
            $this->result['message']    = "Image uploaded successfully";

            $salon = $this->Common->findById(SALONS, $this->salonId);

	        if(!empty($salon)){
	            $this->result['data']['salon'] = [
	                'title'             => $salon['name'],
	                'caption'           => $salon['caption'],
	                'description'       => $salon['description'],
	                'address'           => $salon['address'],
	                'street_address'    => $salon['street_address'],
	                'city'              => $salon['city'],
	                'state'             => $salon['state'],
	                'zip'               => $salon['zip'],
	                'country'           => $salon['country'],
	                'lattitude'         => $salon['lat'],
	                'longitude'         => $salon['lng'],
	                'service_area'      => $salon['area'],
	                'store_img'         => timthumb($salon['store_img']),
	                'dial_code'         => $salon['dial_code'],
	                'contact_no'        => $salon['contact_no'],
	                'email'             => $salon['email'],
	                'home_service'      => $salon['home_service'],
	                'on_visit'          => $salon['on_visit'],
	                'rating'            => $salon['rating']
	            ];
	        }
        }else{
            $this->result['message']    = strip_tags(str_replace("\n", '', $this->upload->display_errors()));
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * SALON DASHBOARD 
     *------------------------------------------------
     *
     *
     * @return json array()
     */
    public function salon_dashboard_post(){
        $record_per_page    = (@$this->params['rec_per_page']) ? $this->params['rec_per_page'] : 2;
        $page_no            = (@$this->params['page_no']) ? $this->params['page_no'] : 1;

        # total no of products
        $this->result['data']['total']['products'] = $this->Common->find([
            'table' => PRODUCTS,
            'where' => "salon_id = '{$this->salonId}'",
            'query' => 'count'
        ]);

        # total no of bookings
        $this->result['data']['total']['bookings'] = $this->Common->find([
            'table' => BOOKINGS,
            'where' => "salon_id = '{$this->salonId}'",
            'query' => 'count'
        ]);

        # Total no of Orders
        $orderJoin = [
            [ORDER_PRODUCTS, "Ord_product", "INNER", "Ord_product.order_id = Order.id"],
            [PRODUCTS, "Product", "INNER", "Ord_product.product_id = Product.id"]
        ];
        $this->result['data']['total']['orders'] = $this->Common->find([
            'table'     => ORDERS." Order",
            'select'    => "DISTINCT(Order.id)",
            'where'     => "Product.salon_id = '{$this->salonId}'",
            'join'      => $orderJoin,
            'query'     => 'count'
        ]);

        $monthly_sale = $this->Common->find([
            'table'     => PROVIDER_TXNS,
            'select'    => "SUM(salon_amount) AS total",
            'where'     => "salon_id = '{$this->salonId}' AND MONTH(created) = MONTH(CURRENT_DATE()) AND YEAR(created) = YEAR(CURRENT_DATE())",
            'query'     => 'first'
        ]);
        # Total monthly sale
        $this->result['data']['total']['monthly_sale'] = (@$monthly_sale['total'] > 0) ? (double) @$monthly_sale['total'] : 0;  

        $this->result['status'] = TRUE;
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * SALON ORDERS
     *------------------------------------------------
     *
     * @param rec_per_page  : (int) | NULL | <Record Per Page>
     * @param page_no       : (int) | NULL | <Page Number>
     * @param sort          : (enum)| NULL | <ordered_on DESC,ordered_on ASC,total_price DESC,total_price ASC>
     * @param from_date     : (date)| NULL | <valid date>
     * @param to_date       : (date)| NULL | <valid date>
     *
     * @return json array()
     */
    public function salon_orders_post(){
        # Set validation rule
        $this->form_validation->set_rules('rec_per_page', 'Record per page', 'numeric|trim');
        $this->form_validation->set_rules('page_no', 'Page no', 'numeric|trim');
        $this->form_validation->set_rules('sort', 'Sort', 'trim|enum[ordered_on DESC,ordered_on ASC,total_price DESC,total_price ASC]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Set conditions
        $conditions = "Product.salon_id = '{$this->salonId}'";

        # filter by date
        if(@$this->params['from_date'] != ""){
            $conditions .= " AND Order.created >= '".date("Y-m-d 00:00:00", strtotime($this->params['from_date']))."' ";
        }
        if(@$this->params['to_date'] != ""){
            $conditions .= " AND Order.created <= '".date("Y-m-d 23-59-59", strtotime($this->params['to_date']))."' ";
        }

        # Set sort, by default latest order first
        if(@$this->params['sort'] != ""){
            $sort = $this->params['sort'];
        }else{            
            $sort = "Order.created DESC";
        }

        # Fetch last 2 orders
        $orders = $this->Common->find([
            'select'    => "Order.id, Order.created AS ordered_on,
                            Order.orderno AS order_number, 
                            Product.id AS product_id, Product.pname, Product.pimg, Ord_product.qty, 
                            Ord_product.id AS ord_product_id, Ord_product.price AS pro_price, 
                            Ord_product.order_status AS pro_ord_status, (Ord_product.price * Ord_product.qty) AS total_price",
            'table'     => ORDERS." Order",
            'join'      =>  [
                                [ORDER_PRODUCTS, "Ord_product", "INNER", "Ord_product.order_id = Order.id"],
                                [PRODUCTS, "Product", "INNER", "Ord_product.product_id = Product.id"]
                            ],
            'where'     => $conditions,
            'order'     => $sort,
            'per_page'  => @$this->params['rec_per_page'],
            'page'      => @$this->params['page_no']
        ]);

        # Count total records
        $this->result['data']['total'] = $this->Common->find([
            'table'     => ORDERS." Order",
            'join'      =>  [
                                [ORDER_PRODUCTS, "Ord_product", "INNER", "Ord_product.order_id = Order.id"],
                                [PRODUCTS, "Product", "INNER", "Ord_product.product_id = Product.id"]
                            ],
            'where'     => $conditions,
            'order'     => "Order.created DESC", 
            'query'     => 'count'
        ]);

        $order_status = [
            'A' => 'accepted', 
            'D' => 'declined',  
            'C' => 'completed', 
            'P' => 'Order Placed', 
            'O' => 'Processing', 
            '' => 'N/A'
        ];
        
        $this->result['data']['orders'] = [];
        if(!empty($orders)){
            foreach($orders AS $key => $eachOrder){
                $this->result['data']['orders'][] = [
                    'id'            => $eachOrder['id'],
                    'ordered_on'    => date('d F Y h:i a', strtotime($eachOrder['ordered_on'])),
                    'order_number'  => $eachOrder['order_number'],
                    'product_id'    => $eachOrder['product_id'],
                    'product_name'  => $eachOrder['pname'],
                    'product_image' => timthumb($eachOrder['pimg'], 100),
                    'product_price' => number_format($eachOrder['pro_price'], 2),
                    'qty'           => (int) $eachOrder['qty'],
                    'total_price'   => number_format(($eachOrder['pro_price'] * $eachOrder['qty']), 2),
                    'ordered_status'=> @$order_status[$eachOrder['pro_ord_status']]
                ];
            }
        }

        $this->result['status'] = TRUE;
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * ORDER DETAILS
     *------------------------------------------------
     *
     * @param order_id  : (int) | REQUIRED
     *
     * @return json array()
     */
    public function order_details_post(){
        # Set validation rule
        $this->form_validation->set_rules('order_id', 'Order ID', 'required|numeric|trim');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->params = $this->post();

        # Fetch order details
        $order = $this->Common->findById(ORDERS, $this->params['order_id']);
        if(empty($order)){
            $this->result['message'] = "Order not found";
            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
        }

        $this->result['status'] = TRUE;
        # Process order data
        $this->result['data']['order'] = [
            'id'            => $order['id'],
            'orderno'       => $order['orderno'],
            'username'      => $order['username'],
            'emailid'       => $order['emailid'],
            'grandtotal'    => 0,
            'phoneno'       => $order['phoneno'],
            'created'       => $order['created']
        ];

        # Fetch order items
        $products = $this->Common->find([
            'table'     => ORDER_PRODUCTS." Ord_product",
            'select'    => "Product.id AS product_id, Product.pname, Product.pimg, Ord_product.qty, 
                            Ord_product.id AS ord_product_id, Ord_product.price AS pro_price, 
                            Ord_product.order_status AS pro_ord_status, (Ord_product.price * Ord_product.qty) AS total_price",
            'join'      => [[PRODUCTS, "Product", "INNER", "Ord_product.product_id = Product.id"]],
            'where'     => "Ord_product.order_id = '{$this->params['order_id']}'"
        ]);
        $order_status = [
            'A' => 'Accepted', 
            'D' => 'Declined',  
            'C' => 'Completed', 
            'P' => 'Order Placed', 
            'O' => 'Processing', 
            ''  => 'N/A'
        ];

        # Process Order Items
        $this->result['data']['order']['products'] = [];
        $grandtotal = 0;
        if(!empty($products)){
            foreach($products AS $key => $eachProduct){
                $this->result['data']['order']['products'][$key] = [
                    'product_id'        => $eachProduct['product_id'],
                    'product_name'      => $eachProduct['pname'],
                    'product_image'     => timthumb($eachProduct['pimg'], 150),
                    'product_price'     => $eachProduct['pro_price'],
                    'qty'               => $eachProduct['qty'],
                    'total_price'       => $eachProduct['total_price'],
                    'status_key' 		=> $eachProduct['pro_ord_status'],
                    'ordered_status'    => @$order_status[$eachProduct['pro_ord_status']]
                ];
                $grandtotal += $eachProduct['total_price'];
            }
        }
        $this->result['data']['order']['grandtotal'] = number_format($grandtotal, 2);
        
        # Fetch shipping & billing data
        $order_meta  = $this->Common->findBy(ORDERMETA, "order_id", $this->params['order_id']);
        
        # Process Billing address
        $billing_data = unserialize(@$order_meta['billing_data']);
        $full_address = [
            @$billing_data['addressl1'], @$billing_data['addressl2'], @$billing_data['city'], @$billing_data['state'], @$billing_data['zipcode'], @$billing_data['country']
        ];
        $billing_data['full_address'] = implode(', ', array_filter($full_address));
        $this->result['data']['order']['billing_address']   = $billing_data;

        # Process shipping address
        $shipping_data = unserialize(@$order_meta['shipping_data']);
        $full_address = [
            @$shipping_data['addressl1'], @$shipping_data['addressl2'], @$shipping_data['city'], @$shipping_data['state'], @$shipping_data['zipcode'], @$shipping_data['country']
        ];
        $shipping_data['full_address'] = implode(', ', array_filter($full_address));
        $this->result['data']['order']['shipping_address']  = $shipping_data;

        # Order actions
        $this->result['actions'] = [
            ['key' => 'A', 'label' => 'Accepted'],
            ['key' => 'D', 'label' => 'Declined'],
            ['key' => 'C', 'label' => 'Completed'],
            ['key' => 'P', 'label' => 'Order Placed'],
            ['key' => 'O', 'label' => 'Processing']
        ];

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * CHANGE ORDER STATUS
     *------------------------------------------------
     *
     * @param order_id  	: (int) | REQUIRED
     * @param product_id  	: (int) | REQUIRED
     * @param action  		: (enum)| REQUIRED [A,D,C,P,O]
     */
    public function change_order_status_post(){
    	# Set validation rule
        #$this->result['params'] = $this->params;
        $this->form_validation->set_rules('order_id', 'Order ID', 'required|numeric|trim');
        $this->form_validation->set_rules('product_id', 'Product ID', 'required|numeric|trim');
        $this->form_validation->set_rules('action', 'Action', 'required|trim|enum[A,D,C,P,O]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->params = $this->post();

        $order = $this->Common->find([
        	'table'    => ORDER_PRODUCTS." Ordproduct",
            'select'   => "Ordproduct.*, Product.pname, Product.pimg",
            'join'     => [[PRODUCTS, 'Product', 'INNER', "Product.id = Ordproduct.product_id"]],
        	"where"    => "Ordproduct.order_id = '{$this->params['order_id']}' AND 
        				   Ordproduct.product_id	= '{$this->params['product_id']}' AND 
        				   Ordproduct.salon_id = '{$this->salonId}'",
        	"query"    => "first"
        ]);

        if(empty($order)){
            $this->result['message'] = "Order not found";
            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
        }

        if($this->Common->save(ORDER_PRODUCTS, ['id' => $order['id'], 'order_status' => $this->params['action']])){
        	$this->result['status'] 	= TRUE;
        	$this->result['message'] 	= "Order status changed successfully";

            if($this->params['action'] == 'A'){
                $statusMsg = "Seller accepted your order";
            } elseif($this->params['action'] == 'D'){
                $statusMsg = "Seller declined your order";
            } elseif($this->params['action'] == 'C'){
                $statusMsg = "Delivered successfully";
            } elseif($this->params['action'] == 'O'){
                $statusMsg = "Your order recieved by seller";
            }
            $pImage = ($order['pimg'] != "") ? UPLOAD_PATH.$order['pimg'] : "";
            blothru_notification($order['user_id'], $statusMsg, $order['pname'], $pImage);
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * SALON BOOKINGS
     *------------------------------------------------
     *
     * @param rec_per_page  : (int) | NULL | <Record Per Page>
     * @param page_no       : (int) | NULL | <Page Number>
     * @param sort          : (enum)| NULL | <booked_on DESC,booked_on ASC,total_price DESC,total_price ASC>
     * @param from_date     : (date)| NULL | <valid date>
     * @param to_date       : (date)| NULL | <valid date>
     *
     * @return json array()
     */
    public function salon_bookings_post(){
        #return response($this->params);
        # Set validation rule
        $this->form_validation->set_rules('rec_per_page', 'Record per page', 'numeric|trim');
        $this->form_validation->set_rules('page_no', 'Page no', 'numeric|trim');
        $this->form_validation->set_rules('sort', 'Sort', 'trim|enum[booked_on DESC,booked_on ASC,total_price DESC,total_price ASC]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Set conditions
        $conditions = "Booking.salon_id = '{$this->salonId}'";

        # filter by date
        if(@$this->params['from_date'] != ""){
            $conditions .= " AND Booking.created >= '".date("Y-m-d 00:00:00", strtotime($this->params['from_date']))."' ";
        }
        if(@$this->params['to_date'] != ""){
            $conditions .= " AND Booking.created <= '".date("Y-m-d 23-59-59", strtotime($this->params['to_date']))."' ";
        }

        # Set sort, by default latest order first
        if(@$this->params['sort'] != ""){
            $sort = $this->params['sort'];
        }else{            
            $sort = "Booking.created DESC";
        }

        # Fetching bookings
        $bookings = $this->Common->find([
            'table'     => BOOKINGS." Booking", 
            'select'    => "Booking.id AS booking_id, Booking.bookingno, BookedService.*, CONCAT(Profile.fname, ' ' ,Profile.lname) AS customer_name, IF(Booking.servicetype = 1, 'Home Service', 'On Visit') service_location, 
                            Cat.title AS cat_title, Services.type_name, Booking.created AS booked_on, 
                            (BookedService.rate * BookedService.qty) AS total_price, 
                            (BookedService.time * BookedService.qty) total_time", 
            'join'      => [
                            [BOOKED_SERVICES, 'BookedService', 'INNER', "BookedService.order_id = Booking.id"],
                            [SERVICES_TYPE, 'Services', 'INNER', "BookedService.service_id = Services.id"],
                            [CATEGORIES, 'Cat', 'INNER', "Services.service_cat_id = Cat.id"],
                            [USER_PROFILE, 'Profile', 'INNER', "Profile.user_id = Booking.user_id"]
                        ],
            'where'     => $conditions,
            'order'     => $sort,
            'per_page'  => @$this->params['rec_per_page'],
            'page'      => @$this->params['page_no']
        ]);
        #pre(last_query(), 1);
        $this->result['data']['bookings'] = [];
        if(!empty($bookings)){
            foreach($bookings AS $eachBooking){
                $this->result['data']['bookings'][] = [
                    'booking_id'        => @$eachBooking['booking_id'],
                    'booking_number'    => @$eachBooking['bookingno'],
                    'category'          => @$eachBooking['cat_title'],
                    'service_title'     => @$eachBooking['type_name'],
                    'booked_on'         => @$eachBooking['booked_on'],
                    'qty'               => @$eachBooking['qty'],
                    'rate'              => @$eachBooking['rate'],
                    'time'              => @$eachBooking['time'],
                    'total_time'        => @$eachBooking['total_time'],
                    'time_unit'         => @$eachBooking['time_unit'],
                    'total_price'       => @$eachBooking['total_price'],
                    'service_place'     => @$eachBooking['service_location'],
                    'customer_name'     => @$eachBooking['customer_name'],
                    'status'            => @$eachBooking['order_status'],
                ];
            }
        }

        # count bookings
        $this->result['data']['total'] = $this->Common->find([
            'table'     => BOOKINGS." Booking", 
            'join'      => [
                            [BOOKED_SERVICES, 'BookedService', 'INNER', "BookedService.order_id = Booking.id"],
                            [SERVICES_TYPE, 'Services', 'INNER', "BookedService.service_id = Services.id"],
                            [CATEGORIES, 'Cat', 'INNER', "Services.service_cat_id = Cat.id"],
                            [USER_PROFILE, 'Profile', 'INNER', "Profile.user_id = Booking.user_id"]
                        ],
            'where'     => $conditions,
            'query'     => 'count'
        ]);

        $this->result['status'] = TRUE;
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * BOOKING DETAILS
     *------------------------------------------------
     *
     * @param booking_id  : (int) | REQUIRED
     *
     * @return json array()
     */
    public function bookings_details_post(){
        # Set validation rule
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'required|numeric|trim');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }
        $this->params = $this->post();

        # Fetch booking details
        $this->result['data']['booking'] = $this->Common->find([
            'table'     => BOOKINGS." Booking", 
            'select'    => "id, bookingno, servicetype, fname, lname, emailid, phoneno, book_address, 
            				book_zipcode, CONCAT(booking_date, ' ', time_slot) AS time_slot, payment_mode, created",
            'where'     => "Booking.id = '{$this->params['booking_id']}'",
            'query'     => 'first'
        ]);

        if(empty($this->result['data']['booking'])){
	        $this->result['message'] = "Booking not found";
	        return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
	    }

	    $this->result['status'] = TRUE;

        # Update 'servicetype', change key to value
        $this->result['data']['booking']['servicetype'] = ($this->result['data']['booking']['servicetype'] == 1) ? 'Home Service' : 'On Visit ';

        # Fetch booked services and there status
        $joinArr = [
            [SERVICES_TYPE, 'Services', 'INNER', "BookedService.service_id = Services.id"],
            [CATEGORIES, 'Cat', 'INNER', "Services.service_cat_id = Cat.id"]
        ];

        $booked_items = $this->Common->find([
            'table'     => BOOKED_SERVICES." BookedService",
            'select'    => "BookedService.*, Cat.title AS cat_title, Services.type_name",
            'join'      => $joinArr,
            'where'     => "BookedService.order_id = '{$this->params['booking_id']}'"
        ]);

        # Process services
        $this->result['data']['booking']['grandtotal'] 	= $total_price = 0;
        $this->result['data']['booking']['services'] 	= [];
        if(!empty($booked_items)){
        	foreach($booked_items AS $key => $eachService){
        		$this->result['data']['booking']['services'][$key] = [
        			'service_id' 	=> $eachService['service_id'],
        			'rate' 			=> $eachService['rate'],
        			'qty' 			=> $eachService['qty'],
        			'total_price' 	=> (string) ($eachService['qty'] * $eachService['rate']),
        			'time' 			=> $eachService['time'],
        			'time_unit' 	=> $eachService['time_unit'],
        			'status' 		=> $eachService['status'],
        			'cat_title' 	=> $eachService['cat_title'],
        			'type_name' 	=> $eachService['type_name'],
        			'order_status' 	=> $eachService['order_status']
        		];

        		# Process total price of booking
        		$total_price += $eachService['qty'] * $eachService['rate'];
        	}
        }
        $this->result['data']['booking']['grandtotal'] = (string) $total_price;

        # Acctions to change order styatus
        $this->result['data']['actions'] = [
        	'pending','approved','canceled','completed'
        ];


        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * CHANGE BOOKING STATUS
     *------------------------------------------------
     *
     * @param booking_id  	: (int) | REQUIRED
     * @param service_id  	: (int) | REQUIRED
     * @param action  		: (enum) | REQUIRED ['pending','approved','canceled','completed']
     *
     * @return json array()
     */
	public function change_booking_status_post(){
		# Set validation rule
        $this->form_validation->set_rules('booking_id', 'Booking ID', 'required|numeric|trim');
        $this->form_validation->set_rules('service_id', 'Service ID', 'required|numeric|trim');
        $this->form_validation->set_rules('action', 'Action', 'required|trim|enum[pending,approved,canceled,completed]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }
        $this->params = $this->post();

        $service = $this->Common->find([
            'table'     => BOOKED_SERVICES." BookedService",
            'select'    => "BookedService.*, Booking.salon_id, Booking.bookingno",
            'join'      => [[BOOKINGS, 'Booking', 'INNER', "Booking.id = BookedService.order_id"]],
            'where' 	=> "BookedService.order_id = '{$this->params['booking_id']}' AND  
            				BookedService.service_id = '{$this->params['service_id']}'",
            'query' 	=> 'first'
        ]);        

        if(empty($service)){
	        $this->result['message'] = "Booking not found";
	        return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
	    }

        if($this->Common->save(BOOKED_SERVICES, ['id' => $service['id'], 'order_status' => $this->params['action']])){
        	$this->result['status'] 	= TRUE;
        	$this->result['message'] 	= "Status changed successfully";

            provider_notification($service['user_id'], "Your booking {$service['bookingno']} is ".$this->params['action'], "Booking Status");
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
	}


    /**
     *------------------------------------------------
     * CATEGORY LIST 
     *------------------------------------------------
     *
     * @return json array()
     */
    public function category_list_post(){
        # Set validation rule
        $this->form_validation->set_rules('type', 'Type', 'trim|required|enum[p,s]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $this->result['status'] = TRUE;
        $this->result['data']['categories'] = categories(@$this->params['type']);
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * SALON PRODUCT LIST 
     *------------------------------------------------
     *
     * @param rec_per_page  : (int)     | NULL | <Record Per Page>
     * @param page_no       : (int)     | NULL | <Page Number>
     * @param search        : (string)  | NULL | <Product Name>
     * @param categories    : (array)   | NULL | <Category ID>
     *
     * @return json array()
     */
    public function product_list_post(){
        # Set validation rule
        $this->form_validation->set_rules('rec_per_page', 'Record per page', 'numeric|trim');
        $this->form_validation->set_rules('page_no', 'Page no', 'numeric|trim');
        $this->form_validation->set_rules('search', 'Search', 'trim');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $conditions = "Product.salon_id = '{$this->salonId}'";
        if(@$this->params['search'] != ""){
            $conditions .= " AND Product.pname LIKE '%{$this->params['search']}%'";
        }
        
        if(@$this->params['categories'] != ""){
            $categoryArr = json_decode(@$this->params['categories'], true);
            /*if(empty($categoryArr)){
                $this->result['message'] = "Invalid categories format entered";
                return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
            }*/
            if(!empty($categoryArr)){
                $categories = implode(',', array_filter($categoryArr));
                if($categories != ""){
                    $conditions .= " AND (Category.id IN ({$categories}) OR Category.parent_id IN ({$categories}))";
                }
            }
        }

        $products = $this->Common->find([
            'table'     => PRODUCTS." Product",
            'select'    => "DISTINCT(Product.id) AS product_id, Product.*",
            'join'      =>  [
                                [PRODUCT_CATEGORY, 'PCat', 'LEFT', "PCat.product_id = Product.id"],
                                [CATEGORIES, 'Category', 'LEFT', "Category.id = PCat.category_id"]
                            ],
            'where'     => $conditions,
            'order'     => "Product.created DESC",
            'per_page'  => @$this->params['rec_per_page'],
            'page'      => @$this->params['page_no']
        ]);

        $this->result['data']['products'] = [];
        if(!empty($products)){
            foreach($products AS $eachProduct){
                $images = [];
                $imageQry = $this->Common->find([
                    'table'     => IMAGES,
                    'select'    => "id, file_path AS path, is_default",
                    'where'     => "table_const = 'PRODUCTS' AND 
                                    table_id = '{$eachProduct['product_id']}' AND 
                                    status = 'active'"
                ]);

                if(!empty($imageQry)){
                    foreach($imageQry AS $eachImage){
                        $eachImage['path'] = timthumb($eachImage['path'], 150);
                        $images[] = $eachImage;
                    }
                }

                # Fetching categoris name and ids from site_helper
                $categories = product_categories_list($eachProduct['product_id']);

                $this->result['data']['products'][] = [
                    'product_id'        => $eachProduct['product_id'],
                    'title'             => $eachProduct['pname'],
                    'categories'        => @$categories['title'],
                    'parent_categories' => @$categories['parent_categories'],
                    'child_categories'  => @$categories['child_categories'],
                    'slug'              => $eachProduct['slug'],
                    'caption'           => $eachProduct['caption'],
                    'image'             => timthumb($eachProduct['pimg']),
                    'stock'             => $eachProduct['stock'],
                    'actual_price'      => $eachProduct['actual_price'],
                    'discount'          => $eachProduct['discount'],
                    'discount_type'     => '%',
                    'price'             => $eachProduct['price'],
                    'description'       => @$eachProduct['description'],
                    'status'            => $eachProduct['status'],
                    'created'           => date("d F Y h:i a", strtotime($eachProduct['created'])),
                    'images'            => $images
                ];
            }
        }

        # Count all products
        $this->result['data']['total'] = $this->Common->find([
            'table'     => PRODUCTS." Product",
            'select'    => "DISTINCT(Product.id) AS product_id, Product.*",
            'join'      =>  [
                                [PRODUCT_CATEGORY, 'PCat', 'LEFT', "PCat.product_id = Product.id"],
                                [CATEGORIES, 'Category', 'LEFT', "Category.id = PCat.category_id"]
                            ],
            'where'     => $conditions,
            'query'     => 'count'
        ]);
        #pre(last_query(), 1);
        $this->result['status'] = TRUE;
        $this->result['salonId'] = $this->salonId;
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * ADD NEW PRODUCT OR UPDATE EXISTING PRODUCT
     *------------------------------------------------
     *
     * @param pname         : (string)  | REQUIRED | <Product Name>
     * @param caption       : (string)  | NULL     | <Product Name>
     * @param categories    : (array)   | REQUIRED | <Product Categories>
     * @param description   : (string)  | NULL
     * @param stock         : (int)     | REQUIRED | <Product Limit>
     * @param actual_price  : (double)  | REQUIRED | <MRP>
     * @param discount      : (double)  | REQUIRED | <Discount in %>
     * @param price         : (double)  | REQUIRED | <Final Price>
     * @param product_id    : (int)     | NULL     | <In case of update product>
     * @param status        : (enum[active,inactive])     | REQUIRED     | <In case of update product>
     *
     * @return json array()
     */
    public function add_update_product_post(){

        # Set validation rule
        $this->form_validation->set_rules('pname', 'Product name', 'required|trim|min_length[3]|max_length[128]');
        $this->form_validation->set_rules('description', 'Description', 'trim|min_length[3]|max_length[250]');
        $this->form_validation->set_rules('caption', 'Caption', 'trim|min_length[3]|max_length[128]');
        $this->form_validation->set_rules('stock', 'Stock', 'required|trim|min_max[0,10000]');
        $this->form_validation->set_rules('actual_price', 'Actual Price', 'required|trim|min_max[0,10000]');
        $this->form_validation->set_rules('discount', 'Discount', 'required|trim|min_max[0,100]');
        $this->form_validation->set_rules('price', 'Selling Price', 'required|trim|min_max[0,10000]');
        $this->form_validation->set_rules('categories', 'Categories', 'required|trim');
        $this->form_validation->set_rules('product_id', 'Product ID', 'trim|numeric');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|enum[active,inactive]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Set categorirs
        $categoryArr = json_decode(@$this->params['categories'], true);
        if(empty($categoryArr)){
            $this->result['message'] = "Invalid categories format entered";
            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }
        $categories = implode(',', $categoryArr);

        # Prepare product data
        $productArr = [
            'salon_id'      => $this->salonId, 
            'pname'         => @$this->params['pname'],
            'categories'    => $categories,
            'description'   => @$this->params['description'],
            'caption'       => @$this->params['caption'],
            'stock'         => @$this->params['stock'],
            'actual_price'  => @$this->params['actual_price'],
            'discount'      => @$this->params['discount'],
            'price'         => @$this->params['price'],
            'status'        => @$this->params['status'],
        ];

        # In case of update product
        if(@$this->params['product_id'] != "") {
            $productArr['id'] = $this->params['product_id']; 
        }else{
            $productArr['slug'] = slug(PRODUCTS, 'slug', $this->params['pname']); 
        }

        # Save product
        if($product = $this->Common->save(PRODUCTS, $productArr, true)){
            # Set product category in relational table
            $this->Common->dropAll(PRODUCT_CATEGORY, "product_id = '{$product['id']}'");
            if(!empty($categoryArr)){
                foreach($categoryArr AS $eachCat){
                    $this->Common->save(PRODUCT_CATEGORY, ['product_id' => $product['id'], 'category_id' => $eachCat]);
                }
            }
            $this->result['status']             = TRUE;
            $this->result['data']['product']    = $product;
            $this->result['message']            = "Product saved successfully";
        }else{
            $this->result['message'] = "Oops! something went wrong";
        }
        return $this->response($this->result, REST_Controller::HTTP_OK);
    } 

    /**
     *------------------------------------------------
     * REMOVE PRODUCT
     *------------------------------------------------
     *
     * @param product_id : (int) | REQUIRED
     *
     * @return json array()
     */
    public function remove_product_post(){
        # Set validation rule
        $this->form_validation->set_rules('product_id', 'Product ID', 'trim|required|numeric');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Fetch record if product exist
        $product = $this->Common->find([
            'table'     => PRODUCTS,
            'where'     => "id = '{$this->params['product_id']}' AND salon_id = '{$this->salonId}'",
            'query'     => 'first'
        ]);

        # Check if product return a value
        if(empty($product)){
            $this->result['message'] = "Invalid product ID entered";
            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
        }

        # Soft Delete Product
        if($this->Common->delete(PRODUCTS, $this->params['product_id'])){
            $this->result['status']     = TRUE;
            $this->result['message']    = "Product deleted successfully";
        } else{
            $this->result['message']    = "Oops! something went wrong";
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * UPLOAD PRODUCT IMAGE
     *------------------------------------------------
     *
     * @param product_id : (int)        | REQUIRED
     * @param image_file : (file)       | REQUIRED
     * @param is_default : (boolean)    | NULL <true/false>
     *
     * @return json array()
     */
    public function upload_product_image_post(){
        # Set validation rule
        $this->form_validation->set_rules('product_id', 'Product ID', 'trim|required|numeric');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Check if file exist
        if(empty($_FILES) || @$_FILES['image_file']['name'] == ""){
            $this->result['message'] = "Select an image file";
            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Fetch record if product exist
        $product = $this->Common->find([
            'table'     => PRODUCTS,
            'where'     => "id = '{$this->params['product_id']}' AND salon_id = '{$this->salonId}'",
            'query'     => 'first'
        ]);

        # Check if product return a value
        if(empty($product)){
            $this->result['message'] = "Invalid product ID entered";
            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
        }

        # Configure image size, type
        $file_path = 'images/products/';
        $base = get_dir($file_path);
        $imgconfig['upload_path']   = $base;
        $imgconfig['max_size']      = 1024 * 2;
        $imgconfig['allowed_types'] = 'png|jpg|jpeg';
        $imgconfig['encrypt_name']  = TRUE;
        $this->load->library('upload', $imgconfig);

        # Upload file
        if($this->upload->do_upload('image_file')) {
            $upload_data    = $this->upload->data();
            $imgname        = $upload_data['file_name'];

            $is_default = 1;

            # Check if another default image exist or not
            if(@$this->params['is_default'] != 'true'){
                $defaultImage = $this->Common->find([
                    'table'     => IMAGES,
                    'where'     => "table_const = 'PRODUCTS' AND 
                                    table_id = '{$this->params['product_id']}' AND 
                                    is_default = '1' AND status = 'active'",
                    'query'     => 'first'
                ]);

                # If already default image found then set to 0
                if(!empty($defaultImage)){
                    $is_default = 0;
                }
            }

            # Process image DB data
            $imageArr = [
                'image_name'    => $imgname,
                'table_const'   => 'PRODUCTS',
                'table_id'      => @$this->params['product_id'],
                'file_path'     => $file_path.$imgname,
                'is_default'    => $is_default
            ];
            # Save image details
            $image = $this->Common->save(IMAGES, $imageArr, true);

            if($is_default == 1){
                $productArr = [
                    'id'        => $this->params['product_id'],
                    'pimg'      => $file_path.$imgname,
                    'image_id'  => $image['id']
                ];

                $this->Common->save(PRODUCTS, $productArr);
            }

            $this->result['data']['image']  = [
                'id'            => $image['id'],
                'path'          => timthumb($image['file_path'], 150),
                'is_default'    => $image['is_default'],
            ];
            $this->result['status']     = TRUE;
            $this->result['message']    = "Image uploaded successfully";
        }else{
            $this->result['message']    = strip_tags(str_replace("\n", '', $this->upload->display_errors()));
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * REMOVE PRODUCT IMAGE
     *------------------------------------------------
     *
     * @param product_id : (int)  | REQUIRED
     * @param image_id   : (file) | REQUIRED
     *
     * @return json array()
     */
    public function remove_product_image_post(){
        # Set validation rule
        $this->form_validation->set_rules('product_id', 'Product ID', 'trim|required|numeric');
        $this->form_validation->set_rules('image_id', 'Image ID', 'trim|required|numeric');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Fetch record if product exist
        $product = $this->Common->find([
            'table'     => PRODUCTS,
            'where'     => "id = '{$this->params['product_id']}' AND salon_id = '{$this->salonId}'",
            'query'     => 'first'
        ]);

        # Check if product return a value
        if(empty($product)){
            $this->result['message'] = "Invalid product ID entered";
            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
        }

        $image = $this->Common->find([
            'table'     => IMAGES,
            'where'     => "id = '{$this->params['image_id']}' AND 
                            table_const = 'PRODUCTS' AND 
                            table_id = '{$this->params['product_id']}'",
            'query'     => 'first'
        ]);

        # Check if image exist
        if(empty($image)){
            $this->result['message'] = "Invalid image ID entered";
            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
        }

        if($this->Common->delete(IMAGES, $this->params['image_id'])){
            @unlink(UPLOADS_REAL_PATH.$image);
            $this->result['status'] = TRUE;
            $this->result['message'] = "Image deleted successfully";
        }else{
            $this->result['message'] = "Oops! something went wrong";
        }
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * SERVICES LIST
     *------------------------------------------------
     *
     * @param category_id    : (int) | REQUIRED
     * @param rec_per_page  : (int) | NULL | <Record Per Page>
     * @param page_no       : (int) | NULL | <Page Number>
     *
     * @return json array()
     */
    public function service_list_post(){
        # Set validation rule
        $this->form_validation->set_rules('category_id', 'Category ID', 'trim|numeric');
        $this->form_validation->set_rules('rec_per_page', 'Record per page', 'numeric|trim');
        $this->form_validation->set_rules('page_no', 'Page no', 'numeric|trim');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Set condition for services
        $conditions = "Services.salon_id = '{$this->salonId}'";

        if(@$this->params['category_id'] != ""){
        	$conditions .= " AND (Services.service_cat_id = '{$this->params['category_id']}' OR Category.parent_id = '{$this->params['category_id']}') ";
        }
        
        # Fetch service list, 
        $this->result['data']['services'] = $this->Common->find([
            'table'     => SERVICES_TYPE." Services",
            'select' 	=> "Services.*, Category.parent_id AS parent_category",
            'where'     => $conditions,
            'join' 		=> [[CATEGORIES, "Category", "LEFT", "Services.service_cat_id = Category.id"]],
            'order' 	=> "Services.service_cat_id",
            'per_page'  => @$this->params['rec_per_page'],
            'page'      => @$this->params['page_no']
        ]);

        $this->result['status'] = TRUE;
        # Count total services depending on same conditions
        $this->result['data']['total'] = $this->Common->find([
            'table' => SERVICES_TYPE." Services",
            'where' => $conditions,
            'join' 	=> [[CATEGORIES, "Category", "LEFT", "Services.service_cat_id = Category.id"]],
            'query' => 'count'
        ]);
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * ADD / UPDATE SERVICES
     *------------------------------------------------
     *
     * @param service_id        : (int)                     | NULL
     * @param category_id       : (int)                     | REQUIRED
     * @param title             : (string)                  | REQUIRED
     * @param required_time     : (time)                    | REQUIRED
     * @param time_unit         : (enum[hr,min])            | REQUIRED
     * @param price             : (float)                   | REQUIRED
     * @param status            : (enum[active,inactive])   | REQUIRED
     *
     * @return json array()
     */
    public function service_add_edit_post(){
        # Set validation rule
        $this->form_validation->set_rules('service_id', 'Service ID', 'trim|numeric');
        $this->form_validation->set_rules('category_id', 'Category ID', 'trim|required|numeric');
        $this->form_validation->set_rules('title', 'Title', 'trim|required|min_length[3]|max_length[100]');
        $this->form_validation->set_rules('required_time', 'Required time', 'trim|required|numeric');
        $this->form_validation->set_rules('time_unit', 'Time unit', 'trim|required|enum[hr,min]');
        $this->form_validation->set_rules('price', 'Price', 'numeric|required|trim');
        $this->form_validation->set_rules('status', 'Price', 'trim|required|enum[active,inactive]');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $serviceArr = [
            'service_cat_id'    => @$this->params['category_id'],
            'salon_id'          => @$this->salonId,
            'type_name'         => @$this->params['title'],
            'time'              => @$this->params['required_time'],
            'time_unit'         => @$this->params['time_unit'],
            'rate'              => @$this->params['price'],
            'status'            => @$this->params['status']
        ];

        # If service_id is a valid service ID and service belongs to this salon
        # then service will update otherwise insert a new record

        if(@$this->params['service_id'] != ""){
            $extService = $this->Common->find([
                'table'     => SERVICES_TYPE,
                'where'     => "salon_id = '{$this->salonId}' AND 
                                id = '{$this->params['service_id']}'", 
                'query'     => 'first'
            ]);

            # Check if service found with this service ID
            if(empty($extService)){
                $this->result['message'] = "Invalid service ID entered";
                return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
            }

           $serviceArr['id'] = $this->params['service_id'];
        }

        if($service = $this->Common->save(SERVICES_TYPE, $serviceArr)){
            $this->result['status'] = TRUE;
            $this->result['message'] = "Service saved successfully";
        } else{
            $this->result['message'] = "Oops! something went wrong";
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }


    /**
     *------------------------------------------------
     * REMOVE SERVICES
     *------------------------------------------------
     *
     * @param service_id : (int) | REQUIRED
     *
     * @return json array()
     */
    public function remove_service_post(){
        # Set validation rule
        $this->form_validation->set_rules('service_id', 'Service ID', 'trim|required|numeric');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $extService = $this->Common->find([
            'table'     => SERVICES_TYPE,
            'where'     => "salon_id = '{$this->salonId}' AND 
                            id = '{$this->params['service_id']}'", 
            'query'     => 'first'
        ]);

        # Check if service found with this service ID
        if(empty($extService)){
            $this->result['message'] = "Invalid service ID entered";
            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);
        }

        if($service = $this->Common->delete(SERVICES_TYPE, $this->params['service_id'])){
            $this->result['status'] = TRUE;
            $this->result['message'] = "Service deleted successfully";
        } else{
            $this->result['message'] = "Oops! something went wrong";
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     *------------------------------------------------
     * SALON WORKING DAYS
     *------------------------------------------------
     *
     * @return json array()
     */
    public function working_days_post(){
        $this->result['status'] = TRUE;
        $days = $this->Common->find([
            'table'     => WEEKDAYS." Weekdays",
            'select'    => "Active.id AS id, Weekdays.id AS day_id, Weekdays.title AS day_name, Weekdays.short_name,
                            Active.from_time, Active.to_time,
                            Active.status AS active_status",
            'join'      => [[SALON_ACTIVE_DAYS, 'Active', 'LEFT', "Active.weekday_id = Weekdays.id AND Active.salone_id = '{$this->salonId}'"] ],
            'where'     => "Weekdays.status = 'active'",
            'order'     => "Weekdays.id"
        ]);

        $this->result['data']['days'] = [];
        if(!empty($days)){
            foreach($days AS $key => $eachDay){
                $eachDay['active_status'] = ($eachDay['active_status'] == 'active') ? TRUE : FALSE;
                $this->result['data']['days'][$key] = $eachDay;
            }
        }
        #IF(Active.status = 'active', 'true', 'false') AS active_status

        return $this->response($this->result, REST_Controller::HTTP_OK);
    } 

    /**
     *------------------------------------------------
     * SALON SAVE WORKING DAYS
     *------------------------------------------------
     * 
     * @param days (json array) | REQUIRED 
     * Example of one day
	 *    [
	 *	    {
	 *	        "id" 			: "8",
	 *	        "day_id" 		: "1",
	 *	        "day_name"  	: "Sunday",
	 *	        "short_name"	: "Sun",
	 *	        "from_time" 	: "06:00:00",
	 *	        "to_time" 		: "18:00:00",
	 *	        "active_status" : "active"
	 *	    }
     * 	]
     * @return json array()
     */
	public function save_week_days_post(){
        # Set validation rule
        $this->form_validation->set_rules('days', 'Weekdays', 'trim|required');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $weekdays = json_decode($this->params['days'], true);
        if(empty($weekdays)){
        	$this->result['message'] = "Invalid data entered";
        	return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }
        #return $this->response($weekdays, REST_Controller::HTTP_OK);
        foreach($weekdays AS $key => $eachDay){
            $weekday = [
                'id'            => @$eachDay['id'],
                'salone_id'     => @$this->salonId,
                'weekday_id'    => @$eachDay['day_id'],
                'from_time'     => (@$eachDay['from_time'] != "") ? $eachDay['from_time'] : "00:00:00",
                'to_time'       => (@$eachDay['to_time'] != "") ? $eachDay['to_time'] : "00:00:00",
                'status'        => (@$eachDay['active_status'] == TRUE) ? 'active' : 'inactive'
            ];
            #pre($weekday, 1);
            $this->Common->save(SALON_ACTIVE_DAYS, $weekday);
        }
        
        $days = $this->Common->find([
            'table'     => WEEKDAYS." Weekdays",
            'select'    => "Active.id AS id, Weekdays.id AS day_id, Weekdays.title AS day_name, Weekdays.short_name,
                            Active.from_time, Active.to_time,
                            Active.status AS active_status",
            'join'      => [[SALON_ACTIVE_DAYS, 'Active', 'LEFT', "Active.weekday_id = Weekdays.id AND Active.salone_id = '{$this->salonId}'"] ],
            'where'     => "Weekdays.status = 'active'",
            'order'     => "Weekdays.id"
        ]);

        $this->result['data']['days'] = [];
        if(!empty($days)){
            foreach($days AS $key => $eachDay){
                $eachDay['active_status'] = ($eachDay['active_status'] == 'active') ? TRUE : FALSE;
                $this->result['data']['days'][$key] = $eachDay;
            }
        }

        $this->result['status'] = TRUE;
		$this->result['message'] = "Week days saved successfully";
        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    
    /**
     *------------------------------------------------
     * UPLOAD GALLERY IMAGE (30-05-2020)
     *------------------------------------------------
     *
     * @param image_file : (file)       | REQUIRED
     * @param thumb_size : 200       	| OPTIONAL
     *
     * @return json array()
     */
    public function upload_gallery_image_post(){

        # Check if file exist
        if(empty($_FILES) || @$_FILES['image_file']['name'] == ""){
            $this->result['message'] = "Select an image file";
            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        # Configure image size, type
        $file_path = 'images/salon-gallery/';
        $base = get_dir($file_path);
        $imgconfig['upload_path']   = $base;
        $imgconfig['max_size']      = 1024 * 2;
        $imgconfig['allowed_types'] = 'png|jpg|jpeg';
        $imgconfig['encrypt_name']  = TRUE;
        $this->load->library('upload', $imgconfig);

        # Upload file
        if($this->upload->do_upload('image_file')) {
            $upload_data    = $this->upload->data();
            $imgname        = $upload_data['file_name'];

            $is_default = 1;

            # Process image DB data
            $imageArr = [
                'image_name'    => $imgname,
                'table_const'   => 'GALLERY',
                'table_id'      => @$this->salonId,
                'file_path'     => $file_path.$imgname,
                'is_default'    => $is_default
            ];
            # Save image details
            $image = $this->Common->save(IMAGES, $imageArr, true);

            # Dynamic thumb size
            $thumb_size = 100;
	        if(@$this->params['thumb_size'] && is_numeric($this->params['thumb_size'])){
	        	$thumb_size = $this->params['thumb_size'];
	        }

            $this->result['status']     	= TRUE;
            $this->result['message']    	= "Image uploaded successfully";
            $this->result['data']['image'] 	= [
            	'id' 				=> $image['id'],
            	'file_path_thumb' 	=> timthumb($image['file_path'], $thumb_size),
            	'file_path' 		=> UPLOAD_PATH.$image['file_path'],
            ];
        }else{
            $this->result['message']    = strip_tags(str_replace("\n", '', $this->upload->display_errors()));
        }

        return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     * ----------------------------------------------
     * IMAGE GALLERY (30-05-2020)
     * ----------------------------------------------
     * @param thumb_size 	OPTIONAL
     * @param page 			OPTIONAL
     * @param rec_per_page 	OPTIONAL
     */
    public function image_gallery_post(){
    	$this->form_validation->set_rules('page', 'Page No', 'trim|numeric');
    	$this->form_validation->set_rules('rec_per_page', 'Record Per Page', 'trim|numeric');
    	$this->form_validation->set_rules('thumb_size', 'Thumbnail Size', 'trim|numeric');

        # Check validation rule
        if($this->form_validation->run() == FALSE) {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

    	$thumb_size = 100;
    	$gallery = $this->Common->find([
            'table' 	=> IMAGES,
            'where' 	=> "table_id = '{$this->salonId}' AND table_const = 'GALLERY' AND status = 'active'",
            'page' 		=> @$this->params['page'],
            'per_page' 	=> @$this->params['rec_per_page'],
            'order' 	=> 'created DESC'
        ]);

    	# Dynamic thumb size
        if(@$this->params['thumb_size'] && is_numeric($this->params['thumb_size'])){
        	$thumb_size = $this->params['thumb_size'];
        }

    	$imageGallery = [];
        if(!empty($gallery)){
        	foreach($gallery AS $eachImage){
	        	$imageGallery[] = [
	        		'id' 				=> $eachImage['id'],
	        		'file_path_thumb' 	=> timthumb($eachImage['file_path'], $thumb_size),
	        		'file_path' 		=> UPLOAD_PATH.$eachImage['file_path']
	        	];
	        }
        }

    	$this->result['status'] 			= TRUE;
    	$this->result['data']['gallery'] 	= $imageGallery;

    	return $this->response($this->result, REST_Controller::HTTP_OK);
    }

    /**
     * ----------------------------------------------
     * IMAGE GALLERY
     * ----------------------------------------------
     * @param image_ids REQUIRED <1,2,3>
     * @param delete_all OPTIONAL <TRUE/FALSE>
     */
    public function remove_gallery_image_post(){
    	$condition = "";
    	if(@$this->params['delete_all'] !== TRUE){	
	        # Set validation rule
	        $this->form_validation->set_rules('image_ids', 'Image IDs', 'trim|required');

	        # Check validation rule
	        if($this->form_validation->run() == FALSE) {
	            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));
	            $this->result['errors'] = $this->form_validation->error_array();

	            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
	        }	        

	        $images_id_arr 	= explode(",", $this->params['image_ids']);
	        $image_ids 		= implode(",", array_filter($images_id_arr));

	        $condition = "id IN ({$image_ids})";
	    }

        if(@$this->params['delete_all'] === TRUE){
        	$condition = "table_const = 'GALLERY' AND table_id = '{$this->salonId}'";
        }
        
        if($condition != ""){
	        if($this->Common->deleteAll(IMAGES, $condition)){
		    	$this->result['status'] 	= TRUE;
		    	$this->result['message'] 	= "Image deleted successfully";
		    }
		}

    	return $this->response($this->result, REST_Controller::HTTP_OK);
    }
}


