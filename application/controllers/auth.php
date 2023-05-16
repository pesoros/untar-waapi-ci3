<?php
defined('BASEPATH') or exit('No direct script access allowed');

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

class auth extends REST_Controller
{
    public function submitregistration()
    {
        $error = [];
        if (!$this->getPost('username')) {
            $error[] = 'username must be filled';
        }

        if (!$this->getPost('password')) {
            $error[] = 'password must be filled';
        }

        if (count($error) > 0) {
            $this->response([
                'success' => false,
                'message' => $error,
            ], 400);
        }
        
        $this->response([
            'success' => true,
            'message' => "registration succeed",
        ], 200);
    }

    public function getlogin()
    {
        $error = [];
        if (!$this->getPost('username')) {
            $error[] = 'username must be filled';
        }

        if (!$this->getPost('password')) {
            $error[] = 'password must be filled';
        }

        if (count($error) > 0) {
            $this->response([
                'success' => false,
                'message' => $error,
            ], 400);
        }

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
            "token" => $jwt,
            "expireAt" => $token['exp'],
        ];
        $this->response([
            'success' => true,
            'message' => "login succeed",
            'data'    => $output
        ], 200);
    }
}
