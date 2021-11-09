<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MX_Controller {

    private $data;
    public function __construct()  
    { 
    	parent::__construct();
    	//$this->layout->set("default");
    	$this->data = [];
    }

    public function delete_record(){
        if($this->input->post()){
            $success = 0;
            $params = $this->input->post();
            //pre($params);
            $table      = constant(@$params['table']);
            $id         = decrypt(@$params['record_id']);
            
            if(@$params['drop'] == 1){
                $success = $this->Common->drop($table, $id);
            }else{
                $success = $this->Common->delete($table, $id);
            }

            if($success){
                die(json_encode(['status' => 1, 'msg' => "Deleted successfully"]));
            }else{
                die(json_encode(['status' => 0, 'msg' => "Please try again"]));
            }
        }
    }

    public function status_change(){
        if($this->input->post()){
            $success = 0;
            $params = $this->input->post();
            //pre($params);
            $table      = constant(@$params['table']);
            $id         = decrypt(@$params['record_id']);
            $success    = $this->Common->change_status($table, $id);
            //echo last_query();

            if($success){
                die(json_encode(['status' => 1, 'msg' => "Status changed successfully"]));
            }else{
                die(json_encode(['status' => 0, 'msg' => "Please try again"]));
            }
        }
    }

    public function delete_image($imgId = NUll){
        if($imgId != ""){
            if($this->Common->drop(IMAGES, $imgId)){
                echo 1;
            }else{
                echo 0;
            }
        }
    }
}
