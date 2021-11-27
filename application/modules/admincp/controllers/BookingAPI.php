<?php 

defined('BASEPATH') OR exit('No direct script access allowed');



class BookingAPI extends MX_Controller {



    private $data;

    public function __construct() {
    	parent::__construct();
    	$this->data = [];
    }

    public function index() {
    	return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode([
            	"message" => "Welcome, to aviani booking business process management..."
            ]));
    }

    public function getChildStatusList($bookingId = 1) {
    	$query = $this->db->query("
    		SELECT bs2.name as name, bs2.id as status_id 
			FROM st_bookings b 
			INNER JOIN booking_status bs ON bs.id = b.status AND b.id = ? 
			INNER JOIN business_process_management bpm ON bpm.status_id = b.status 
			INNER JOIN booking_status bs2 ON bpm.child_id = bs2.id", [$bookingId]);
    	$data = $query->result_array();
    	return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }

    public function setStatus() {
    	$stream_clean = $this->security->xss_clean($this->input->raw_input_stream);
		$request = json_decode($stream_clean, true);
        
		if(!isset($request) || !isset($request['status_id']) || !isset($request['booking_id']))
             return $this->output->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['msg'=>'JSON doesnt have status_id or booking_id properties.']));

        try {
            $this->db->select('booking_start_date, booking_end_date, no_of_room, room_id');
            $this->db->where('id', $request['booking_id']);
            $query = $this->db->get('st_bookings');
            $booking = $query->row_array();

            if($booking['status'] == $request['status_id'])
                return $this->output->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode(['msg'=>'Cannot update booking with the same status more than once.']));

            $roomDetails = $this->db->get_where('hotel', 
                ['id' => $booking['room_id']])->row_array();     

            $this->db->trans_start();
            
            $this->db->insert('booking_detail_history', $request);

            $this->db->reset_query();
            
            $this->db->set('status', $request['status_id']);
            $this->db->where('id', $request['booking_id']);
            $this->db->update('st_bookings');

            $this->db->reset_query();

            if (in_array($request['status_id'], SPECIAL_BOOKING_STATUS_LIST)) {
                
                $startDate = new DateTime($booking['booking_start_date']);
                $endDate = new DateTime($booking['booking_end_date']);

                while ($startDate < $endDate) {
                    $this->Custom->updateHotelRoomAvailability(
                        $startDate->format('Y-m-d'), $roomDetails, (0 - $booking['no_of_room']));
                    $startDate->modify('+1 day');
                }
            }

            $this->db->trans_complete(); 
            return $this->output->set_content_type('application/json')
                                ->set_status_header(200)
                                ->set_output(json_encode(['status'=>'success']));
        } catch (Exception $e) {
            return $this->output->set_content_type('application/json')
                                ->set_status_header(500)
                                ->set_output(json_encode(['msg'=>$e->getMessage()]));
        }
        
    }

    public function getBookingHistory() {
    	$sql = "SELECT * FROM `booking_detail_history` bdh
				INNER JOIN booking_status bs ON bs.id = bdh.status_id";
		$query = $this->db->query($sql);
		$data = $query->result_array();
    	return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode($data));
    }
}

?>