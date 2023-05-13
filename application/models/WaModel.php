<?php

class WaModel extends CI_Model
{
  public function tetstDb()
  {
    $query = $this->db->query("SELECT * FROM wa_bulk");
    return $query->result();
  }
}