<?php

class WaModel extends CI_Model
{
  public function getAuth($phoneName)
  {
    $query = $this->db->query("
        SELECT 
        wp.name as phone_name,
        auth.username,
        auth.password
        FROM wa_phone as wp
        INNER JOIN wa_phone_auth as auth
        WHERE wp.name = '$phoneName'
    ");
    return $query->row();
  }

  public function getToken($phoneName)
  {
    $query = $this->db->query("
        SELECT 
        wp.name as phone_name,
        wp.token
        FROM wa_phone as wp
        WHERE wp.name = '$phoneName'
    ");
    return $query->row();
  }

  public function rewriteToken($phoneName, $data)
  {
    $this->db->where('name', $phoneName);
    $this->db->update('wa_phone', $data);

    return true;
  }

  public function getBulkData($flag)
  {
    $query = $this->db->query("
        SELECT 
        flag_id,
        nim,
        phone_number,
        variable,
        sent_at,
        template
        FROM wa_bulk
        WHERE flag_id = '$flag'
        AND status_code IS NULL
    ");
    return $query->result();
  }

  public function saveOtp($data)
  {
    $this->db->insert('otp',$data);

    return true;
  }
}