<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends MX_Controller
{
	public $data = [];

	public function __construct()	{
		$this->load->library('form_validation');

	}

	/**
     * ADMIN LOGIN
     * params : {email_address, password}
     */
    public function member_login(){
        if(!empty($this->input->post())){
			
			unset($_SESSION['user_id']);
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
                    $this->session->set_userdata(CUSTOMER_SESS, [
                                                                'status'    => 1, 
                                                                'id'        => $userDtls['id'], 
                                                                'data'      =>  [
                                                                                    'name'  => $userDtls['name'], 
                                                                                    'email' => $userDtls['email'],
                                                                                    'phone' => $userDtls['phone']
                                                                                ]
                                                            ]);
                }
            }

			if(isset($inputs['loginFrom']) && $inputs['loginFrom']=="popup")
			{
				redirect($_SERVER['HTTP_REFERER']);
			}

            if($error == TRUE){
                die(json_encode(['status' => 0, 'msg' => "Invalid email or password entered"]));
            }else{
                die(json_encode(['status' => 1, 'msg' => "Login successful"]));
            }
        }
		die(json_encode(['status' => 1, 'msg' => "Invalid request"]));
		
    }

    public function logout(){
        $this->session->unset_userdata(CUSTOMER_SESS);
        redirect(BASE_URL.'auth/login');
	}
	
	public function check_if_exist($userId = NULL){
		if(@$_GET['email'] != ""){
			$user = $this->User->findBy('email', $_GET['email']);
			if(!empty($user)){
				echo "false";
			}else{
				echo "true";
			}
		}
		if(@$_GET['phone'] != ""){
			$user = $this->User->findBy('phone', $_GET['phone']);
			if(!empty($user)){
				echo "false";
			}else{
				echo "true";
			}
		}
	}

    public function member_save(){
		if(!empty($this->input->post())){
			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required|min_length[3]|max_length[128]');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|min_length[3]|max_length[128]');
			$this->form_validation->set_rules('email', 'email address', 'trim|required|valid_email|min_length[3]|max_length[128]');
            $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[6]|max_length[64]');

            if ($this->form_validation->run() == FALSE){
                die(json_encode(['status' => 0, 'msg' => validation_errors()]));
			}
			$data = $this->input->post();
			if(!isset($_SESSION['user_id']))
			{
				$formData = [
					'role' 		=> 2,
					'name' 		=> $data['first_name'].' '.$data['last_name'],
					'email' 	=> $data['email'],
					'phone' 	=> $data['phone'],
					'password' 	=> createpassword($data['password'])
				];
				if($userId = $this->User->save($formData)){
					$profileData = [
						'user_id' 		=> $userId,
						'first_name' 	=> $data['first_name'],
						'last_name' 	=> $data['last_name'],
					];
					$this->Common->save(USER_PROFILES, $profileData);
					die(json_encode(['status' => 1, 'msg' => "Registration successful"]));
				}else{
					die(json_encode(['status' => 0, 'msg' => "Please try again"]));
				}
			}
			else{
				$formData = [
					'id'        => $_SESSION['user_id'],
					'role' 		=> 2,
					'name' 		=> $data['first_name'].' '.$data['last_name'],
					'email' 	=> $data['email'],
					'phone' 	=> $data['phone'],
					'password' 	=> createpassword($data['password'])
				];
				if($this->db->insert(USERS,$formData)){
					$profileData = [
						'user_id' 		=> $_SESSION['user_id'],
						'first_name' 	=> $data['first_name'],
						'last_name' 	=> $data['last_name'],
					];
					$this->Common->save(USER_PROFILES, $profileData);
					die(json_encode(['status' => 1, 'msg' => "Registration successful"]));
				}else{
					die(json_encode(['status' => 0, 'msg' => "Please try again"]));
				}
			}
			

			
		}
	}
}
