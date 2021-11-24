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

    public function updateRoom()
    {
        $breadcrumb = [
            [
                'page' => "Room List",
                'url' => ADMIN_URL . 'rooms/all'
            ], [
                'page' => "Update Room"
            ]
        ];
        //$this->data['country'] = $this->Common->findAll('st_countries');
        $id  = $this->uri->segment(4);
        $get_id = decrypt($id);
        if (!empty($get_id)) {
            $data['storeDetails'] = $this->Common->findById('hotel', $get_id);
            // $data['country'] = $this->Common->findAll('st_countries');
            // $data['state'] = $this->Common->findById('st_states', $data['storeDetails']['country']);
            // $data['cities'] = $this->Common->findById('st_cities', $data['storeDetails']['city']);
            $this->layout->set_breadcumb($breadcrumb);
            $this->layout->set_title("Update room");
            $this->layout->view('user/user-add', $data);
        } else {
            $this->layout->set_breadcumb($breadcrumb);
            $this->layout->set_title("Add Room");
            $this->layout->view('user/user-add', $this->data);
        }
    }
    public function create_gallery()
    {
        $breadcrumb = [
            [
                'page' => "Add Gallery",
                'url' => ADMIN_URL . 'settings/content-types'
            ]
        ];

        
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title("Add Gallery");
        $this->layout->view('user/add_gallery', $this->data);
    }

    
    public function submitgallery()
    {
      
        $data =  $this->input->post();
            $countfiles = count($_FILES['files']['name']);
            for($i=0;$i<$countfiles;$i++){
                if(!empty($_FILES['files']['name'][$i])){
                       $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                      $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                      $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                      $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                      $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                      // Set preference
                      $config['upload_path'] = 'public/uploads/gallery/'; 
                      $config['allowed_types'] = 'jpg|jpeg|png|gif';
                      $config['max_size'] = '5000'; // max_size in kb

                      $new_name = time();
                        $config['file_name'] = $new_name;
                      //$config['file_name'] = $_FILES['files']['name'][$i];
             
                      //Load upload library
                      $this->load->library('upload',$config); 
                      if($this->upload->do_upload('file')){
                        // Get data about the file
                        $uploadData = $this->upload->data();
                        $filename = $uploadData['file_name'];


                        // Initialize array
                        $data['image'] = $filename;
                        $this->Common->save('gallery', $data);
                      }
                }

        }

            //$file_name = $this->do_upload('image[]', 'public/uploads/test/');
            //var_dump($file_name);
           // if ($file_name != '') {
            //    $data['image'] = $file_name;
                //$old_image = $this->Custom->get_old_image('st_stores', $id, 'image');
                //unlink('public/assets/store/' . $old_image[0]->image);
            //}
           
           
           echo json_encode(["stat"=>"success","msg"=>"Gallery added Successfully","data"=>$data]);
    }
    public function delete_user()
    {

        $id = $this->input->post('id');
        $data = $this->Custom->update_custom(array('status' => 'D'), $id);
        return $data;
    }

    public function delete_gallery()
    {
        $id = $this->input->post('id');
        $data = $this->Custom->update_by_table('gallery',array('status' => 'I'), $id);
        return $data;
    }

    
    public function delete_hotel_image()
    {
        $id = $this->input->post('id');
        $data = $this->Custom->update_by_table('hotel_image',array('status' => 'I'), $id);
        return $data;
    }




    public function roomList()
    {
        $breadcrumb = [
            [
                'page' => 'Room List'
            ]
        ];


        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title('Room List');
        $this->layout->set("admin-panel");
        $this->layout->view('roomListView', $this->data);
    }

    public function roomPricesByDayOfWeek() {
        $breadcrumb = [['page' => 'Room Prices by Day of Week']];
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title('Room Prices by Day of Week');
        $this->layout->set("admin-panel");
        $this->layout->view('roomPricesByDayOfWeek', $this->data);
    }

    public function roomPricesByDate() {
        $breadcrumb = [['page' => 'Room Prices by Date']];
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title('Room Price by Date');
        $this->layout->set("admin-panel");
        $this->layout->view('roomPricesByDate', $this->data);
    }

    public function roomListAPI() {
        $draw = intval($this->input->get("draw"));
        // $start = intval($this->input->get("start"));
        // $length = intval($this->input->get("length"));


        $this->db->select('*');
        $this->db->from('hotel');
        // $this->db->join('st_user_roles', 'st_user_roles.id = st_stores.role');
        // $this->db->where("st_stores.role", 2);
        // $this->db->where("st_stores.status !=", "D");
        $query = $this->db->get()->result();
        foreach ($query as $key => $row) {
           // $available_rooms =  $this->Custom->get_available_room($row->id);
            //$image = "<img src=''>";
            $data[] = array(
                "<input type='checkbox' class='check' value='" . $row->id . "'/>",
                $key + 1,
                // "<img src='".base_url().'public/uploads/room/'.$row->image."'/ style='width:48px;height:36px'>",
                $row->name,
                $row->person,
                $row->size,
                $row->back_rate,
                $row->gst,
                $row->net_rate,
                $row->no_of_room,
                "<a href='" . ADMIN_URL . 'hotel-image/' . encrypt($row->id) . "' class='btn btn-info'>Images</a>",
                "<a href='" . ADMIN_URL . 'rooms/update/' . encrypt($row->id) . "' class='btn btn-info'><i class='fa fa-edit'></i></a>"

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

    public function roomPricesAPI() {
        $sql = "SELECT wdi.id, wdi.day, rrbdow.id, rrbdow.rate as new_rate, h.* 
            FROM room_rates_by_day_of_week rrbdow
            JOIN hotel h ON h.id = rrbdow.room_id
            JOIN week_day_indexes wdi ON CAST(wdi.id AS char) COLLATE utf8_unicode_ci = rrbdow.day_of_week ORDER BY h.slug;";
        $query = $this->db->query($sql)->result();
        foreach ($query as $key => $row) {
            $data[] = array(
                "<input type='checkbox' class='check' value='" . $row->id . "'/>",
                $key + 1,
                $row->day,
                $row->name,
                $row->person,
                $row->size,
                $row->back_rate,
                $row->gst,
                $row->new_rate,
                $row->no_of_room,
                // "<a href='" . ADMIN_URL . 'hotel-image/' . encrypt($row->id) . "' class='btn btn-info'>Images</a>",
                "<a href='" . ADMIN_URL . 'rooms/update/' . encrypt($row->id) . "' class='btn btn-info'><i class='fa fa-edit'></i></a>"
            );
        }
        $output = array(
            "draw" => 0,
            "recordsTotal" => count($query),
            "recordsFiltered" => count($query),

            "data" => $data
        );

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($output));
    }

    public function roomPricesByDateAPI() {
        $sql = "SELECT rrbd.id, rrbd.date, rrbd.id, rrbd.rate as new_rate, h.* 
            FROM room_rates_by_date rrbd
            JOIN hotel h ON h.id = rrbd.room_id 
            ORDER BY h.slug;";
        $query = $this->db->query($sql)->result();
        foreach ($query as $key => $row) {
            $data[] = array(
                "<input type='checkbox' class='check' value='" . $row->id . "'/>",
                $key + 1,
                date("F j, Y", strtotime($row->date)),
                $row->name,
                $row->person,
                $row->size,
                $row->back_rate,
                $row->gst,
                $row->new_rate,
                $row->no_of_room,
                // "<a href='" . ADMIN_URL . 'hotel-image/' . encrypt($row->id) . "' class='btn btn-info'>Images</a>",
                "<a href='" . ADMIN_URL . 'rooms/update/' . encrypt($row->id) . "' class='btn btn-info'><i class='fa fa-edit'></i></a>"
            );
        }
        $output = array(
            "draw" => 0,
            "recordsTotal" => count($query),
            "recordsFiltered" => count($query),

            "data" => $data
        );

        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($output));
    }

    public function gallerylist()
    {

        $draw = intval($this->input->get("draw"));
        // $start = intval($this->input->get("start"));
        // $length = intval($this->input->get("length"));
        $data = array();

        $this->db->select('*');
        $this->db->from('gallery');
        $this->db->where("status =", "A");
        $query = $this->db->get()->result();
        foreach ($query as $key => $row) {
            //$image = "<img src=''>";
            $data[] = array(
                "<input type='checkbox' class='check' value='" . $row->id . "'/>",
                $key + 1,
                 "<img src='".base_url().'public/uploads/gallery/'.$row->image."'/>",
                 "<button type='button' class='btn btn-danger' id='del-" . $row->id . "' onclick='deleteusers(" . $row->id . ")'><i class='fa fa-trash'></i></button>"

            );
        }
        //print_r(count($query)); exit;
        $output = array(
            "draw" => $draw,
            "recordsTotal" => count($query),
            "recordsFiltered" => count($query),
            "data" => $data,
        );

        echo json_encode($output);
        exit();
    }

    public function submit_site_settings($value='') {
        $data = $this->input->post();
        $data = $this->Custom->update_by_table('site_settings', $data, 1);
        echo json_encode(["stat"=>"success","msg"=>"Settings updated Successfully"]);
    }

    public function upsertRoomDetails() {
      
        $id = $this->input->post('store_id');
        if (empty($id)) {
                $data = $this->input->post();
                $data['description'] = $data['editor1'];
                unset($data['store_id']);
                unset($data['editor1']);
                $file_name = $this->do_upload('image', 'public/uploads/room/');
                //var_dump($file_name);
                if ($file_name != '') {
                    $data['image'] = $file_name;
                    //$old_image = $this->Custom->get_old_image('st_stores', $id, 'image');
                    //unlink('public/assets/store/' . $old_image[0]->image);
                }
                $this->Common->save('hotel', $data);
                echo json_encode(["stat"=>"success","msg"=>"Room added Successfully"]);
            
        } else {

            $data =  $this->input->post();
            

            $data['description'] = $data['editor1'];
            //var_dump($data);
            unset($data['store_id']);
            unset($data['editor1']);
            $file_name = $this->do_upload('image', 'public/uploads/room/');
            //var_dump($file_name);
            if ($file_name != '') {
                $data['image'] = $file_name;
                //$old_image = $this->Custom->get_old_image('st_stores', $id, 'image');
                //unlink('public/assets/store/' . $old_image[0]->image);
            }
           $data = $this->Custom->update_by_table('hotel', $data, $id);
           
           echo json_encode(["stat"=>"success","msg"=>"Room updated Successfully","data"=>$data]);
        }
    }

    public function do_upload($image, $path)
    {



        $config['upload_path']          = $path;
        $config['allowed_types']        = 'gif|jpg|png|jpeg';
        $config['max_size']             = 1024;
        $config['max_width']            = 2000;
        $config['max_height']           = 2000;

        $new_name = time();
        $config['file_name'] = $new_name;


        $this->load->library('upload', $config);

        if ($this->upload->do_upload($image)) {
            $data = array($this->upload->data());

            $file_name = $data[0]['file_name'];

            return  $file_name;
        }
        //var_dump($this->upload->display_errors());
    }

    
    public function emailexsists()
    {
        $email = $this->input->post('email');
        $chekemail = $this->Custom->checkexsitingemail($email);
        if ($chekemail) {
            echo "1";
        } else {
            echo "0";
        }
    }

    public function gallery($value='')
    {
        $breadcrumb = [
            [
                'page' => 'Gallery List'
            ]
        ];

        $gallery = $this->Common->find([
            'table'     => 'gallery', 
            'select'    => "*",
            // 'join'      => [

            //                     ['hotel', 't2', 'INNER', "t1.room_id = t2.id"],
            //                 ],
            'where'=>'status="A"',
            'order'=>'id DESC'
            
            ]);
        $this->data['gallery'] = $gallery;
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title('Room List');
        $this->layout->set("admin-panel");
        $this->layout->view('user/gallery', $this->data);
    }

    public function site_settings()
    {
        $breadcrumb = [
            [
                'page' => "Site settings",
            ]
        ];
        
        $this->data['site_settings'] = $this->Common->find('site_settings');
        //var_dump($data['site_settings']);
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title("Site settings");
        $this->layout->view('user/site_settings', $this->data);
    }

    public function pages($slug='')
    {
        $breadcrumb = [
            [
                'page' => "Pages",
            ]
        ];
        $details = $this->Common->find([
            'table'     => 'pages', 
            'select'    => "*",
            // 'join'      => [

            //                     ['hotel', 't2', 'INNER', "t1.room_id = t2.id"],
            //                 ],
            'where'     => "slug = '{$slug}'",
            'query'=>'first',
            ]);
        
        $this->data['details'] = $details;
        //var_dump($data['site_settings']);
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title($details['name']);
        $this->layout->view('user/pages', $this->data);
    }

    public function insert_page($value='')
    {
        if(isset($_POST))
        {
            $this->db->update('pages',array('content'=>$_POST['editor1']),array('slug'=>$_POST['slug']));
            echo json_encode(["stat"=>"success","msg"=>"Pages updated Successfully","data"=>$_POST]);
        }
    }

    public function hotel_image($value='')
    {
        $hotel_id = decrypt($value);
        $room_images = $this->Custom->get_room_images($hotel_id);
        $this->data['room_images'] = $room_images;
        //var_dump($data['site_settings']);
        $breadcrumb = [
            [
                'page' => 'Room Images'
            ]
        ];

        $hotel_details = $this->Common->find([
            'table'     => 'hotel', 
            'select'    => "id,name",
            'where'=>"id='{$hotel_id}'",
            'query'=>'first',
            ]);

        $this->data['hotel_details'] = $hotel_details;
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title('Room Images');
        $this->layout->view('user/hotel_image', $this->data);
    }

    public function submit_hotel_image($value='')
    {
        $data =  $this->input->post();
            $countfiles = count($_FILES['files']['name']);
            for($i=0;$i<$countfiles;$i++){
                if(!empty($_FILES['files']['name'][$i])){
                       $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                      $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                      $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                      $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                      $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                      // Set preference
                      $config['upload_path'] = 'public/uploads/room/'; 
                      $config['allowed_types'] = 'jpg|jpeg|png|gif';
                      $config['max_size'] = '5000'; // max_size in kb

                      $new_name = time();
                        $config['file_name'] = $new_name;
                      //$config['file_name'] = $_FILES['files']['name'][$i];
             
                      //Load upload library
                      $this->load->library('upload',$config); 
                      if($this->upload->do_upload('file')){
                        // Get data about the file
                        $uploadData = $this->upload->data();
                        $filename = $uploadData['file_name'];


                        // Initialize array
                        $data['image'] = $filename;
                        $this->Common->save('hotel_image', $data);
                      }
                }

        }

            //$file_name = $this->do_upload('image[]', 'public/uploads/test/');
            //var_dump($file_name);
           // if ($file_name != '') {
            //    $data['image'] = $file_name;
                //$old_image = $this->Custom->get_old_image('st_stores', $id, 'image');
                //unlink('public/assets/store/' . $old_image[0]->image);
            //}
           
           
           echo json_encode(["stat"=>"success","msg"=>"Hotel Images added Successfully","data"=>$data]);
    }
    
}
