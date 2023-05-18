<?php

class WaModel extends CI_Model
{
  public function getAuth($phoneName)
  {
    $query = $this->db->query("
        SELECT 
        wp.nama_nomor as phone_name,
        auth.username,
        auth.password
        FROM wa_phone as wp
        INNER JOIN wa_phone_auth as auth ON auth.wa_phone_recid = wp.recid
        WHERE wp.name = '$phoneName'
    ");
    return $query->row();
  }

  public function getToken($phoneName)
  {
    $query = $this->db->query("
        SELECT 
        wp.nama_nomor as phone_name,
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
        recid,
        flag_id,
        nim,
        nama_template,
        no_hp,
        isi_variable,
        kirim_at,
        no_sender
        FROM wa_bulk
        WHERE flag_id = '$flag'
        AND status_code IS NULL
    ");
    return $query->result();
  }

  public function updateBulkData($recid, $data)
  {
    $this->db->where('recid', $recid);
    $this->db->update('wa_bulk', $data);

    return true;
  }

  public function saveOtp($data)
  {
    $this->db->insert('otp',$data);

    return true;
  }
}