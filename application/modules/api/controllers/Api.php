<?php



use Restserver\Libraries\REST_Controller;



defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

require APPPATH . 'libraries/Format.php';



class Api extends REST_Controller {



    private $result = [];

    private $userId = "";

    private $token  = "";

    private $user   = "";

    private $headers= [];

    private $params = [];

    private $store_id;

    private $user_table = USERS;

    private $store_table = STORE;

    private $ride_table = RIDE;

    private $booking_ride_table = BOOKING_RIDE;



    /**

     * @param Authorization : TOKEN | REQUIRED

     */

    function __construct() {

        parent::__construct();
        $this->load->library('Authorization_Token');
        $this->load->helper('api');

        # Default response

        $this->result = [

            'status'    => FALSE, 

            'message'   => "", 

            'data'      => []

        ];

        # Input headers

        $this->headers    = $this->input->request_headers();

        # Input params

        $this->params = $this->post();
        $this->per_page = 10;

        

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

                $this->result['msession_connect(host, port)age'] = "Invalid token entered";

                return $this->response($this->result, REST_Controller::HTTP_UNAUTHORIZED);

            }

            $this->store_id = $this->user['store_id'];


            $this->token = $this->headers['Authorization'];


        }

    }


    public function fetch_profile_post($value='')
    {
        # code...
        $post = (object) $this->input->post();
        $user_id = $post->user_id;
        $user_table = $this->user_table;
        $store_table = $this->store_table;
        $query = $this->db->query("SELECT {$user_table}.name,{$user_table}.email,{$store_table}.store_name FROM {$user_table} INNER JOIN {$store_table} ON {$user_table}.store_id={$store_table}.id WHERE {$user_table}.id='{$user_id}'");
        $userdata = $query->result_array();

        $this->result['status'] = TRUE;
        $this->result['message'] = "Profile fetch successfully.";

        $this->result['data']   = [

            'ridelist' => $userdata

        ];

        return $this->response($this->result, REST_Controller::HTTP_OK);
        
    }

    /**

     * -------------------------------------

     * RIDE LIST

     * -------------------------------------

     * @method POST

     * @return json array()

     */

    public function ride_list_get(){

        $this->result['status'] = TRUE;
        $ride_table = RIDE;
        $booking_ride_table = BOOKING_RIDE;
        $ridelist_data = array();

        //GET STORE ID
        $user_table = USERS;
        $store_query = $this->db->query("SELECT store_id FROM {$user_table} WHERE id='{$this->userId}'");
        $store_query_result = $store_query->result_array();
        $store_id = $store_query_result[0]['store_id'];
        $query = $this->db->query("SELECT * FROM {$ride_table} WHERE store_id='{$store_id}' AND status='A'");
        $ridelist = $query->result_array();

        foreach ($ridelist as $key => $value) {
            $ride_id = $value['id'];

            //CHECK BOOKING AVAILABILITY

            $booking_ride_query = $this->db->query("SELECT COUNT(*) AS count FROM {$booking_ride_table} WHERE (status='A' OR status='P') AND ride_id='{$ride_id}'");
            $booking_ride_list = $booking_ride_query->result_array();
            $count = $booking_ride_list[0]['count'];

            $ridelist_data[] = array('id'=>$ride_id,
                'ride_name'=>$value['ride_name'],
                'ride_desc'=>$value['ride_desc'],
                'ride_img'=>$value['ride_img'],
                'color_code'=>$value['color_code'],
                'status'=>$value['status'],
                'availibility'=>(($count == 0) ? 'yes' : 'no'),
            );
        }
        // exit;

        $this->result['status'] = TRUE;
        $this->result['message'] = "Ridelist fetch successfully.";

        $this->result['data']   = [

            'ridelist' => $ridelist_data

        ];

        return $this->response($this->result, REST_Controller::HTTP_OK);

    }


    public function createBooking_post($value='')
    {
        if(isset($_POST))
        {
            $rides = json_decode($_POST['ride']);
            //INSERT BOOKING DATA
            $booking_no = rand();
            $now = time();
            $booking_date = date('Y-m-d');
            $current_time = strtotime(date('m-d-Y H:i:s'));
            $store_id = $this->store_id;
            $data_booking = array(
                'customer_name'=>$_POST['name'],
                'customer_mobile'=>$_POST['mobile'],
                'store_id'=>$store_id,
                'user_id'=>$this->userId,
                'booking_no'=>$booking_no,
                'booking_start_time'=>$current_time,
                'booking_end_time'=>'',
                'total_ride_time'=>'',
                'booking_date'=>$booking_date,
                'no_of_ride'=>'',
                'ride_cost'=>'',
                'addition_cost'=>'',
                'total_cost'=>'',
                'created_by'=>$this->userId,
                'end_by'=>'','status'=>'P');

                $this->Common->save(BOOKING, $data_booking);
                $booking_id = $this->db->insert_id();

                 
            $total_ride_time = 0;
            $total_ride_price = 0;
            $no_of_ride = 0;
            $ride_data = array();
            foreach ($rides as $key => $value) {
                ++$no_of_ride;
                // $ride_time = $value->ride_time;
                // $ride_price = $value->ride_price;
                $ride_id = $value->ride_id;
                
                // $total_ride_time += $ride_time;
                // $total_ride_price += $ride_price;
                
                // $minutes = $now + ($ride_time * 60);
                
                // $ride_end = date('m-d-Y H:i:s', $minutes);
                // $ride_end = strtotime($ride_end);

                //INSERT BOOKING QUEUE TABLE

                // $data_booking_queue = array('store_id'=>1,'booking_id'=>$booking_id,'user_id'=>$this->userId,'ride_id'=>$ride_id,'ride_time'=>$ride_time,'ride_end'=>$ride_end);
                //  $this->Common->save(BOOKING_QUEUES, $data_booking_queue);

                // $ride_start_time = $current_time;
                // $ride_time = $ride_time;
                // $ride_time_to_end = $now + ($total_ride_time * 60);
                // $ride_end_time = date('m-d-Y H:i:s', $ride_time_to_end);
                // $ride_end_time = strtotime($ride_end_time);

                //INSERT BOOKING RIDE TABLE

                $data_booking_ride = array('booking_id'=>$booking_id,
                    'ride_id'=>$ride_id,
                    'store_id'=>$store_id,
                    'statr_time'=>'',
                    'end_time'=>'',
                    'total_ride'=>'',
                    'min_ride_time'=>'',
                    'min_ride_price'=>'',
                    'addition_ride_time'=>'',
                    'addition_ride_cost'=>'',
                    'total_ride_cost'=>'',
                    'status'=>'P');

                $this->Common->save(BOOKING_RIDE, $data_booking_ride);
                $booking_ride_id = $this->db->insert_id();

                $ride_table = RIDE;
                $ride_time_slot = RIDE_TIME_SLOT;
            	$query = $this->db->query("SELECT * FROM {$ride_table} WHERE id='{$ride_id}'");
            	$ride_result = $query->result_array();

                $ride_data_single = array(
                    'id'=>$booking_ride_id,
                    'ride_id'=>$ride_result[0]['id'],
                    'ride_name'=>$ride_result[0]['ride_name'],
                    'ride_desc'=>$ride_result[0]['ride_desc'],
                    'ride_img'=>$ride_result[0]['ride_img'],
                    'color_code'=>$ride_result[0]['color_code'],
                    'status'=>$ride_result[0]['status'],
                                       );

            	$ride_time_slot_query = $this->db->query("SELECT  `ride_base_time`, `ride_base_charge` FROM {$ride_time_slot} WHERE `status`='A' AND ride_id='{$ride_id}'");
            	$ride_time_slot_result = $ride_time_slot_query->result_array();


                $ride_data[] = array('ride_data'=>$ride_data_single,
                	'ride_time_slot'=>$ride_time_slot_result);
            }

            //UPDATE BOOKING TABLE
            //$total_ride_time_to_end = $now + ($total_ride_time * 60);
            //$booking_end_time = date('m-d-Y H:i:s', $total_ride_time_to_end);

            $data_booking_update = array(
                'id'=>$booking_id,
                'booking_end_time'=>'',
                'total_ride_time'=>'',
                'no_of_ride'=>$no_of_ride,
                'ride_cost'=>'',
                'addition_cost'=>'',
                'total_cost'=>'',
                'created_by'=>$this->userId,
            );

            $this->Common->save(BOOKING, $data_booking_update);

            $data_booking = $this->Common->find([

            'table'     => BOOKING, 

            'select'    => "*",

            'where'     => "id = '{$booking_id}'"

            ]);

            $data_booking_ride = $this->Common->find([

            'table'     => BOOKING_RIDE, 

            'select'    => "id",

            'where'     => "booking_id = '{$booking_id}'"

            ]);

            

            $this->result['status'] = TRUE;
            $this->result['message'] = 'Booking Successfully';

            $this->result['data']   = [
                'data_booking' => $data_booking,
                'data_booking_ride'=>$ride_data
            ];

            return $this->response($this->result,Rest_controller::HTTP_OK);
        }
        

    }



    public function cancel_ride_post($value='')
    {
        $this->form_validation->set_rules('id', 'Ride id', 'required|trim');

        //$this->form_validation->set_rules('booking_id', 'Booking_id', 'required|trim');

        # Check validation rule

        if ($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $post = (object) $this->input->post();
        $ride_id = $post->id;
        // $booking_id = $post->booking_id;

        if($this->Common->save(BOOKING_RIDE,array('status'=>'I'),array('id'=>$ride_id)))
        {
            $this->result['status'] = TRUE;
            $this->result['message'] = 'Ride Cancel Successfully';
            return $this->response($this->result,Rest_controller::HTTP_OK);
        }
        else
        {
            $this->result['status'] = FALSE;
            $this->result['message'] = 'Something Error';
            return $this->response($this->result,Rest_controller::HTTP_BAD_REQUEST);
        }
    }


    public function ride_start_post($value='')
    {
        # code...
        $this->form_validation->set_rules('ride_id', 'Ride id', 'required|trim');
        // $this->form_validation->set_rules('booking_id', 'Booking id', 'required|trim');
        $this->form_validation->set_rules('ride_base_time', 'ride base time', 'required|trim');
        $this->form_validation->set_rules('ride_base_charge', 'ride base charge', 'required|trim');

        # Check validation rule

        if ($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $post = (object) $this->input->post();
        $ride_id = $post->ride_id;
        $ride_base_time = $post->ride_base_time;
        $ride_base_charge = $post->ride_base_charge;

        $ride_data = $this->Common->find([
            'table'     => BOOKING_RIDE, 
            'select'    => "booking_id,ride_id",
            'where'     => "id = '{$ride_id}'"
            ]);
        $booking_id = $ride_data[0]['booking_id'];
        $ride_details_id = $ride_data[0]['ride_id'];

        $ride_details = $this->Common->find([
            'table'     => RIDE, 
            'select'    => "id,ride_name,ride_img,color_code",
            'where'     => "id = '{$ride_details_id}'"
            ]);

        $ride_details = array(
            'id'=>$ride_id,
            'ride_id'=>$ride_details[0]['id'],
            'ride_name'=>$ride_details[0]['ride_name'],
            'ride_img'=>$ride_details[0]['ride_img'],
            'color_code'=>$ride_details[0]['color_code'],);
        
        $start_time = time();
        $end_time = $start_time + ($ride_base_time * 60);


        
        if($this->Common->save(BOOKING_RIDE,array('statr_time'=>$start_time,'end_time'=>$end_time,'total_ride'=>$ride_base_time,'min_ride_time'=>$ride_base_time,'min_ride_price'=>$ride_base_charge,'total_ride_cost'=>$ride_base_charge,'status'=>'A'),array('id'=>$ride_id)))
        {
            $this->Common->save(BOOKING,array('status'=>'A'),array('id'=>$booking_id));

            $this->result['status'] = TRUE;
            $this->result['message'] = 'Ride Start Succesfully.';
            $this->result['data'] = $ride_details;
            
            return $this->response($this->result,Rest_controller::HTTP_OK);
        }
        else
        {
            $this->result['status'] = FALSE;
            $this->result['message'] = 'Something Error.';
            return $this->response($this->result,Rest_controller::HTTP_BAD_REQUEST);
        }

    }

    public function ride_end_post($value='')
    {
        # code...
        $this->form_validation->set_rules('ride_id', 'Ride_id', 'required|trim');

        //$this->form_validation->set_rules('booking_id', 'Booking_id', 'required|trim');

        # Check validation rule

        if ($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }

        $post = (object) $this->input->post();
        $ride_id = $post->ride_id;
        $ride_data = $this->Common->find([
            'table'     => BOOKING_RIDE, 
            'select'    => "booking_id,ride_id,statr_time,end_time,min_ride_time,min_ride_price,",
            'where'     => "id = '{$ride_id}'"
            ]);
        $booking_id = $ride_data[0]['booking_id'];
        $ride_details_id = $ride_data[0]['ride_id'];

        $ride_details = $this->Common->find([
            'table'     => RIDE, 
            'select'    => "id,ride_name,ride_img,color_code,extra_per_min_cost",
            'where'     => "id = '{$ride_details_id}'"
            ]);

        $actual_end_time = time();
        $start_time = $ride_data[0]['statr_time'];
        //$end_time = $ride_data[0]['end_time'];
        //$extra_per_min_cost = $ride_details[0]['extra_per_min_cost'];
        //$extra_time = round(($actual_end_time - $end_time)/60);
        
        $total_ride_time = round(($actual_end_time - $start_time)/60);
 
        if($total_ride_time > $ride_data[0]['min_ride_time'])
        {
        	$extra_time = $total_ride_time - $ride_data[0]['min_ride_time'];
        	$total_ride_price = $ride_data[0]['min_ride_price'] + ($ride_details[0]['extra_per_min_cost']*$extra_time);
        	$extra_price = $extra_time*$ride_details[0]['extra_per_min_cost'];
        }
        else
        {
        	$extra_time = 0;
        	$total_ride_price = $ride_data[0]['min_ride_price'];
        	$extra_price = 0;
        }
        
       

        if($this->Common->save(BOOKING_RIDE,array('status'=>'C','end_time'=>$actual_end_time,'addition_ride_time'=>$extra_time,'addition_ride_cost'=>$extra_price,'total_ride'=>$total_ride_time,'total_ride_cost'=>$total_ride_price),array('id'=>$ride_id)))
        {
                //CHECK ALL RIDE STATUS 
                //IF ALL RIDE COMPLETED THEN BOOKING ALSO COMPLETED.
                $allRideStatus   = $this->Common->find([
                'table' => BOOKING_RIDE,
                'select'=>'status',
                'where' => "booking_id = '{$booking_id}'",
                ]);
                $status = TRUE;
                foreach ($allRideStatus as $key => $value) 
                {
                   if($value['status'] != 'C')
                   {
                    $status = FALSE;
                   }
                }
                if($status === TRUE)
                {
                    $this->Common->save(BOOKING,array('status'=>'C'),array('id'=>$booking_id));
                }

            $ride_details = array(
            'id'=>$ride_id,
            'ride_id'=>$ride_details[0]['id'],
            'ride_name'=>$ride_details[0]['ride_name'],
            'ride_img'=>$ride_details[0]['ride_img'],
            'color_code'=>$ride_details[0]['color_code'],
            'start_time'=>$start_time,
            'end_time'=>$actual_end_time,
            'total_ride'=>$total_ride_time,
            'min_ride_time'=>$ride_data[0]['min_ride_time'],
            'min_ride_price'=>$ride_data[0]['min_ride_price'],
            'additional_time'=>$extra_time,
            'total_amount'=>$total_ride_price);

            $this->result['status'] = TRUE;
            $this->result['message'] = 'Ride Completed Successfully.';
            $this->result['data'] = $ride_details;

            return $this->response($this->result,Rest_controller::HTTP_OK);
        }
        else
        {
            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }
        
    }


    public function upload_image_post($value='')
    {
        # Set validation rule
        //$this->form_validation->set_rules('image_file', 'Image file', 'required|trim');
        $this->form_validation->set_rules('type', 'Type', 'required|trim');
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
        $post = (object) $this->input->post();
        $type = $post->type;

        if($type == 'user')
        {
            $file_path = 'users/';
            $table = USERS;
        }
        elseif($type == 'ride')
        {
            $file_path = 'users/';
            $table = RIDE;
        }

        $base = get_dir($file_path);

        $imgconfig['upload_path']   = $base;

        $imgconfig['max_size']      = 1024 * 2;

        $imgconfig['allowed_types'] = 'png|jpg|jpeg';

        $imgconfig['encrypt_name']  = TRUE;

        $this->load->library('upload', $imgconfig);

        if($this->upload->do_upload('image_file')) {

            $upload_data    = $this->upload->data();

            $imgname        = $upload_data['file_name'];
            $this->Common->save($table,array('image'=>$imgname),array('id'=>$this->userId));

            $image_data = array('image_name'=>$imgname);

            $this->result['status'] = TRUE;
            $this->result['message'] = 'Image uploaded successfully.';
            $this->result['data'] = $image_data;
            return $this->response($this->result,Rest_controller::HTTP_OK);

        }
        else
        {
            $this->result['status'] = FALSE;
            $this->result['message'] = 'Something error,try again.';
            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
        }
    }
    // public function ride_list_post($value='')
    // {
    //     //GET RIDE LIST STATUS WISE 
    //     $store_id
    //     $rideList   = $this->Common->find([
    //             'table' => BOOKING_RIDE,
    //             'select'=>'*',
    //             'where' => "status = 'P'",
    //         ]);

    //     $this->result['status'] = TRUE;
    //     $this->result['message'] = 'Ride List Fetched Succesfully.';
    //     $this->result['data']['ridelist'] = $rideList;
    //     return $this->response($this->result,Rest_controller::HTTP_OK);
        
    // }

    public function ride_list_by_user_post($value='')
    {
        //GET RIDE LIST STATUS WISE 
        // $rideList   = $this->Common->find([
        //         'table' => BOOKING_RIDE,
        //         'select'=>'*',
        //         'where' => "status = 'P' AND ",
        //     ]);

        $this->result['status'] = TRUE;
        $this->result['message'] = 'Ride List Fetched Succesfully.';
        $this->result['data']['ridelist'] = $rideList;
        return $this->response($this->result,Rest_controller::HTTP_OK);
        
    }

    // public function ride_history_list_post($value='')
    // {
    //     $post = (object) $this->input->post();
    //     $keyword = $post->keyword;

    //     //GET RIDE LIST STATUS WISE 
    //     $result = $this->Main->ride_history();

    //     $this->result['status'] = TRUE;
    //     $this->result['message'] = 'Ridelist List Fetched Succesfully.';
    //     $this->result['data']['ridelist'] = $result;
    //     return $this->response($this->result,Rest_controller::HTTP_OK);
    // }


    // public function booking_list_post($value='')
    // {
    //     # code...
    //     $this->form_validation->set_rules('keyword', 'Keyword', 'required|trim');

    //     # Check validation rule

    //     if ($this->form_validation->run() == FALSE) {

    //         $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

    //         $this->result['errors'] = $this->form_validation->error_array();

    //         return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
    //     }

    //     $post = (object) $this->input->post();
    //     $keyword = $post->keyword;
    //     //GET RIDE LIST STATUS WISE 
    //     $result   = $this->Common->find([
    //             'table' => BOOKING,
    //             'select'=>'*',
    //             'where' => "customer_mobile = '{$keyword}' OR customer_name='{$keyword}'",
    //             'order'     => "created DESC",
    //         ]);

    //     $this->result['status'] = TRUE;
    //     $this->result['message'] = 'Ridelist List Fetched Succesfully.';
    //     $this->result['data']['ridelist'] = $result;
    //     return $this->response($this->result,Rest_controller::HTTP_OK);
    // }

    public function booking_list_post($value='')
    {
        $post = (object) $this->input->post();
        //$per_page = $post->per_page;
        $page_no = @$post->page_no;
        //GET RIDE LIST STATUS WISE 

        $store_id = $this->store_id;
       
        $result  = $this->Common->find([
                'table' => BOOKING,
                'select'=>'*',
                'where'=>"store_id='{$store_id}' AND status!='D' AND status!='P'",
                'order'=> "created DESC",
                'per_page'  => @$this->per_page,
                'page'      => @$page_no
            ]);
        $today_total_cost = 0;
        $booking_details = array();
        foreach ($result as $key => $value) {
            $booking_id = $value['id'];
            $ride  = $this->Common->find([
                'table' => BOOKING_RIDE,
                'select'=>'total_ride,total_ride_cost,statr_time,end_time',
                'where'=>"booking_id='{$booking_id}'",
            ]);
            $total_ride_time = 0;
            $total_ride_cost = 0;
            $total_ride = 0;
          foreach ($ride as $key => $rides) {
                # code...
                $total_ride_time += $rides['end_time'] - $rides['statr_time'];
                $total_ride_cost += $rides['total_ride_cost'];
                $total_ride  = $total_ride + 1;
            }  
            $today_total_cost += $total_ride_cost;
            $booking_details[] = array(
                'id'=>$booking_id,
                'customer_name'=>$value['customer_name'],
                'customer_mobile'=>$value['customer_mobile'],
                'booking_no'=>$value['booking_no'],
                'total_ride_time'=>gmdate("H:i:s", $total_ride_time),
                'total_ride_cost'=>$total_ride_cost,
                'total_ride'=>$total_ride,
                'status'=>$value['status'],
                'booking_date'=>$value['booking_date']
            );
        }
        $today_total_cost = $today_total_cost;
        //exit;

        $this->result['status'] = TRUE;
        $this->result['message'] = 'Booking List Fetched Succesfully.';
        $this->result['data']['ridelist'] = $booking_details;
        $this->result['data']['today_cost'] = $today_total_cost;
        return $this->response($this->result,Rest_controller::HTTP_OK);
    }

    public function booking_list_by_status_post($value='')
    {
            $this->form_validation->set_rules('status', 'Status', 'required|trim');

            # Check validation rule

            if ($this->form_validation->run() == FALSE) {

                $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

                $this->result['errors'] = $this->form_validation->error_array();

                return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
            }

            $post = (object) $this->input->post();
            $status = $post->status;
            $store_id = $this->store_id;
        //GET RIDE LIST STATUS WISE 
        $result   = $this->Common->find([
                'table' => BOOKING,
                'where' => "store_id='{$store_id}' AND status = '{$status}'",
                'select'=>'*',
                'order'     => "created DESC",
            ]);

        $this->result['status'] = TRUE;
        $this->result['message'] = 'Ridelist List Fetched Succesfully.';
        $this->result['data']['ridelist'] = $result;
        return $this->response($this->result,Rest_controller::HTTP_OK);
    }

    public function ride_list_by_status_post($value='')
    {
            $this->form_validation->set_rules('status', 'Status', 'required|trim');

            # Check validation rule

            if ($this->form_validation->run() == FALSE) {

                $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

                $this->result['errors'] = $this->form_validation->error_array();

                return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
            }

            $post = (object) $this->input->post();
            $status = $post->status;
            $store_id = $this->store_id;
            $ride_table = $this->ride_table;
            $booking_ride_table = $this->booking_ride_table;

        //GET RIDE LIST STATUS WISE 
        // $result   = $this->Common->find([
        //         'table' => BOOKING,
        //         'where' => "store_id='{$store_id}' AND status = '{$status}'",
        //         'select'=>'*',
        //         'order'     => "created DESC",
        //     ]);

            $query = $this->db->query("SELECT {$booking_ride_table}.*,{$ride_table}.ride_name,{$ride_table}.ride_img,{$ride_table}.color_code FROM {$booking_ride_table} INNER JOIN {$ride_table} ON {$booking_ride_table}.ride_id={$ride_table}.id WHERE {$booking_ride_table}.status='{$status}' AND {$booking_ride_table}.store_id='{$store_id}'");
            $result = $query->result_array();

        $this->result['status'] = TRUE;
        $this->result['message'] = 'Ridelist List Fetched Succesfully.';
        $this->result['data'] = $result;
        return $this->response($this->result,Rest_controller::HTTP_OK);
    }

    public function ride_list_by_booking_post($value='')
    {
            $this->form_validation->set_rules('booking_id', 'Status', 'required|trim');

            # Check validation rule

            if ($this->form_validation->run() == FALSE) {

                $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

                $this->result['errors'] = $this->form_validation->error_array();

                return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
            }

            $post = (object) $this->input->post();
            $booking_id = $post->booking_id;
            $store_id = $this->store_id;
            $ride_table = $this->ride_table;
            $booking_ride_table = $this->booking_ride_table;

        //GET RIDE LIST STATUS WISE 
        $query = $this->db->query("SELECT {$booking_ride_table}.*,{$ride_table}.ride_name FROM {$booking_ride_table} INNER JOIN {$ride_table} ON {$booking_ride_table}.ride_id={$ride_table}.id WHERE {$booking_ride_table}.booking_id='{$booking_id}' AND {$booking_ride_table}.store_id='{$store_id}'");
            $result = $query->result_array();

        //$query = $this->db->query("SELECT ")

        $this->result['status'] = TRUE;
        $this->result['message'] = 'Ridelist List Fetched Succesfully.';
        $this->result['data']['ridelist'] = $result;
        return $this->response($this->result,Rest_controller::HTTP_OK);
    }


    public function changepassword_post($value='')
    {
        $this->form_validation->set_rules('old_password', 'Old password', 'required|trim');
        $this->form_validation->set_rules('new_password', 'New password', 'required|trim');

            # Check validation rule

            if ($this->form_validation->run() == FALSE) {

                $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

                $this->result['errors'] = $this->form_validation->error_array();

                return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);
            }

            $post = (object) $this->input->post();
            $new_password = $post->new_password;
            $old_password = $post->old_password;

            $userDtls   = $this->Common->find([

                'table' => USERS,
                'select' =>'password',

                'where' => "id = '{$this->userId}'",

                'query' => "first"

            ]);

            

            $password = $userDtls['password'];

            if(!empty($userDtls) && passwordmatch($old_password, $password)) {
                $this->Common->save(USERS,array('password'=>createpassword($new_password)),array('id'=>$this->userId));

            $this->result['status'] = TRUE;
            $this->result['message'] = 'Password changed successfully.';
            //$this->result['data']['ridelist'] = $result;
            return $this->response($this->result,Rest_controller::HTTP_OK);


            }
            else
            {
                $this->result['status'] = FALSE;
                $this->result['message'] = 'Old Password does not matched.';
                //$this->result['data']['ridelist'] = $result;
                return $this->response($this->result,Rest_controller::HTTP_BAD_REQUEST);
            }
    }

}





