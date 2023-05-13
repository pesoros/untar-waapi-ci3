<?php
defined('BASEPATH') or exit('No direct script access allowed');

class wa_services extends REST_Controller
{   
    var $baseUrl;
	public function __construct() {
        parent::__construct();
		// $this->checkToken();
        $this->load->model('WaModel');
        $this->baseUrl = getenv('WAAPI_URL');
    }

    public function testdbloc()
    {
        $res = $this->WaModel->tetstDb();

        $this->response([
            'success' => true,
            'message' => 'refresh token success',
            'data' => $res,
        ], 200);
    }

    public function refreshToken()
    {
        $res = $this->generateToken();

        $this->response([
            'success' => true,
            'message' => 'refresh token success',
            'data' => $res,
        ], 200);
    }

    public function generateToken()
    {
        $postData["username"] = "untar-791d7b98";
        $postData["password"] = "9wtSrUMvLExdsHhG";

        $res = $this->curlPostRequest('users/login',$postData);

        return $res;
    }

    public function single()
    {
        $error = [];
        if (!$this->getPost('phone')) {
            $error[] = 'phone must be filled';
        }

        if (!$this->getPost('message')) {
            $error[] = 'message must be filled';
        }

        if (count($error) > 0) {
            $this->response([
                'success' => false,
                'message' => $error,
            ], 400);
        }

        $postData["to"] = "628123456789";
        $postData["recipient_type"] = "individual";
        $postData["type"] = "text";
        $postData["text"]['body'] = "some text to be sent";
        $postData["preview_url"] = false;

        $res = $this->curlPostRequest('messages',$postData, $this->waToken);

        $this->response([
            'success' => true,
            'message' => 'sending message success',
            'data' => $res,
        ], 200);
    }

    public function curlPostRequest($endPoint, $postData, $token = null)
    {
        $url = $this->baseUrl.'/'.$endPoint;
        $header[] = 'Content-Type: application/json';
        if ($token) {
            $header[] = 'Authorization: '.$token;
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, true);
        return $result;
    }
}
