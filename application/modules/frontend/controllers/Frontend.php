<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Frontend extends MX_Controller

{

    private $data;

    public function __construct()
    {

        parent::__construct();

        $this->layout->set("admin-panel");

        $this->data = [];

    }

    public function index($value='')

    {

        $rooms = $this->Custom->get_rooms();

        $this->data['rooms'] = $rooms;

        $about = $this->Custom->get_page_content('about-us');
        $this->data['about'] = $about['content']; 

        $this->load->view('template/header');

        $this->load->view('template/home', $this->data);

        $this->load->view('template/footer');

    }



    public function contactUs($value='')

    {
        $content = $this->Custom->get_page_content('contact-us');
        $this->data['content'] = $content['content']; 
        $this->data['site_details'] = $this->Common->findById('site_settings', 1);
        $this->load->view('template/header');

        $this->load->view('template/contact', $this->data);

        $this->load->view('template/footer');

    }



    public function rates($value='')

    {

        $rooms = $this->Custom->get_rooms();

        $this->data['rooms'] = $rooms;

        $this->load->view('template/header');

        $this->load->view('template/rates', $this->data);

        $this->load->view('template/footer');

    }



    public function gallery($value='')

    {

        $query = $this->db->query("SELECT * FROM gallery WHERE status='A'");
        $this->data['gallery'] = $query->result_array();

        $this->load->view('template/header');

        $this->load->view('template/gallery', $this->data);

        $this->load->view('template/footer');

    }



    public function thankyou($value='')

    {

        # code...

        $this->load->view('template/header');

        $this->load->view('template/thank_you', $this->data);

        $this->load->view('template/footer');

    }





    public function roomDetails($slug='')

    {

        $roomdetails = $this->Custom->room_details($slug);

        $this->data['roomdetails'] = $roomdetails;



        $rooms = $this->Custom->get_rooms();
        $room_images = $this->Custom->get_room_images($roomdetails['id']);

        $this->data['rooms'] = $rooms;
        $this->data['room_images'] = $room_images;



        $this->data['available_room'] = $this->Custom->get_available_room($roomdetails['id']);

        // var_dump($roomdetails);

        // exit;

        $this->load->view('template/header');

        $this->load->view('template/room_details', $this->data);

        $this->load->view('template/footer');

    }



    public function createBooking($value='') {

        if(isset($_POST)) {
            $roomdetails = $this->Custom->room_details_by_id($_POST['room_id']);

            $back_rate = $roomdetails['back_rate'];
            $gst = $roomdetails['gst']*$_POST['no_of_room'];
            //$capacity = $roomdetails['person'];
            //$booked_person = $_POST['person'];
            $booking_no = $this->Custom->get_booking_no();
            $start_date = str_replace('/', '-', $_POST['booking_start_date']);
            $end_date = str_replace('/', '-', $_POST['booking_end_date']);

            $start_date = date('Y-m-d',strtotime($start_date));
            $end_date = date('Y-m-d',strtotime($end_date));

            $diff = strtotime($start_date) - strtotime($end_date);
     
            $days = ceil(abs($diff / 86400));
            $total_cost = ($back_rate*$_POST['no_of_room']*$days) + $gst;
            //exit;

            $bookingdata = array(

                'room_id'=>$_POST['room_id'],

                'customer_name'=>$_POST['name'],

                'customer_mobile'=>$_POST['phone'],

                'customer_email'=>$_POST['email'],

                'booking_no'=>$booking_no,

                'booking_start_date'=>$_POST['booking_start_date'],

                'booking_end_date'=>$_POST['booking_end_date'],

                'booking_date'=>date('Y-m-d'),

                'no_of_room'=>$_POST['no_of_room'],

                'addition_cost'=>0,

                'gst'=>$gst,
                
                'total_cost'=>$total_cost,

            );

            if($this->db->insert('st_bookings',$bookingdata)) {
                $startDate = new DateTime($_POST['booking_start_date']);
                $endDate = new DateTime($_POST['booking_end_date']);

                while ($startDate < $endDate) {
                    $this->Custom->updateHotelRoomAvailability(
                        $startDate, $roomdetails, $bookingdata['no_of_room']);
                    $startDate->modify('+1 day');
                }
                
                // $to = $_POST['email'];
                // $subject = 'Hotel Aviana | Booking success';
                // $body = '<h3>YOUR BOOKING HAS BEEN SUCCESSFULLY DONE.</h3>';
                // $body .= '<p>Our support team will contact you soon.</p>';
                // $this->Custom->send_aviana_email($to,$subject,$body);

                //get room info
                // $room_details = $this->Custom->room_details_by_id($_POST['room_id']);

                $to_admin = $_POST['email'];
                $subject_admin = 'Hotel Aviana | New Booking created';
                $body_admin = '<h3>NEW BOOKING CREATED</h3>';
                $body_admin .= '<p>Booking no: '.$booking_no.'</p>';
                $body_admin .= '<p>Name: '.$_POST['name'].'</p>';
                $body_admin .= '<p>Email: '.$_POST['email'].'</p>';
                $body_admin .= '<p>Phone: '.$_POST['phone'].'</p>';
                $body_admin .= '<p>Room: '.$roomdetails['name'].'</p>';
                $body_admin .= '<p>Checkin: '.$_POST['booking_start_date'].'</p>';
                $body_admin .= '<p>Checkout: '.$_POST['booking_end_date'].'</p>';
                $body_admin .= '<p>No of room: '.$_POST['no_of_room'].'</p>';
                $body_admin .= '<p>Date: '.date('Y-m-d').'</p>';
                $body_admin .= '<p>Total cost: '.$total_cost.'</p>';
                $this->Custom->send_aviana_email($to_admin,$subject_admin,$body_admin);
                $this->Custom->send_aviana_email(DOMAIN_MAIL,$subject_admin,$body_admin);
                //SEND ADMIN EMAIL

                redirect('thank-you');

            }
        }
    }





    public function list() {
        $breadcrumb = [[ 'page' => "Booking List"]];
        $this->load->view('template/home', $this->data);
    }

    public function global_rate($value='') {

        if(isset($_POST) && !empty($_POST)) {

            $global_rate = $_POST['global_rate'];
            if(isset($_POST['_data_id']) & !empty($_POST['_data_id'])) {

                $this->Common->save(GLOBAL_RATE,array('rate'=>$global_rate),array('id'=>$_POST['_data_id']));
            } else {
                $this->Common->save(GLOBAL_RATE,array('rate'=>$global_rate,'status'=>'A'));
            }
        }

        

        $breadcrumb = [



            [



                'page' => "Ride global rate",



            ], [



                'page' => "Ride global rate",

            ]

        ];



        $this->data['global_rate'] = $this->Common->find(GLOBAL_RATE);



        $this->layout->set_breadcumb($breadcrumb);

        $this->layout->set_title("Ride global rate");

        $this->layout->view('admin/global-rate', $this->data);

    }

    public function create_ride()

    {

        $breadcrumb = [



            [



                'page' => "Ride Add",



            ], [



                'page' => "Ride Update",

            ]



        ];

        $id = $this->uri->segment(4);

        $this->data['stores'] = $this->Common->findAll('st_stores');

        $get_id = decrypt($id);

        if (!empty($id)) {

            $this->data['ride_details'] = $this->Common->findById('st_ride', $get_id);

            $this->data['time_slots'] = $this->Custom->gettimeslots($this->data['ride_details']['id']);

            $this->layout->set_breadcumb($breadcrumb);

            $this->layout->set_title("User Upadate");

            $this->layout->view('admin/ride-add', $this->data);

        } else {

            $this->layout->set_breadcumb($breadcrumb);

            $this->layout->set_title("User Add");

            $this->layout->view('admin/ride-add', $this->data);

        }

    }

    public function save_ride()

    {



        $id  = $this->input->post('id');

        if (empty($id)) {

            $this->load->library('form_validation');

            $this->form_validation->set_rules('store_name', 'store_name', 'required');

            $this->form_validation->set_rules('ridename', 'ridename', 'required');

            $this->form_validation->set_rules('description', 'description', 'required');

            $this->form_validation->set_rules('extra_const', 'extra_const', 'required');

            $this->form_validation->set_rules('status', 'status', 'required');

            $this->form_validation->set_rules('ridebase[]', "ridebase", "required");

            $this->form_validation->set_rules('ride_base_charges[]', "ride_base_charges", "required");

            $this->form_validation->set_rules('timestatus[]', "timestatus", "required");

            if ($this->form_validation->run() == FALSE) {



                echo json_encode(["stat" => "error", "msg" => "Somthing missed in Ride form"]);

            } else {

                $data['store_id'] = $this->input->post('store_name');

                $data['ride_name'] = $this->input->post('ridename');

                $data['ride_min_time'] = $this->input->post('time');

                $data['ride_base_price'] = $this->input->post('ride_base');

                $data['ride_desc'] = $this->input->post('description');

                $data['ride_img'] = $this->do_upload('image', 'public/uploads/ride/');

                $data['extra_per_min_cost'] = $this->input->post('extra_const');

                $data['status'] = $this->input->post('status');

                $this->Common->save('st_ride', $data);

                $last_ride_id = $this->db->insert_id();

                $ridebase = $this->input->post('ridebase');

                $ride_base_charges = $this->input->post('ride_base_charges');

                $timestatus = $this->input->post('timestatus');

                foreach ($ridebase as $key => $val) {

                    $slot['ride_id'] = $last_ride_id;

                    $slot['ride_base_time'] = $ridebase[$key];

                    $slot['ride_base_charge'] = $ride_base_charges[$key];

                    $slot['status'] = $timestatus[$key];

                    $this->Common->save('st_ride_time_slot', $slot);

                }





                echo json_encode(["stat" => "success", "msg" => "Ride added Successfully", "error" => (object)[]]);

            }

        } else {

            $data['store_id'] = $this->input->post('store_name');

            $data['ride_name'] = $this->input->post('ridename');

            $data['ride_min_time'] = $this->input->post('time');

            $data['ride_base_price'] = $this->input->post('ride_base');

            $data['ride_desc'] = $this->input->post('description');

            $file_name = $this->do_upload('image', 'public/uploads/ride/');

            if ($file_name != '') {

                $data['ride_img'] = $file_name;

                $old_image = $this->Custom->get_old_image('st_ride', $id, 'ride_img');

                @unlink('public/uploads/ride/' . $old_image[0]->ride_img);

            }

            // $data['ride_img'] = $this->do_upload("image", 'public/uploads/ride/');

            $data['extra_per_min_cost'] = $this->input->post('extra_const');

            $data['status'] = $this->input->post('status');

            $this->Custom->update_by_table('st_ride', $data, $id);

            $count_new = $this->input->post('multiple');

            if (!empty($count_new)) {

                $ridebase = $this->input->post('ridebase');

                $ride_base_charges = $this->input->post('ride_base_charges');

                $timestatus = $this->input->post('timestatus');





                foreach ($ridebase as $key => $value) {

                    $slot_id[$key] = $this->Custom->get_slot_id($id);

                    $check = $this->Custom->findupdateorinsert(@$slot_id[0][$key]->id, $count_new);

                    if ($check == 0) {



                        $slot['ride_id'] = $id;

                        $slot['ride_base_time'] = $ridebase[$key];

                        $slot['ride_base_charge'] = $ride_base_charges[$key];

                        $slot['status'] = $timestatus[$key];

                        $this->Common->save('st_ride_time_slot', $slot);

                    }

                }

            }



            echo json_encode(["stat" => "success", "msg" => "User updated successfully", "error" => (object)[]]);

        }

    }



    public function do_upload($image, $path)

    {



        $config['upload_path']          = $path;

        $config['allowed_types']        = 'gif|jpg|jpeg|png';

       // $config['max_size']             = 1024;

        //$config['max_width']            = 1024;

      //  $config['max_height']           = 768;

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



    public function ridelist() {

        $draw = intval($this->input->get("draw"));

        // $start = intval($this->input->get("start"));

        // $length = intval($this->input->get("length"));

        $this->db->select('*');

        $this->db->from('st_bookings');

        // $this->db->join('st_stores', 'st_stores.id = st_ride.store_id');

        // $this->db->where("st_ride.status !=", "D");  

        $query = $this->db->get()->result();

        foreach ($query as $key => $row) {

            $data[] = array(

                "<input type='checkbox' class='check' value='" . $row->id . "'/>",

                // $key + 1,

                $row->booking_no,

                $row->customer_name,

                $row->customer_mobile,

                $row->customer_email,

                $row->booking_start_date,

                $row->booking_end_date,

                $row->booking_date,

                $row->no_of_room,

                $row->no_of_person,

                $row->total_cost,

                ($row->status == 'A') ? 'Active' : 'Checkout',

                "<a href='" . ADMIN_URL . 'rooms/update/' . $row->id . "' class='btn btn-info' title='Checkout'><i class='fa fa-check'></i></a>"

                // (!empty($row->ride_img)) ? "<img src='" . base_url() . '/public/uploads/ride/' . $row->ride_img . "' height='50px' width='50px'/>" : "<img src='https://previews.123rf.com/images/pavelstasevich/pavelstasevich1811/pavelstasevich181101065/112815953-no-image-available-icon-flat-vector.jpg' height='50px' width='50px'>",

                // $row->ride_min_time . "/" . "Minute",

                // $row->ride_base_price,

                // $row->extra_per_min_cost,

                // ($row->status == 'A') ? "<img alt='Accept, active, agree, approved, check, checkmark, confirmed, correct,  done, good, ok, success, valid, validation, verify icon' class='n3VNCb' src='https://www.iconfinder.com/data/icons/free-basic-icon-set-2/300/11-512.png' jsname='HiaYvf' jsaction='load:XAeZkd;' data-iml='6012.000000000626' style='width: 20px; height: 20px; margin: 0px;'>" : "<img alt='Accept, active, agree, approved, check, checkmark, confirmed, correct,  done, good, ok, success, valid, validation, verify icon' class='n3VNCb' src='https://w7.pngwing.com/pngs/41/41/png-transparent-red-x-button-icon-error-http-404-icon-red-cross-mark-file-miscellaneous-trademark-heart.png' jsname='HiaYvf' jsaction='load:XAeZkd;' data-iml='6012.000000000626' style='width: 20px; height: 20px; margin: 0px;'>",

                // "<a href='" . ADMIN_URL . 'ride/update/'. encrypt($row->id) . "' class='btn btn-info'><i class='fa fa-edit'></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button type='button' class='btn btn-danger' id='del-" . $row->id . "' onclick='deleteusers(" . $row->id . ")'><i class='fa fa-trash'></i></button>",



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

    public function insert_subscriber($value='')
    {
        if(isset($_POST))
        {
            $email = $_POST['email'];
            $to = $email;
            $subject = 'Hotel Aviana | Subcription success';
            $body = '<h3>YOUR SUBSCRIPTION HAS BEEN SUCCESSFULLY DONE.</h3>';
            $body .= '<p>WILL BE THE FIRST TO HEAR ABOUT LOCAL EVENTS AND NEW AMENITIES.</p>';
            $this->Custom->send_aviana_email($to,$subject,$body);

            // $to_admin = DOMAIN_MAIL;
            // $subject_admin = 'Hotel Aviana | New Booking created';
            // $body_admin = '<h3>NEW BOOKING CREATED</h3>';
            
            //$this->Custom->send_aviana_email($to,$subject,$body);
            exit;
        }
    }

    public function send_contact_form($value='')
    {
        if(isset($_POST))
        {
            //var_dump($_POST);
            $email = $_POST['email'];
            $to = $email;
            $subject = 'Hotel Aviana | Thank you '.$_POST['name'];
            $body = '<h3>Thank you for your interest.</h3>';
            $body .= '<p>Our support team will contact you soon.</p>';
            $this->Custom->send_aviana_email($to,$subject,$body);

            $to_admin = DOMAIN_MAIL;
            $subject_admin = 'Hotel Aviana | Contact Form';
            $body_admin = '<h3>NEW CONTACT FORM SUBMITTED</h3>';
            $body_admin .= '<p>Name: '.$_POST['name'].'</p>';
            $body_admin .= '<p>Email: '.$_POST['email'].'</p>';
            $body_admin .= '<p>Phone: '.$_POST['phone'].'</p>';
            $body_admin .= '<p>Message: '.$_POST['message'].'</p>';
            
            $this->Custom->send_aviana_email($to_admin,$subject_admin,$body_admin);
            exit;
        }
    }
    

    public function getAllRoomTypeAvailabilityByDateRange($startDate = '', $endDate = '') {
        $data = $this->Custom->getAllRoomTypeAvailabilityByDateRange($startDate, $endDate);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }

    public function getRoomAvailabilityByDateRange($roomType = 1, $startDate = '', $endDate = '') {
        $data = $this->Custom->getAvailabilityByDateRange($roomType, $startDate, $endDate);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }


    public function getRoomRateByDate($slug = '', $startDate = '', $endDate = '') {
        $roomRate = $this->Custom->room_rates_by_date($slug, $startDate, $endDate);
        return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($roomRate));
    }
}

