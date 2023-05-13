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

  public function rewriteToken($phoneName, $newToken, $expiresIn)
  {
    $data = array(
        'token' => $newToken,
        'token_expires_in' => $expiresIn,
        'token_updated_at' => date("Y-m-d H:i:s")
    );

    $this->db->where('name', $phoneName);
    $this->db->update('wa_phone', $data);

    return true;
  }
}