<?php

defined('BASEPATH') or exit('No direct script access allowed');



class User extends MX_Controller
{



    private $data;

    public function __construct() {
        parent::__construct();
        $this->layout->set("admin-panel");
        $this->data = [];
    }

    public function list() {
        $breadcrumb = [
            ['page' => "Booking List"]
        ];
        $this->layout->set_breadcumb($breadcrumb);
        $this->layout->set_title("Booking list");
        $this->layout->view('bookings', $this->data);
    }

    public function global_rate($value='')
    {
        if(isset($_POST) && !empty($_POST))
        {
            $global_rate = $_POST['global_rate'];
            if(isset($_POST['_data_id']) & !empty($_POST['_data_id']))
            {
                $this->Common->save(GLOBAL_RATE,array('rate'=>$global_rate),array('id'=>$_POST['_data_id']));
            }
            else
            {
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
            ['page' => "Ride Add"], 
            ['page' => "Ride Update"]
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

    public function ridelist()
    {
        $draw = intval($this->input->get("draw"));
        // $start = intval($this->input->get("start"));
        // $length = intval($this->input->get("length"));
        $this->db->select('*');
        $this->db->from('st_bookings');
        $this->db->where("status !=", "D"); 
        $this->db->order_by('id desc');
        // $this->db->join('st_stores', 'st_stores.id = st_ride.store_id');
        
        $data = array(); 
        $query = $this->db->get()->result();
        foreach ($query as $key => $row) {
            $data[] = array(
                //"<input type='checkbox' class='check' value='" . $row->id . "'/>",
                // $key + 1,
                $row->booking_no,
                $row->customer_name,
                $row->customer_mobile,
                // $row->customer_email,
                $row->booking_start_date,
                $row->booking_end_date,
                $row->booking_date,
                $row->no_of_room,
                // $row->no_of_person,
                number_format($row->total_cost,2),

                ($row->status == 'A') ? "<button onclick='checkout(".$row->id.")' class='btn btn-info' title='Checkout'>Checkout</button>" : (($row->status == 'B') ? 'Cancelled' : 'Completed').'<br>'.date('F j, Y, g:i a',$row->checkout_time),
                "<button class='btn btn-info' title='Details' onclick='booking_details(".$row->id.")'><i class='fa fa-eye'></i></button> &nbsp;&nbsp;".
                ((($row->status === 'A') ? ("<button type='button' class='btn btn-danger' id='del-" . $row->id . "' onclick='cancelBooking(" . $row->id . ")'><i class='fa fa-ban'></i></button>") : ''))

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
    public function delete_ride()
    {

        $id = $this->input->post('id');
        $data = $this->Custom->update_rows('st_bookings', array('status' => 'D'), $id);
        return $data;
    }
     public function checkout($value='')
     {
        $id = $this->input->post('id');
        $data = $this->Custom->update_rows('st_bookings', array('status' => 'C','checkout_time'=>time()), $id);
        return $data;
     }

     public function cancelBooking() {
        $id = $this->input->post('id');
        $description = $this->input->post('description');
        $data = $this->Custom->update_rows('st_bookings', ['status' => 'B', 'cancel_reasons' => $description], $id);
        return $data;
     }

     public function booking_details($value='')
     {
        $id = $this->input->post('id');
        $data = $this->Custom->booking_details($id);
        // var_dump($data);
        // exit;
        ?>
        <table class="table table-bordered">
            <tbody>

                <tr>
                    <td>Booking no</td>
                    <td><?= $data['booking_no']?></td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><?= $data['customer_name']?></td>
                </tr>
                <tr>
                    <td>Mobile</td>
                    <td><?= $data['customer_mobile']?></td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td><?= $data['customer_email']?></td>
                </tr>
                <tr>
                    <td>Check In</td>
                    <td><?= $data['booking_start_date']?></td>
                </tr>
                <tr>
                    <td>Checkout</td>
                    <td><?= $data['booking_end_date']?></td>
                </tr>
                <tr>
                    <td>Room</td>
                    <td><?= $data['name']?></td>
                </tr>
                <tr>
                    <td>No of room</td>
                    <td><?= $data['no_of_room']?></td>
                </tr>
                <tr>
                    <td>Total cost</td>
                    <td><?= $data['total_cost']?></td>
                </tr>
                <tr>
                    <td>Booking date</td>
                    <td><?= $data['booking_date']?></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td><?= ($data['status'] == 'C') ? 'Completed' : (($data['status'] == 'B') ? 'Cancelled' : 'Active') ?></td>
                </tr>

                <?php if($data['status'] === 'B') { ?>
                <tr>
                    <td>Cancellation Reasons</td>
                    <td><?=$data['cancel_reasons'] ?></td>
                </tr>
                <?php } ?>
                <?php
                if($data['status'] == 'C')
                {
                    ?>
                    <tr>
                        <td>Checkout time</td>
                        <td><?= date('F j, Y, g:i a',$data['checkout_time'])?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr>
                    <td>Created at</td>
                    <td><?= $data['created']?></td>
                </tr>
            </tbody>
        </table>
        <?php
         # code...
     }
}
