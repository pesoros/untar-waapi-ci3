<?php
defined('BASEPATH') or exit('No direct script access allowed');

class waController extends REST_Controller
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
        $saveData['token'] = $data['access_token'];
        $saveData['token_expires_in'] =  $data['expires_in'];
        $saveData['token_updated_at'] = date("Y-m-d H:i:s");

        $updateToken = $this->WaModel->rewriteToken($phoneName, $saveData);

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

    public function bulkSending($flag)
    {
        $getBulkData = $this->WaModel->getBulkData($flag);

        $this->response([
            'success' => true,
            'message' => 'bulk sending message success',
            'data' => $getBulkData,
        ], 200);
    }

    public function otp($category, $phoneNumber)
    {
        $minuteExpired = getenv('OTP_MINUTE_EXPIRES');
        $otp = random_int(100000, 999999);
        $dateNow = date("Y-m-d H:i:s");
        $expiredDate = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +$minuteExpired minutes"));

        $dataToInsert['category'] = $category;
        $dataToInsert['phone_number'] = $phoneNumber;
        $dataToInsert['code'] = $otp;
        $dataToInsert['status'] = 'WAITING';
        $dataToInsert['created_at'] = $dateNow;
        $dataToInsert['expired_at'] = $expiredDate;

        $saveOtp = $this->WaModel->saveOtp($dataToInsert);

        $this->response([
            'success' => true,
            'message' => 'sending otp success',
            'data' => $dataToInsert,
        ], 200);
    }

    public function singleTemplate()
    {
        $parameters = $this->input->get();

        $this->response([
            'success' => true,
            'message' => 'sending message success',
            'data' => $parameters,
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