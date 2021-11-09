<?php



defined('BASEPATH') or exit('No direct script access allowed');







class User extends MX_Controller

{







    private $data;



    public function __construct()



    {



        //GET ACCESS

        $admin_sess = $this->session->userdata('admin_sess');

        if($admin_sess['role'] != 1)

        {

            @redirect(base_url('admincp'));

        }

        

        parent::__construct();



        $this->layout->set("admin-panel");



        $this->data = [];

    }







    public function list()

    {



        $breadcrumb = [



            [



                'page' => "User List",



            ],



        ];







        $this->layout->set_breadcumb($breadcrumb);



        $this->layout->set_title("User list");



        $this->layout->view('admin/user-list', $this->data);

    }



    public function userlist()

    {



        $draw = intval($this->input->get("draw"));

        $start = intval($this->input->get("start"));

        $length = intval($this->input->get("length"));





        $this->db->select('*');

        $this->db->from('st_users');

        // $this->db->join('st_user_roles', 'st_user_roles.id = st_users.role');

        // $this->db->where("st_users.role", 3);

         $this->db->where("st_users.status !=", "D");

        $query = $this->db->get()->result();

        foreach ($query as $key => $row) {

            $data[] = array(

                "<input type='checkbox' class='check' value='" . $row->id . "'/>",

                $key + 1,

                $row->name,

                $row->role,

                //$row->opcode,

                $row->email,

                $row->phone,

                (!empty($row->image)) ? "<img src='" . base_url() . '/public/uploads/users/' . $row->image . "' height='50px' width='50px'/>" : "<img src='https://previews.123rf.com/images/pavelstasevich/pavelstasevich1811/pavelstasevich181101065/112815953-no-image-available-icon-flat-vector.jpg' height='50px' width='50px'>",

                // ($row->status == 'A') ? "<img alt='Accept, active, agree, approved, check, checkmark, confirmed, correct,  done, good, ok, success, valid, validation, verify icon' class='n3VNCb' src='https://www.iconfinder.com/data/icons/free-basic-icon-set-2/300/11-512.png' jsname='HiaYvf' jsaction='load:XAeZkd;' data-iml='6012.000000000626' style='width: 20px; height: 20px; margin: 0px;'>" : "<img alt='Accept, active, agree, approved, check, checkmark, confirmed, correct,  done, good, ok, success, valid, validation, verify icon' class='n3VNCb' src='https://w7.pngwing.com/pngs/41/41/png-transparent-red-x-button-icon-error-http-404-icon-red-cross-mark-file-miscellaneous-trademark-heart.png' jsname='HiaYvf' jsaction='load:XAeZkd;' data-iml='6012.000000000626' style='width: 20px; height: 20px; margin: 0px;'>",
                ($row->status == 'A') ? '<i class="glyphicon glyphicon-ok-circle text-success"></i>' : '<i class="glyphicon glyphicon-remove-circle text-danger"></i>',

                "<a href='" . ADMIN_URL . 'user/update/' . encrypt($row->id) . "' class='btn btn-info'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-danger' id='del-" . $row->id . "' onclick='deleteusers(" . $row->id . ")'><i class='fa fa-trash'></i></button>",



            );

        }

        //print_r(count($query)); exit;

        $output = array(

            "draw" => $draw,

            "recordsTotal" => count($query),

            "recordsFiltered" => count($query),



            "data" => $data

        );



        echo json_encode($output);

        exit();

    }

    public function createuser()

    {
    // 	$headers  = 'MIME-Version: 1.0' . "\r\n";

				// $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    // 	$from = 'sankarnandi1010@gmail.com';
    // 	$headers .= 'From: '.$from."\r\n".

				//     		'Reply-To: '.$from."\r\n" .

				//     		'X-Priority: 1 (Highest)'."\r\n".

				//     		'X-MSMail-Priority: High'."\r\n".

				//     		'X-Mailer: PHP/' . phpversion();

    // 	if (mail($to='sankarnandi1010@gmail.com', $subject='test subject', $message='Hi test message.',$headers)) {
    // 		# code...
    // 		echo "string";
    // 	}
    // 	else
    // 	{
    // 		echo "dsds";
    // 	}

    	// email(DOMAIN_MAIL, 'sankarnandi1010@gmail.com', "Welcome to Hotel Aviana | Registration success", $email_message='test2');
    	// exit;
        //$this->data['stores'] = $this->Custom->get_all_stores();

        $id = $this->uri->segment(4);

        $get_id = decrypt($id);

        if (!empty($get_id))

        {

            $breadcrumb = [



            [



                'page' => "User Update",

            ]



        ];

        }

        else

        {

            $breadcrumb = [



            [



                'page' => "User Add",



            ]



        ];

        }

        

        

        if (!empty($get_id)) {

            $this->data['user_details'] = $this->Common->findById('st_users', $get_id);

            $this->layout->set_breadcumb($breadcrumb);

            $this->layout->set_title("User Update");

            $this->layout->view('admin/user-add', $this->data);

        } else {



            $this->layout->set_breadcumb($breadcrumb);

            $this->layout->set_title("User Add");

            $this->layout->view('admin/user-add', $this->data);

        }

    }

