<?php

class Custom_model extends CI_Model
{
  public function get_rooms($value='')
  {
    $query = $this->db->query("SELECT * FROM hotel WHERE status=1");
    return $result = $query->result_array();
  }

  public function get_site_details($value='')
  {
    $query = $this->db->query("SELECT * FROM site_settings");
    return $result = $query->result_array();
    //var_dump($result);
  }

  public function room_details($slug='')
  {
  	return $room_details   = $this->Common->find([
                'table' => 'hotel',
                'select' =>'*',
                'where' => "slug = '{$slug}'",
                'query' => "first"
            ]);
  	
  }

  public function room_details_by_id($id='')
  {
  	return $room_details   = $this->Common->find([
                'table' => 'hotel',
                'select' =>'*',
                'where' => "id = '{$id}'",
                'query' => "first"
            ]);
  	
  }

  public function get_available_room($room_id='')
  {
  	$query = $this->db->query("SELECT hotel.no_of_room,SUM(st_bookings.no_of_room) AS booked_room FROM `hotel` INNER JOIN st_bookings ON st_bookings.room_id=hotel.id WHERE hotel.id='{$room_id}' AND st_bookings.status='A'");
    $result = $query->result_array();

    return $available_room = $result[0]['no_of_room'] - $result[0]['booked_room'];

    $query = $this->db->get_where(
      'hotel_room_availability_by_date', 
      "date = '" . $date->format('Y-m-d') . "' AND room_type = '" . $roomDetails['id'] . "'");
  }

  public function get_booking_no($value='')
  {
  	$booking_no_max   = $this->Common->find([
                'table' => 'st_bookings',
                'select' =>'COUNT(id) AS counts',
                'query' => "first"
            ]);

  	return 'AVI202100'.($booking_no_max['counts']+1);
  }

  public function update_by_table($table, $array, $id)
  {
    if ($id != "") {
      $this->db->where('id', $id);
      $this->db->update($table, $array);
      return 1;
    } else {
      return 0;
    }
  }

  public function update_rows($table, $array, $id)
  {

    if (!empty($id)) {
      $this->db->where('id', $id);
      $this->db->update($table, $array);
      return true;
    } else {
      return false;
    }
  }

  public function booking_details($id='')
  {
  	return $data_booking = $this->Common->find([
            'table'     => 'st_bookings t1', 
            'select'    => "t1.*,t2.name",
            'join'      => [

                                ['hotel', 't2', 'INNER', "t1.room_id = t2.id"],
                            ],
            'where'     => "t1.id = '{$id}'",
            'query'=>'first',
            ]);
  }

  public function get_page_content($slug='')
  {
    return $data_booking = $this->Common->find([
            'table'     => 'pages', 
            'select'    => "content",
            'where'     => "slug = '{$slug}'",
            'query'=>'first',
            ]);
  }

  public function get_room_images($id='')
  {
    return $room_images = $this->Common->find([
            'table'     => 'hotel_image', 
            'select'    => "*",
            'where'     => "hotel_id = '{$id}' AND status='A'",
            ]);
  }

  public function checkexsitingemail($email)
  {

    $this->db->select('email');
    $this->db->from('st_stores');
    $this->db->where('role', 2);
    $this->db->where('email', $email);
    $query = $this->db->get();
    return $query->row_array();
  }
  public function checkexsiting_user_mail($email)
  {

    $this->db->select('email');
    $this->db->from('st_users');
    $this->db->where('role', 2);
    $this->db->where('email', $email);
    $query = $this->db->get();
    return $query->row_array();
  }

  public function send_aviana_email($to='',$subject='',$body='')
  {
    ob_start();
            ?>
            <div style="max-width: 750px; margin: 0 auto; background-color: #f5f5f5; border: #ddd solid 1px; color: #000;">
                <div style="background-color: #000; text-align: center; padding: 30px;">
                    
                        <img style="height: 100px;" src="http://hotelaviana.com/public/uploads/logo.png" style="width: 200px;">
                        
                </div>
                <div style="background-color: #fff; border: #000 solid 1px; padding: 30px;">
                    <?php echo $body;?>

                    
                   
                    <div style="padding-bottom: 15px;">Questions? If you have any questions, head over to our online chat support, or email us at <a href="mailto:support@hotelaviana.com">support@hotelaviana.com</a></div>
                    
                    
                </div>

                <div style="background-color: #000; color: #fff; text-align: center; padding: 15px; font-size: 13px;">Copyright © 2021 Aviani Infra Projects PVT Ltd. All rights reserved.</div>
            </div>
            <?php 
            $body = ob_get_clean();
            $emailSent = email(DOMAIN_MAIL, $to, $subject, $body);
  }

  public function getAllRoomTypeAvailabilityByDateRange($startDate = '', $endDate = '') {
    $result = [];
    $this->db->select('*');
    $this->db->from('hotel');
    $query = $this->db->get();
    foreach ($query->result_array() as $row) {
      // $this->db->select('*');
      // $this->db->from('hotel_room_availability_by_date');
      // $this->db->where('room_type', $row['id']);
      // $this->db->where("date BETWEEN '$startDate' AND '$endDate'");
      // $this->db->where("available_rooms > 0");
      // $this->db->order_by('available_rooms', 'ASC');
      // $this->db->limit(1);
      // $query = $this->db->get();
      // $res = $query->row_array();
      $res = $this->getAvailabilityByDateRange($row['id'], $startDate, $endDate);
      if(isset($res))
        array_push($result, $res); 
    }
    return $result;
  }

  public function getAvailabilityByDateRange($roomType = 1, $startDate = '', $endDate = '') {
    $this->db->select('available_rooms, name, slug, person');
    $this->db->from('hotel_room_availability_by_date AS hra');
    $this->db->join('hotel AS h', 'h.id = hra.room_type');
    $this->db->where('room_type', $roomType);
    $this->db->where("date BETWEEN '$startDate' AND '$endDate'");
    // $this->db->where("available_rooms > 0");
    $this->db->order_by('available_rooms', 'ASC');
    $this->db->limit(1);
    $query = $this->db->get();
    $res = $query->row_array();
    if(isset($res) && !empty($res))
      return $res;
    else {
      $this->db->select('no_of_room as available_rooms, name, slug, person');
      $this->db->from('hotel');
      $this->db->where('id', $roomType);
      return $this->db->get()->row_array();
    }
  }

  public function updateHotelRoomAvailability($date, $roomDetails) {
    $query = $this->db->get_where(
      'hotel_room_availability_by_date', 
      "date = '" . $date->format('Y-m-d') . "' AND room_type = '" . $roomDetails['id'] . "'");
    $res = $query->row_array();
    if(!isset($res)) {
      $this->db->insert('hotel_room_availability_by_date', [
        'room_type' => $roomDetails['id'],
        'date' => $date->format('Y-m-d'),
        'total_rooms' => (int) $roomDetails['no_of_room'],
        'available_rooms' => ((int)$roomDetails['no_of_room']) - 1
      ]);
    } else {
      $availableRooms = intval($res['available_rooms']) - 1;
      $this->db->set('available_rooms', $availableRooms);
      $this->db->where('date', $date->format('Y-m-d'));
      $this->db->where('room_type', $roomDetails['id']);
      $this->db->update('hotel_room_availability_by_date');
    }    
  }

}
