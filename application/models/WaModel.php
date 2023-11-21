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
        FROM wa_list_nomor as wp
        INNER JOIN wa_list_nomor_auth as auth ON auth.nomor_recid = wp.recid
        WHERE wp.nama_nomor = '$phoneName'
    ");
    return $query->row();
  }

  public function getToken($phoneName)
  {
    $query = $this->db->query("
        SELECT 
        wp.nama_nomor as phone_name,
        wp.token
        FROM wa_list_nomor as wp
        WHERE wp.nama_nomor = '$phoneName'
    ");
    return $query->row();
  }

  public function rewriteToken($phoneName, $data)
  {
    $this->db->where('nama_nomor', $phoneName);
    $this->db->update('wa_list_nomor', $data);

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
        isi_variabel,
        kirim_at,
        no_sender
        FROM wa_kirim
        WHERE flag_id = '$flag'
        AND status_code IS NULL
    ");
    return $query->result();
  }

  public function updateBulkData($recid, $data)
  {
    $this->db->where('recid', $recid);
    $this->db->update('wa_kirim', $data);

    return true;
  }

  public function saveOtp($data)
  {
    $this->db->insert('wa_otp',$data);

    return true;
  }
}