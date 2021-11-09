<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Dev extends REST_Controller {
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

    public function check_post(){
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

}


