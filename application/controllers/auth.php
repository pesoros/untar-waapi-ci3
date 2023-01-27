<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class auth extends REST_Controller
{
    public function submitregistration()
    {
        # code...
    }
    
    public function getlogin()
    {
        $exp = time() + 3600;
        $token = array(
            "iss" => 'apprestservice',
            "aud" => 'pengguna',
            "iat" => time(),
            "nbf" => time() + 10,
            "exp" => $exp,
            "data" => array(
                "username" => $this->input->post('username'),
                "password" => $this->input->post('password'),
            ),
        );

        $jwt = JWT::encode($token, $this->configToken()['secretkey'], 'HS256');
        $output = [
            'status' => 200,
            'message' => 'Berhasil login',
            "token" => $jwt,
            "expireAt" => $token['exp'],
        ];
        $data = array('kode' => '200', 'pesan' => 'token', 'data' => array('token' => $jwt, 'exp' => $exp));
        $this->response($data, 200);
    }
}