    public function submituser()

    {



        $id = $this->input->post('user_id');

        if ($id != '') {

            $data['name'] = $this->input->post('name');

            $data['email'] = $this->input->post('email');

            //$data['password'] = createpassword($this->input->post('password'));

            $data['phone'] = $this->input->post('phone');

            $data['status'] = $this->input->post('status');

            // $data['opcode'] = $this->input->post('opcode');

            // $data['store_id'] = $this->input->post('store_id');

            //$opcode =$this->input->post('opcode') - $id;

            // $this->Custom->update_by_table('st_users',['opcode'=>$opcode],$id);

           // $data['role'] = 2;

            $file_name = $this->do_upload("image", 'public/uploads/users/');

            if ($file_name != '') {

                $data['image'] = $file_name;

                //$old_image = $this->Custom->get_old_image('st_users', $id, 'image');

                //unlink('public/uploads/users/' . $old_image[0]->image);

            }

            $this->Custom->update_by_table('st_users', $data, $id);



            echo json_encode(["stat" => "success", "msg" => 'User updated successfully', "error" => (object)[]]);

        } else {

            $this->load->library('form_validation');

            $this->form_validation->set_rules('name', 'Name', 'required');

            $this->form_validation->set_rules('email', 'email', 'required|is_unique[st_users.email]');

            $this->form_validation->set_rules('password', 'Password', 'required');

            $this->form_validation->set_rules('phone', 'phone', 'required');

            $this->form_validation->set_rules('status', 'status', 'required');

            //$this->form_validation->set_rules('opcode', 'opcode', 'required');

            if ($this->form_validation->run() == FALSE) {



                echo json_encode(["stat" => "error", "msg" => "User missed something"]);

            } else {

                $data['name'] = $this->input->post('name');

                $data['email'] = $this->input->post('email');

                $data['password'] = createpassword($this->input->post('password'));

                $data['phone'] = $this->input->post('phone');

                //$data['store_id'] = $this->input->post('store_id');

                $data['status'] = $this->input->post('status');

                $data['image'] = $this->do_upload("image", 'public/uploads/users/');

                $data['role'] = 2;

                $this->Common->save('st_users', $data);

                $u_id = $this->db->insert_id();

                // $opcode = $this->input->post('opcode') - $u_id;

                //$opcode = $this->input->post('opcode');

                //EMAIL NEW USER

                $email_message = "<h3>Welcome to Hotel Aviana<h3>";

                $email_message .= "<p>Email:{$data['email']}<p>";

                //$email_message .= "<p>Opcode:{$opcode}<p>";

                $email_message .= "<p>Password:{$this->input->post('password')}<p>";



                $emailSent = email(DOMAIN_MAIL, $data['email'], "Welcome to Hotel Aviana | Registration success", $email_message);

                //$readomids = (rand(00000,99999).date('Y'))-$u_id;

                

                //$this->Custom->update_by_table('st_users', ['opcode' => $opcode], $u_id);

                echo json_encode(["stat" => "success", "msg" => "User added successfully", "error" => (object)[]]);

            }

        }

    }



    public function do_upload($image, $path)

    {



        $config['upload_path']          = $path;

        $config['allowed_types']        = 'gif|jpg|png';

        $config['max_size']             = 1024;

        $config['max_width']            = 1024;

        $config['max_height']           = 768;

        $config['encrypt_name']         = TRUE;



        $new_name = time();

        $config['file_name'] = $new_name;





        $this->load->library('upload', $config);



        if ($this->upload->do_upload($image)) {

            $data = array($this->upload->data());

            $file_name = $data[0]['file_name'];

            return  $file_name;

        }

    }

    public function emailexsists_user_mail()

    {

        $email = $this->input->post('email');

        $chekemail = $this->Custom->checkexsiting_user_mail($email);

        if ($chekemail) {

            echo "1";

        } else {

            echo "0";

        }

    }



    public function delete_user()

    {

        $id = $this->input->post('id');

        $data = $this->Custom->update_rows('st_users', array('status' => 'D'), $id);

        return $data;

    }

}

