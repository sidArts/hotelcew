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
		if(!isset($request) || !isset($request['status_id']) || !isset($request['booking_id']) || !isset($request['comments']))
			return $this->output->set_status_header(500);
		$this->db->insert('booking_detail_history', $request);
		$this->db->update('st_bookings', [
			'id' => $request['booking_id'],
			'status' => $request['status_id']
		]);
		return $this->output->set_status_header(200);
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