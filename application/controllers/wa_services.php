<?php
defined('BASEPATH') or exit('No direct script access allowed');

class wa_services extends REST_Controller
{   
	public function __construct() {
        parent::__construct();
		// $this->checkToken();
        $this->load->model('WaModel');
    }

    public function refreshToken($phoneName)
    {
        $res = $this->generateToken($phoneName);
        $data = $res['result'];
        
        $updateToken = $this->WaModel->rewriteToken($phoneName, $data['access_token'], $data['expires_in']);

        $this->response([
            'success' => true,
            'message' => 'refresh token success',
            'data' => $updateToken,
        ], 200);
    }

    public function generateToken($phoneName)
    {
        $getAuthData = $this->WaModel->getAuth($phoneName);
        $postData["username"] = $getAuthData->username;
        $postData["password"] = $getAuthData->password;

        $requestNewToken = $this->curlPostRequest('users/login',$postData);

        return $requestNewToken;
    }

    public function single($phoneName, $number, $message)
    {
        $postData["to"] = $number;
        $postData["recipient_type"] = "individual";
        $postData["type"] = "text";
        $postData["text"]['body'] = $message;
        $postData["preview_url"] = false;
        $getToken = $this->WaModel->getToken($phoneName);

        $res = $this->curlPostRequest('messages',$postData, $getToken->token);

        $this->response([
            'success' => true,
            'message' => 'sending message success',
            'data' => $res,
        ], 200);
    }

    public function curlPostRequest($endPoint, $postData, $token = null)
    {
        $url = getenv('WAAPI_URL').'/'.$endPoint;
        $header[] = 'Content-Type: application/json';
        if ($token) {
            $header[] = 'Authorization: Bearer '.$token;
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $result = json_decode($result, true);

        $res['statusCode'] = $statusCode;
        $res['result'] = $result;
        return $res;
    }
}
