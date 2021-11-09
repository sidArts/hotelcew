<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') or exit('No direct script access allowed');



require APPPATH . 'libraries/REST_Controller.php';

require APPPATH . 'libraries/Format.php';



class Auth extends REST_Controller {

    private $result     = [];

    private $headers    = [];

    private $params     = [];

    private $user_table = USERS;

    private $store_table = STORE;

    public function __construct() {

        parent::__construct();

        // $this->headers  = $this->input->request_headers();

        // $this->params   = $this->post();

        $this->load->library('Authorization_Token');

        $this->load->helper('api');

        $this->result = ['status' => FALSE, 'message' => "", 'data' => []];

    }





    /**

     *------------------------------------------------

     * CUSTOMER LOGIN 

     *------------------------------------------------

     *

     * Only user type customer and active user can login.

     * @param email     : (string) | REQUIRED | VALID_EMAIL

     * @param password  : (string) | REQUIRED

     * @param fcmToken  : (string) | NULL

     *

     * @return json array()

     */

    // public function test_get(){
    //     echo 1;

    //     die;
    //     $this->result['status']     = TRUE;

    //     $this->result['message']    = "This is test function";

    //     return $this->response($this->result, REST_Controller::HTTP_OK);

    // }

    public function login_post() {

        $otpMsg = "";
        $user_table = $this->user_table;
        $store_table = $this->store_table;
        # Set validation rule
        $this->form_validation->set_rules('opcode', 'Opcode', 'required|trim');

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        $this->form_validation->set_rules('password', 'Password', 'required|trim');



        # Check validation rule

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        } else {

            # Fetch inputs

            $inputs     = $this->input->post();



            $userDtls   = $this->Common->find([

                'table' => USERS,

                'where' => "email = '{$inputs['email']}' AND opcode = '{$inputs['opcode']}' AND role = 3",

                'query' => "first"

            ]);

            if(empty($userDtls))
            {
                $this->result['message'] = "Wrong Opcode or Email address entered";

                return $this->set_response($this->result, REST_Controller::HTTP_UNAUTHORIZED);
            }

            
            if(!empty($userDtls) && passwordmatch($inputs['password'], $userDtls['password'])) {


                # Check user active status

                if ($userDtls['status'] == 'D') {

                    $this->result['message'] = "Your account has been De-activated";

                    return $this->set_response($this->result, REST_Controller::HTTP_OK);

                }



                // # Check if varified email address

                if ($userDtls['email_verified'] == 'I') {

                    //verify_email($userDtls['id']);

                    $this->result['message'] = "Please verify your email address";

                    return $this->set_response($this->result, REST_Controller::HTTP_OK);

                }



                # Check if varified email address

     //            if ($userDtls['mobile_verified'] == 'false') {

     //                $otp = rand(1111, 9999);

     //                $this->Common->save(OTPS, [

     //                    'user_id'   => @$userDtls['id'], 

     //                    'mobile'    => @$userDtls['mobile'],

     //                    'otp'       => $otp,

     //                    'device_id' => @$data['fcmToken']

     //                ]);

     //                $otpMsg = "OTP has been sent to your mobile number";



     //                #$email_message = $this->newotpemail_temp(@$userDtls['id'], $otp);

     //                #$emailSent = email(DOMAIN_MAIL, @$userDtls['email'], "Blothru OTP ", $email_message);





	    //             $dial_code = (@$userDtls['dial_code'] != "") ? @$userDtls['dial_code'] : DEFAULT_DIAL_CODE;

					// $this->load->library('twilio');

					// $this->twilio->send($dial_code.@$userDtls['mobile'], otp_message($otp));

     //            }

                $token = $this->authorization_token->generateToken([

	                'id'            => $userDtls['id'],

	                'email'         => $userDtls['email'],

	                'time'          => time()

	            ]);


                unset($userDtls['password']);


                
                $query = $this->db->query("SELECT {$user_table}.*,{$store_table}.store_name,{$store_table}.opcode AS store_opcode FROM {$user_table} INNER JOIN {$store_table} ON {$user_table}.store_id={$store_table}.id WHERE {$user_table}.id='{$userDtls['id']}'");
                $userdata = $query->result_array();


                $this->result = [

                    'status'    => TRUE,

                    'data'      =>  $userdata,
                    'token'     => $token,

                    'message'   => "Login Successfully"

                ];



                # storing login details

                $this->Common->save(DEVICE_TOKEN, [

                    'user_id'       => $userDtls['id'],

                    'ip_address'    => $_SERVER['REMOTE_ADDR'],

                    // 'user_agent'    => @$this->headers['User-Agent'],

                    // 'fcmToken'      => (@$inputs['fcmToken']) ? $inputs['fcmToken'] : @$this->headers['Postman-Token'],

                    'token_id'   => $token,

                    'status'        => 'active'

                ]);



                // if($otpMsg){

                //     $this->result['status'] = TRUE;

                //     $this->result['message'] = $otpMsg;

                // }

                return $this->response($this->result, REST_Controller::HTTP_OK);

            } else {



                $this->result['message'] = "Wrong email address or password entered";

                return $this->set_response($this->result, REST_Controller::HTTP_UNAUTHORIZED);

            }

        }

    }



    /**

     *------------------------------------------------

     * SALON LOGIN 

     *------------------------------------------------

     *

     * Only user type client and active user can login.

     * @param email         : (string) | REQUIRED | VALID_EMAIL

     * @param password      : (string) | REQUIRED

     * @param fcmToken      : (string) | NULL

     * @return json array()

     */

    public function salon_login_post() {

        #return $this->response($this->input->request_headers());

        # Set validation rule

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        $this->form_validation->set_rules('password', 'Password', 'required|trim');



        # Check validation rule

        if ($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();

            

            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        # Fetch inputs

        $inputs     = $this->input->post();



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

                            Profile.street_address,

                            Profile.country,

                            Salon.id AS salon_id,

                            Salon.name AS salon_name, 

                            CONCAT('" . USER_IMGPATH . "',User.profile_img ) profile_image",

            'join'      => [

                                [USER_PROFILE, 'Profile', 'INNER', "User.id = Profile.user_id"],

                                [SALONS, 'Salon', 'INNER', "User.id = Salon.user_id"],

                            ],

            'where'     => "User.email = '{$inputs['email']}' AND user_type = 'client'",

            'query'     => 'first'

        ]);



        # Check if password is matched

        if(passwordmatch($inputs['password'], @$userDtls['password'])) {

            

            # Check profile is active or not

            if(@$userDtls['status'] != 'active') {

                $this->result['message'] = "Your account has been blocked";

                return $this->set_response($this->result, REST_Controller::HTTP_NOT_ACCEPTABLE);

            }



            # Check if varified email address

            if(@$userDtls['email_verified'] == 'false') {

                verify_email($userDtls['id']);                

                $this->result['message'] = "Your email address is not verified";

                return $this->set_response($this->result, REST_Controller::HTTP_NOT_ACCEPTABLE);

            }



            $address = [

                @$userDtls['street_address'], $userDtls['city'], $userDtls['state'], $userDtls['country'], $userDtls['zipCode']

            ];



            # Process response data

            $this->result['data']['profile'] = [

                'id'                => $userDtls['id'],

                'dial_code'         => $userDtls['dial_code'],

                'mobile'            => $userDtls['mobile'],

                'mobileNumber'      => $userDtls['mobile'],

                'mobile_verified'   => $userDtls['mobile_verified'],

                'email'             => $userDtls['email'],

                'email_verified'    => $userDtls['email_verified'],

                'profile_id'        => $userDtls['profile_id'],

                'first_name'        => $userDtls['first_name'],

                'firstName'         => $userDtls['first_name'], 

                'last_name'         => $userDtls['last_name'],

                'lastName'          => $userDtls['last_name'],

                'full_name'         => $userDtls['first_name']." ".$userDtls['last_name'],

                'gender'            => $userDtls['gender'],

                'birthDate'         => $userDtls['birth_date'],

                'address'           => implode(', ', array_filter($address)),

                'street_address'    => $userDtls['street_address'],

                'city'              => $userDtls['city'],

                'state'             => $userDtls['state'],

                'country'           => $userDtls['country'],

                'zipCode'           => $userDtls['zipCode'],

                'salon_id'        	=> $userDtls['salon_id'],

                'salon_name'        => $userDtls['salon_name'],

                'profileImage'      => timthumb($userDtls['profile_img'], 150, 150)

            ];

            

            #'profile_image'     => timthumb($userDtls['profile_img'], 150, 150)



            $this->result['status']     = TRUE;

            $this->result['currency']   = CURRENCY;

            

            $this->result['data']['token'] = $this->authorization_token->generateToken([

                'id'            => $userDtls['id'],

                'email'         => $userDtls['email'],

                'profile_id'    => $userDtls['profile_id'],

                'mobile'        => $userDtls['mobile'],

                'time'          => time()

            ]);



            # storing login details

            $this->Common->save(USER_LOGINS, [

                'user_id'       => $userDtls['id'],

                'ip_address'    => $_SERVER['REMOTE_ADDR'],

                'user_agent'    => @$this->headers['User-Agent'],

                'fcmToken'      => (@$inputs['fcmToken']) ? $inputs['fcmToken'] : @$this->headers['Postman-Token'],

                'login_token'   => $this->result['data']['token'],

                'status'        => 'active'

            ]);

            return $this->set_response($this->result, REST_Controller::HTTP_OK);

        }else{

            # If user enter a wrong email or password

            $this->result['message'] = "Wrong email address or password";

            return $this->set_response($this->result, REST_Controller::HTTP_UNAUTHORIZED);

        }

    }



    /**

     *------------------------------------------------

     * USER REGISTRATION 

     *------------------------------------------------

     *

     * Customer and Provider registration

     * @param firstName     : (string) | REQUIRED | MIN 3 | MAX 64, 

     * @param lastName      : (string) | NULL | MIN 3 | MAX 64, 

     * @param email         : (string) | REQUIRED | VALID_EMAIL, 

     * @param mobile        : (numeric)| REQUIRED | MIN 10 | MAX 10, 

     * @param password      : (string) | REQUIRED | MIN 6 | MAX 30, 

     * @param cpassword     : (string) | REQUIRED | MIN 6 | MAX 30 | <EQUAL TO password>,  

     * @param device_id     : (string) | NULL, 

     * @param referal       : (string) | NULL, 

     * @param dial_code     : (string) | NULL 

     *

     * @return json array()

     */

    public function user_signup_post($userType = NULL) {

        $data = $this->post();



        # Set validation rule

        $this->form_validation->set_rules('firstName', 'First Name ', 'trim|required|min_length[3]|max_length[64]');

        $this->form_validation->set_rules('lastName', 'Last Name  ', 'trim|min_length[3]|max_length[64]');

        $this->form_validation->set_rules('email', 'Email ', 'trim|required|valid_email');

        $this->form_validation->set_rules('mobile', 'Mobile No ', 'trim|required|numeric|min_length[10]|max_length[15]');

        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[30]|matches[cpassword]');

        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required');

        

        if($userType == 'client'){

            $this->form_validation->set_rules('salon_title', 'Salon name', 'trim|required|min_length[3]|max_length[64]');

            $this->form_validation->set_rules('home_service', 'Home service', 'trim|enum[true,false]');

            $this->form_validation->set_rules('on_visit', 'On visit', 'trim|enum[true,false]');

            $this->form_validation->set_rules('home_service', 'Home Service', 'trim|enum[true,false]');

        }



        # Check validation rule

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        #check email id already registered or not

        $countEmail = $this->Common->find([

            'table' => USERS,

            'where' => "email = '{$data['email']}'",

            'query' => 'count'

        ]);

        if($countEmail > 0){

            $this->result['message'] = "Email address already exist";

            return $this->set_response($this->result, REST_Controller::HTTP_NOT_ACCEPTABLE);

        }



        #check Phone No already registered or not

        $countEmail = $this->Common->find([

            'table' => USERS,

            'where' => "mobile = '{$data['mobile']}'",

            'query' => 'count'

        ]);

        if($countEmail > 0){

            $this->result['message'] = "Phone number already exist";

            return $this->set_response($this->result, REST_Controller::HTTP_NOT_ACCEPTABLE);

        }



        # Process user data

        $profileid = substr(time(), 4, 9);

        $mem_data = [

            'user_type'     => ($userType != "") ? $userType : 'customer',

            'profile_id'    => 'BT-' . $profileid,

            'email'         => @$data['email'],

            'password'      => createpassword($data['password']),

            'dial_code'     => @$data['dial_code'],

            'mobile'        => @$data['mobile'],

            'profile_img'   => ''

        ];



        # Save user data

        $userid = $this->Common->save(USERS, $mem_data);

        if($userid != ""){

            # Process profile data

            $profile_data = [

                'fname'     => $data['firstName'],

                'lname'     => @$data['lastName'],

                'user_id'   => $userid

            ];



            # Save profile data to existing user id

            $userPid = $this->Common->save(USER_PROFILE, $profile_data);

            if($userPid != ""){

                # Generate OTP and store in DB, device_id is optional

                $otp = substr($profileid,2,4);

                $this->Common->save(OTPS, [

                    'user_id'   => $userid, 

                    'mobile'    => $data['mobile'],

                    'otp'       => $otp,

                    'device_id' => @$data['fcmToken']

                ]);



                $dial_code = (@$data['dial_code'] != "") ? @$data['dial_code'] : DEFAULT_DIAL_CODE;

				$this->load->library('twilio');

                $otpMsg = otp_message($otp);

                /*$this->result['twilio']['request'] = [

                    'dial_code' => $dial_code,

                    'mobile' => @$data['mobile'],

                    'message' => $otpMsg

                ];*/

				$this->twilio->send($dial_code.@$data['mobile'], $otpMsg);



                if($userType == 'client'){

                    $this->Common->save(SALONS, [

                        'user_id'       => $userid, 

                        'name'          => addslashes($data['salon_title']),

                        'store_code'    => slug(SALONS, 'store_code', $data['salon_title']),

                        'email'         => @$data['email'],

                        'contact_no'    => @$data['mobile'],

                        'dial_code'     => @$data['dial_code'],

                        'home_service'  => (@$data['home_service'] == 'true') ? 'true' : 'false',

                        'on_visit'      => (@$data['on_visit'] == 'true') ? 'true' : 'false',

                        'area'          => @$data['area'],

                        'status'        => 'pending'

                    ]);

                }



                # Verify email address

                verify_email($userid);



                # Fetching user details

                $userContent    = $this->Common->find([

                    'table'     => USERS." User",

                    'select'    => "User.id AS userid, User.profile_id,User.email email,

                                    User.mobile, User.profile_img, 

                                    User.email_verified,User.mobile_verified,User.status,  

                                    Profile.fname AS firstName, 

                                    Profile.lname AS lastName, 

                                    Profile.gender, 

                                    Profile.dob AS birthDate, 

                                    Profile.zip AS zipCode,

                                    Profile.state, 

                                    Profile.street_address, 

                                    Profile.city, 

                                    Profile.country",

                    'join'      => [[USER_PROFILE, 'Profile', 'INNER', "User.id = Profile.user_id"]],

                    'where'     => "User.id = '{$userid}'",

                    'query'     => 'first'

                ]);



                $userContent['profileImage'] = timthumb($userContent['profile_img'], 150, 150);

                $this->result['status']             = TRUE;

                $this->result['data']['userdata']   = $userContent;

                $this->result['message']            = "Registration successful";



                return $this->set_response($this->result, REST_Controller::HTTP_OK);

            } else{

                $this->result['message'] = "Oops! Something went wrong";

                return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

            }

        } else{

            $this->result['message'] = "Oops! Something went wrong";

            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }

    }





    /**

     *------------------------------------------------

     * CHECK EMAIL IF ALREADY EXIST 

     *------------------------------------------------

     *

     * @param email : (string) | REQUIRED

     * @return json array()

     */

    public function checkemail_post() {

        $inputs = $this->post();

        unset($this->result['data']);

        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');

        # Check validation rule

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        $totRecord = $this->Common->find([

            'table' => USERS,

            'where' => "email = '{$inputs['email']}'",

            'query' => 'count'

        ]);



        $this->result['status'] = TRUE;

        if($totRecord > 0){

            $this->result['emailExist'] = TRUE;

            $this->result['message'] = "Email already exist";

        }else{

            $this->result['emailExist'] = FALSE;

        }



        return $this->set_response($this->result, REST_Controller::HTTP_OK);

    }





    /**

     *------------------------------------------------

     * CHECK PHONE NUMBER IF ALREADY EXIST 

     *------------------------------------------------

     *

     * @param mobile : (string) | REQUIRED

     * @return json array()

     */

    public function checkmobile_post() {

        $inputs = $this->post();

        unset($this->result['data']);



        $this->form_validation->set_rules('mobile', 'Phone no', 'required|numeric|min_length[10]|max_length[10]|trim');

        # Check validation rule

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        $totRecord = $this->Common->find([

            'table' => USERS,

            'where' => "mobile = '{$inputs['mobile']}'",

            'query' => 'count'

        ]);



        $this->result['status'] = TRUE;

        if($totRecord > 0){

            $this->result['mobileExist'] = TRUE;

            $this->result['message'] = "Mobile already exist";

        }else{

            $this->result['mobileExist'] = FALSE;

        }



        return $this->set_response($this->result, REST_Controller::HTTP_OK);

    }



    /**

     *------------------------------------------------

     * OTP VERIFICATION

     *------------------------------------------------

     *

     * If OTP is created more than 30 minutes ago, it will be expired.

     * if verification successful then system will change

     * status from active to verified and user's mobile_verified to 'true'

     * 

     * @param mobile    : (string : 10) | REQUIRED, 

     * @param otp       : (string : 4-6) |REQUIRED , 

     * @param device_id : (string) | NULL

     * 

     * @return json array()

     */

    public function verifyotp_post() {

        $this->form_validation->set_rules('otp', 'OTP', 'trim|required|numeric|min_length[4]|max_length[6]');

        $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|numeric|min_length[8]|max_length[15]');

        

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        $inputs = $this->input->post();

        $conditions = (@$inputs['device_id'] != "") ? " AND `device_id` = '{$inputs['device_id']}'" : '';

        $otpDtls = $this->Common->find([

            'table'     => OTPS." Otp",

            'select'    => "Otp.*",

            'join'      => [[USERS, 'User', 'INNER', "User.mobile = Otp.mobile AND User.id = Otp.user_id"]],

            'where'     => "Otp.`mobile` = '{$inputs['mobile']}' 

                            AND Otp.`otp` = '{$inputs['otp']}' ".$conditions,

            'order'     => "Otp.created DESC",

            'query'     => 'first'

        ]);



        if(!empty($otpDtls)){

            # check if already verified or not

            if($otpDtls['status'] == 'verified'){

                $this->result['message'] = "OTP already verified";

                return $this->set_response($this->result, REST_Controller::HTTP_OK);

            }



            # If OTP is active and created before 30 minutes

            if(time() <= strtotime($otpDtls['created']." +30minutes") && $otpDtls['status'] == 'active'){

                if($this->Common->save(USERS, ['id' => $otpDtls['user_id'], "mobile_verified" => 'true'])){

                    $this->Common->save(OTPS, ['id' => $otpDtls['id'], 'status' => 'verified']);



                    # Fetching user details

                    $userContent    = $this->Common->find([

                        'table'     => USERS." User",

                        'select'    => "User.profile_id,User.email email,

                                        User.mobile mobileNumber, User.profile_img, 

                                        User.email_verified,User.mobile_verified,User.status,  

                                        Profile.fname AS firstName, 

                                        Profile.lname AS lastName, 

                                        Profile.gender, 

                                        Profile.dob AS birthDate,

                                        Profile.street_address,  

                                        Profile.zip AS zipCode,

                                        Profile.state, 

                                        Profile.city, 

                                        Profile.country",

                        'join'      => [[USER_PROFILE, 'Profile', 'INNER', "User.id = Profile.user_id"]],

                        'where'     => "User.id = '".$otpDtls['user_id']."'",

                        'query'     => 'first'

                    ]);



                    $userContent['profileImage'] = timthumb($userContent['profile_img'], 150, 150);

                    $addressArr = [

                        @$userContent['street_address'],

                        @$userContent['city'],

                        @$userContent['state'],

                        @$userContent['country'],

                        @$userContent['zipCode']

                    ];

                    $userContent['full_address'] = implode(', ', array_filter($addressArr));

                    $this->result = [

                        'status'    => TRUE,

                        'data'      =>   [

                            'userdata'  => $userContent,

                            # Generate JWT token

                            'token'     => $this->authorization_token->generateToken([

                                'id'         => $otpDtls['user_id'],

                                'email'      => $userContent['email'],

                                'profile_id' => $userContent['profile_id'],

                                'mobile'     => $userContent['mobileNumber'],

                                'time'       => time()

                            ])

                        ],

                        'message'   => "OTP verified successfully"

                    ];



                    return $this->set_response($this->result, REST_Controller::HTTP_OK);

                }

            }else{

                $this->result['message'] = "OTP Expeired";

                return $this->set_response($this->result, REST_Controller::HTTP_OK);

            }

        }else{

            $this->result['message'] = "Invalid OTP entered";

            return $this->set_response($this->result, REST_Controller::HTTP_NOT_FOUND);

        }        

    }





    /**

     *------------------------------------------------

     * SENT OTP DEPENDING ON USER_ID OR AUTHORIZATION

     *------------------------------------------------

     *

     * If user_id found get user details depending on user_id,

     * otherwise it will search for Authorization.

     * It will send SMS and Email for OTP Details

     * 

     * @header Authorization (optional)

     * @param user_id   : (int) | NULL | <EXISTING USER>, 

     * @param device_id : (string) | NULL

     * 

     * @return json array()

     */

    public function mobileotprequest_post() {

        $reqheader_data = $this->input->request_headers();

        $user_id    = $this->post('user_id');

        $mobile     = "";

        $email_id   = "";

        $dial_code  = "";

        $device_id  = ($this->post('device_id')) ? $this->post('device_id') : NULL;



        # Check Login token

        if(@$reqheader_data['Authorization']){

            $this->result['data']['token'] = $reqheader_data['Authorization'];



            $token_data = $this->authorization_token->validateToken();

            if ($token_data['status'] === false) {

                $this->result['message'] = $token_data['message'];

                return $this->set_response($this->result, REST_Controller::HTTP_UNAUTHORIZED);

            }



            #pre($this->authorization_token->userData(), 1);

            $authData   = $this->authorization_token->userData();

            $user_id    = @$authData->id;

        } else{



            # Get user details by user_id

            $this->form_validation->set_rules('user_id', 'User ID', 'trim|required|numeric');



            if($this->form_validation->run() == FALSE) {

                $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

                $this->result['errors'] = $this->form_validation->error_array();



                return $this->set_response($this->result, REST_Controller::HTTP_BAD_REQUEST);

            }

        }



        if($user_id != ""){

            $userDtls = $this->Common->findById(USERS, $user_id);

            #return response($userDtls);



            # Check if user is found or not

            if(empty($userDtls)){

                $this->result['message'] = "User not found";

                return $this->set_response($this->result, REST_Controller::HTTP_NOT_FOUND);

            }



            # check if user mobile number found or not

            if(empty(@$userDtls['mobile'])){

                $this->result['message'] = "Mobile no not found";

                return $this->set_response($this->result, REST_Controller::HTTP_NOT_FOUND);

            }

            $mobile         = @$userDtls['mobile'];

            $email_id       = @$userDtls['email'];

            $dial_code      = @$userDtls['dial_code'];

        }



        # Generate OTP

        $otpid = substr(time(), 6, 9);

        $otpdata = [

            'user_id'   => $user_id,

            'mobile'    => $mobile,

            'otp'       => $otpid,

            'device_id' => $device_id

        ];



        $userotp = $this->Common->save(OTPS, $otpdata);

        if($userotp){

            /*$email_message = $this->newotpemail_temp($user_id, $otpid);

            $emailSent = email(DOMAIN_MAIL, $email_id, "Blothru OTP ", $email_message);*/





            $dial_code = ($dial_code != "") ? $dial_code : DEFAULT_DIAL_CODE;

			$this->load->library('twilio');

			$this->twilio->send($dial_code.@$mobile, otp_message($otpid));



            $this->result['status'] = TRUE;

            $this->result['message'] = "OTP sent successfully";

            return $this->set_response($this->result, REST_Controller::HTTP_OK);

        }

    }   





    /**

     *------------------------------------------------

     * SOCIAL LOGIN (FACEBOOK)

     *------------------------------------------------

     *

     * @header Authorization (optional)

     * @param firstName     : (string) | REQUIRED

     * @param lastName      : (string) | NULL

     * @param email         : (string) | NULL

     * @param user_type     : (string) | NULL | DEFAULT CUSTOMER | enum[client,customer]

     * @param salon_title   : (string) | REQUIRED | <if registration for salon>

     * @param socialId      : (string) | REQUIRED

     * @param fcmToken      : (string) | NULL | <DEVICE_ID>

     *

     * @return json array()

     */

    public function sociallogin_post() {

        $data = $this->post();



        # Set validation rule

        $this->form_validation->set_rules('firstName', 'First Name ', 'trim|required');

        $this->form_validation->set_rules('lastName', 'Last Name ', 'trim');

        $this->form_validation->set_rules('email', 'Email ', 'trim|valid_email');

        $this->form_validation->set_rules('socialId', 'SocialId ', 'trim|required');

        $this->form_validation->set_rules('user_type', 'User type ', 'trim|enum[client,customer]');

        if(@$data['user_type'] == 'client'){

            $this->form_validation->set_rules('salon_title', 'Salon name ', 'trim|required|min_length[3]|max_length[64]');

        }



        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        # Check if user already exist with this social ID

        $userDtls = $this->Common->find([

            'table' => USERS,

            'where' => "social_id = '{$data['socialId']}'",

            'query' => 'first'

        ]);          



        if(!empty($userDtls) && @$userDtls['email'] == "" && @$data['email'] != ""){

            $this->Common->save(USERS, [

                'id'                => $userDtls['id'], 

                'email'             => @$data['email'], 

                'email_verified'    => 'true'

            ]);

        }



        # Check if email is given by facebook and if any account found with this email id

        if(empty($userDtls) && @$data['email'] != ""){

            $userDtls = $this->Common->find([

                'table' => USERS,

                'where' => "email = '{$data['email']}'",

                'query' => 'first'

            ]);  



            # If found that user connected with email which is provided by

            # Facebook, then connect with existing user, by adding social ID with 

            # this existing account

            if(!empty($userDtls)){

                if(@$data['socialId'] != ""){

                    $updateUser = [

                        'id'        => $userDtls['id'], 

                        'social_id' => $data['socialId']

                    ];

                    $this->Common->save(USERS, $updateUser);

                }

            }

        }



        # check if new user and insert new

        if(empty($userDtls)){

            $profileid = substr(time(), 4, 9);

            $mem_data = [

                'user_type'         => (@$data['user_type'] == 'client') ? 'client' : 'customer',

                'auth_by'           => 'facebook',

                'profile_id'        => 'BT-' . $profileid,

                'profile_img'       => '',

                'social_id'         => @$data['socialId']

            ];



            if(!empty(@$data['email'])){

                $mem_data['email']          = @$data['email'];

                $mem_data['email_verified'] = 'true';

            }



            # Save as a new user

            $userid = $this->Common->save(USERS, $mem_data);

            if (!isset($data['lastName'])){

                $data['lastName'] = null;

            }



            $mem_pro_data = [

                'fname'     => $data['firstName'],

                'lname'     => $data['lastName'],

                'user_id'   => $userid

            ];



            # Inser user profile information

            $userPid = $this->Common->save(USER_PROFILE, $mem_pro_data);

            if($userPid){



                # Check if it is client

                if(@$data['user_type'] == 'client'){

                    $checkIfSalonExist = $this->Common->find([

                        'table' => SALONS, 

                        'where' => "user_id = '{$userid}'",

                        'query' => 'count'

                    ]);



                    # Check if new client

                    if($checkIfSalonExist == 0) {

                        $this->Common->save(SALONS, [

                            'user_id'       => $userid, 

                            'name'          => $data['salon_title'],

                            'store_code'    => slug(SALONS, 'store_code', $data['salon_title']),

                            'email'         => @$data['email'],

                            'status'        => 'pending'

                        ]);

                    }

                }

            }

            $userDtls = $this->Common->findById(USERS, $userid);

        }

        

        if(!empty($userDtls)){

                            

            # Fetching user details

            $userContent    = $this->Common->find([

                'table'     => USERS." User",

                'select'    => "User.id, User.profile_id,User.email email,

                                User.mobile mobileNumber, User.profile_img, 

                                User.email_verified,User.mobile_verified,User.status,  

                                Profile.fname AS firstName, 

                                Profile.lname AS lastName, 

                                Profile.gender, 

                                Profile.dob AS birthDate,

                                Profile.address AS address,

                                Profile.street_address, 

                                Profile.zip AS zipCode,

                                Profile.state, 

                                Profile.city, 

                                Profile.country",

                'join'      => [[USER_PROFILE, 'Profile', 'INNER', "User.id = Profile.user_id"]],

                'where'     => "User.id = '{$userDtls['id']}'",

                'query'     => 'first'

            ]);



            $userContent['full_name']       = $userContent['firstName'].' '.$userContent['lastName'];

            $userContent['profileImage']    = timthumb($userContent['profile_img'], 150, 150);

            $addressArr = [

                @$userContent['street_address'],

                @$userContent['city'],

                @$userContent['state'],

                @$userContent['country'],

                @$userContent['zipCode']

            ];

            $userContent['full_address'] = implode(', ', array_filter($addressArr));

            unset($userContent['profile_img']);



            $returndata['userdata'] = $userContent;



            $tokendata['id']            = @$userDtls['id'];

            $tokendata['profile_id']    = @$userContent['profile_id'];

            $tokendata['email']         = @$userContent['email'];

            $tokendata['mobile']        = @$userContent['mobileNumber'];

            $tokendata['time']          = time();



            $returndata['token'] = $this->authorization_token->generateToken($tokendata);



            $this->result['status']     = TRUE;

            $this->result['data']       = $returndata;

            if($userContent['email_verified']=='false'){

                if($userContent['email'] == ""){

                    $this->result['warnings']['email']  = "Email address not found";

                } else {

                    $this->result['warnings']['email']  = "Email address is not verified";

                }

                $this->result['message'] = "Email address is not verified";

            }



            if($userContent['mobile_verified']=='false'){

                if($userContent['mobileNumber'] == ""){

                    $this->result['warnings']['mobile'] = "Mobile No. not found";

                }else{

                    $this->result['warnings']['mobile'] = "Mobile No. is not verified";

                }                

                $this->result['message'] = "Mobile No. is not verified";

            }else{

                $this->result['message']    = "Successfully login";

            }

            return $this->response($this->result, REST_Controller::HTTP_OK);

        }else{

            

            $this->result['message'] = "Oops! something went wrong";

            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);

        }

    }

    

    /**

     *------------------------------------------------

     * FORGET PASSWORD REQUEST

     *------------------------------------------------

     *

     * @param Authorization : (string) | REQUIRED | HEADER

     * @param email         : (string) | REQUIRED | <EMAIL_ADDRESS | MOBILE_NUMBER>

     * @param device_id     : (string) | NULL

     * @return json array()

     */

    public function forgotpass_post() {

        $this->form_validation->set_rules('email_or_opcode', 'Email or Opcode', 'trim|required');

        # Check validation

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }


        $inputs = $this->input->post();

        # Fetching user details depending on email or mobile number

        $getUserDtls = $this->Common->find([

            'table' => USERS,

            'where' => "email='{$inputs['email_or_opcode']}' OR opcode='{$inputs['email_or_opcode']}'",

            'query' => 'first'

        ]);

        # If record not found

        if(empty($getUserDtls)){

            $this->result['message'] = "Invalid email or opcode entered";

            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);

        }

        # Check if account is active or not

        if(@$getUserDtls['status'] != "A"){

            $this->result['message'] = "Your account is not active currently";

            return $this->response($this->result, REST_Controller::HTTP_OK);

        }

        # Save OTP

        $newOTP     = rand(111111, 999999);

        // $otpdata    = [

        //     'user_id'   => $getUserDtls['id'], 

        //     'mobile'    => $getUserDtls['mobile'],

        //     'otp'       => $newOTP,

        //     'device_id' => @$data['device_id'],

        //     'status'    => 'active'

        // ];



        //$OTPid = $this->Common->save(OTPS, $otpdata);

        $verification_data = $this->Common->find([

            'table' => VERIFICATION_CODE,

            'where' => "user_id='{$getUserDtls['id']}'",

            'query' => 'first'

        ]);

        $otpdata    = [

            'user_id'   => $getUserDtls['id'], 
            'code'      => $newOTP,
        ];

        if(!empty($verification_data))
        {
            $OTPid = $this->Common->save(VERIFICATION_CODE, array('otp_status'=>'','code'=>$newOTP,'created_at'=>date('Y-m-d H:i:s')),array('user_id'=>$getUserDtls['id']));
        }
        else
        {
            $OTPid = $this->Common->save(VERIFICATION_CODE, $otpdata);
        }

        

        
        

        if($OTPid){

            $email_message = $this->otpemail_temp($getUserDtls['id'], $newOTP);

            $emailSent = email(DOMAIN_MAIL, $getUserDtls['email'], "Stuffyriders | Reset Password ", $email_message);


            $this->result['status']     = TRUE;

            $this->result['message']    = "Please check your inbox for OTP";

            $this->result['data']       = [

                'otp'       => $newOTP,

                'user_id'   => $getUserDtls['id'],

                'email'     => $getUserDtls['email'],

                //'mobile'    => $getUserDtls['mobile']

            ];

            return $this->response($this->result, REST_Controller::HTTP_OK);

        }

        $this->result['message'] = "Oops! Something went wrong.";

        return $this->response($this->result, REST_Controller::HTTP_OK);       

    }

    

    /**

     *------------------------------------------------

     * RESET PASSWORD

     *------------------------------------------------

     *

     * @param email     : (string)  | REQUIRED | <EMAIL_ADDRESS>

     * @param otp       : (int)     | REQUIRED 

     * @param password  : (string)  | REQUIRED | MIN 6 | MAX 30

     * @param cpassword : (string)  | REQUIRED | MIN 6 | MAX 30

     *

     * @return json array()

     */

    public function resetpassword_post() {

        $this->form_validation->set_rules('user_id', 'User id ', 'trim|required');

        $this->form_validation->set_rules('otp', 'OTP ', 'trim|required|numeric');

        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[30]');

        //$this->form_validation->set_rules('cpassword', 'Confirm Password ', 'trim|required');

        # Check validation

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }

        

        $post = (object)$this->input->post();
        $password = $post->password;
        $user_id = $post->user_id;
        $otp = $post->otp;

        $validation = $this->Common->find([

            'table'     => VERIFICATION_CODE,

            'select'    => "*",

            'where'     => "code='{$otp}' AND user_id='{$user_id}'",

            'query'     => "first"

        ]);

        # if record not found

        if(empty($validation)){

            $this->result['message'] = "Invalid OTP entered";

            return $this->response($this->result, REST_Controller::HTTP_NOT_FOUND);

        }



        if($validation['otp_status'] == 'verified'){

            $this->result['message'] = "OTP already used";

            return $this->response($this->result, REST_Controller::HTTP_OK);

        }


        # If otp expired
        //echo strtotime($validation['created_at'])+(30*60);
        //echo time();
        if((time() - strtotime($validation['created_at'])) > 30*60){            

            $this->result['message'] = "OTP expired";

            return $this->response($this->result, REST_Controller::HTTP_OK);

        }


        # change password

            $hashPass = createpassword($password);

        if($this->Common->save(USERS, array('password' => $hashPass),array('id' => $user_id))){

            $this->Common->save(VERIFICATION_CODE, array('otp_status' => 'verified'),array('user_id'=>$user_id));

            $this->result['status'] = TRUE;

            $this->result['message'] = "Password changed successfully";

            return $this->response($this->result, REST_Controller::HTTP_OK);

        } else{

            $this->result['message'] = "Oops! Something went wrong";

            return $this->response($this->result, REST_Controller::HTTP_OK);

        }

    }



    public function check_get() {

        try {  

            return response($this->result, REST_Controller::HTTP_OK);

        } catch(Exception $e) {

            $this->result['message'] = $e->getMessage();

            return $this->response($this->result, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

        }

    }



    public function newotpemail_temp($user, $otpno) {

        //$url = BASE_URL . 'reset-password/' . encrypt($user) . '?key=' . encrypt($otpno);

        return $email_message = '

                    <table align="center" border="0" cellpadding="0" cellspacing="0" style="width:620px">

                    <tbody>

                        <tr>

                        <td style="height:14px">&nbsp;</td>

                        </tr>

                        <tr>

                        <td>

                        <br/>

                        

                        </td>

                        </tr>

                        <tr>

                            <td><b>OTP :: </b>' . $otpno . '</td>

                        </tr>';



        $email_message .= '<tr>

                            <td style="height:40px">

                                <br/>Thanks & Regards,

                                <br/>

                                <br/>

                                <b><br/>Blothru <b>

                            </td>

                        </tr>

                    </tbody>

                </table>



                <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-top:6px solid #000; width:620px">

                    <tbody>

                        <tr>

                            <td>

                                <table style="background:#000; color:#000; width:620px">

                                    <tbody>

                                        <tr>

                                            <td>&nbsp;</td>

                                        </tr>

                                    </tbody>

                                </table>

                            </td>

                        </tr>

                    </tbody>

                </table>';

    }



    public function otpemail_temp($user, $otpno) {

        //$url = BASE_URL . 'reset-password/' . encrypt($user) . '?key=' . encrypt($otpno);

        return $email_message = '<p>OTP for password reset : '.$otpno.'</p><p>Valid for 30 minutes.</p>';

    }



    /**

     * -------------------------------------------

     * SITE SETTING, RECORDS ARE CONTROL BY ADMIN

     * -------------------------------------------

     */

    public function settings_post() {

        try {

            $settings = $this->Common->find(SETTINGS);

            $this->result['data'] = [];

            if(!empty($settings)){

                foreach($settings AS $eachSettings){

                    $this->result['data'][$eachSettings['set_key']] = (is_numeric($eachSettings['set_value'])) ?  (double) $eachSettings['set_value'] : $eachSettings['set_value'];

                }

            }

            $this->result['status'] = TRUE;

            return $this->response($this->result, REST_Controller::HTTP_OK);

        } catch(Exception $e) {

            $this->result['message'] = $e->getMessage();

            return $this->response($this->result, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

        }

    }



    public function twilio_post(){

        $this->load->library('twilio');

        $response = $this->twilio->send(@$this->params['to'], @$this->params['message']);



        $this->result['status']     = $response['status'];

        $this->result['message']    = $response['message'];

        $this->result['response']   = $response;

        return $this->response($this->result, REST_Controller::HTTP_OK);

    }



    public function onesignal_post(){    

        #return $this->response($this->params);    

        $this->form_validation->set_rules('user', 'User ', 'trim|required|numeric');

        $this->form_validation->set_rules('message', 'Message ', 'trim|required');

        $this->form_validation->set_rules('title', 'Title ', 'trim');

        $this->form_validation->set_rules('image', 'Image ', 'trim');

        $this->form_validation->set_rules('app', 'Blothru App', 'trim|enum[1,2]');

        # Check validation

        if($this->form_validation->run() == FALSE) {

            $this->result['message'] = strip_tags(str_replace("\n", '', validation_errors()));

            $this->result['errors'] = $this->form_validation->error_array();



            return $this->response($this->result, REST_Controller::HTTP_BAD_REQUEST);

        }



        if($this->params['app'] == 2){

            $this->result['response'] = provider_notification($this->params['user'], $this->params['message'], @$this->params['title'], @$this->params['image']);

        } else{

            $this->result['response'] = blothru_notification($this->params['user'], $this->params['message'], @$this->params['title'], @$this->params['image']);

        }

        $this->result['status'] = TRUE;

        return $this->response($this->result, REST_Controller::HTTP_OK);

    }

}





