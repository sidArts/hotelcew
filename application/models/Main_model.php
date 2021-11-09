<?php

class Main_model extends CI_Model
{
  public function ride_history_list($array, $id)
  {

    if (!empty($id)) {
      $this->db->where('id', $id);
      $this->db->update('st_stores', $array);
      return true;
    } else {
      return false;
    }
  }
}
