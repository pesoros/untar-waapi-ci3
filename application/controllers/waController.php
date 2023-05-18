<?php
defined('BASEPATH') or exit('No direct script access allowed');

class waController extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->checkToken();
        $this->load->model('WaModel');
    }

    public function refreshToken($phoneName)
    {
        $updateToken = $this->generateToken($phoneName);

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

        $requestNewToken = $this->curlPostRequest('users/login', $postData);

        $data = $requestNewToken['result'];
        $saveData['token'] = $data['access_token'];
        // $saveData['token_expires_in'] = $data['expires_in'];
        // $saveData['token_updated_at'] = date("Y-m-d H:i:s");

        $updateToken = $this->WaModel->rewriteToken($phoneName, $saveData);

        return $updateToken;
    }

    public function single($phoneName, $number, $message)
    {
        $postData["to"] = $number;
        $postData["recipient_type"] = "individual";
        $postData["type"] = "text";
        $postData["text"]['body'] = $message;
        $postData["preview_url"] = false;

        $res = $this->curlPostRequest('messages', $postData, $phoneName);

        $this->response([
            'success' => true,
            'message' => 'sending message success',
            'data' => $res,
        ], 200);
    }

    public function bulkSending($flag)
    {
        $getBulkData = $this->WaModel->getBulkData($flag);
        $test = [];
        foreach ($getBulkData as $key => $value) {
            $bodyVariable = explode("|", $value->isi_variable);
            $postData = [];

            $postData['to'] = $value->no_hp;
            $postData['type'] = 'template';
            $postData['template']['name'] = $value->nama_template;
            $postData['template']['language']['policy'] = 'deterministic';
            $postData['template']['language']['code'] = 'id';
            $postData['template']['components'][0]['type'] = 'body';
            foreach ($bodyVariable as $varKey => $varValue) {
                $postData['template']['components'][0]['parameters'][$varKey]['type'] = 'text';
                $postData['template']['components'][0]['parameters'][$varKey]['text'] = STRVAL($varValue);
            }
            $requestMessage = $this->curlPostRequest('messages', $postData, $value->phone_sender_name);
            
            if ($requestMessage['statusCode'] === 401) {
                $updateToken = $this->generateToken($phone_sender_name);
                $requestMessage = $this->curlPostRequest('messages', $postData, $value->phone_sender_name);  
            }
            
            $saveData['status_code'] = $requestMessage['statusCode'];
            if ($requestMessage['statusCode'] === 201) {
                $saveData['message_id'] = $requestMessage['result']['messages'][0]['id'];
            } else if ($requestMessage['statusCode'] === 400) {
                $saveData['error'] = $requestMessage['result']['errors'][0]['code'];
                $saveData['message'] = $requestMessage['result']['errors'][0]['details'];
            }

            $updateBulkData = $this->WaModel->updateBulkData($value->recid, $saveData);

            if (($key + 1) === COUNT($getBulkData)) {
                sleep(1);
            }
        }

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
        $expiredDate = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s") . " +$minuteExpired minutes"));

        $dataToInsert['no_hp'] = $phoneNumber;
        $dataToInsert['otp'] = $otp;
        $dataToInsert['jenis'] = $category;
        $dataToInsert['created_at'] = $dateNow;
        $dataToInsert['expired_at'] = $expiredDate;
        $dataToInsert['status_otp'] = '0';

        $postData['to'] = $phoneNumber;
        $postData['type'] = 'template';
        $postData['template']['name'] = 'lintar_otp';
        $postData['template']['language']['policy'] = 'deterministic';
        $postData['template']['language']['code'] = 'id';
        $postData['template']['components'][0]['type'] = 'body';
        $postData['template']['components'][0]['parameters'][0]['type'] = 'text';
        $postData['template']['components'][0]['parameters'][0]['text'] = STRVAL($otp);

        $requestNewToken = $this->curlPostRequest('messages', $postData, 'phone_a');

        $saveOtp = $this->WaModel->saveOtp($dataToInsert);

        $this->response([
            'success' => true,
            'message' => 'sending otp success',
            'data' => $requestNewToken,
        ], 200);
    }

    public function singleTemplate()
    {
        $parameters = $this->input->get();

        if (
            !isset($parameters['nama_nomor']) 
            OR !isset($parameters['template'])
            OR !isset($parameters['nomor_tujuan']) 
            OR !isset($parameters['variable'])
        ) {
            $this->response([
                'success' => false,
                'message' => 'parameter tidak lengkap',
            ], 400);
            return;
        }

        $postData['to'] = $parameters['nomor_tujuan'];
        $postData['type'] = 'template';
        $postData['template']['name'] = $parameters['template'];
        $postData['template']['language']['policy'] = 'deterministic';
        $postData['template']['language']['code'] = 'id';
        $postData['template']['components'][0]['type'] = 'body';
        $bodyVariable = explode("|", $parameters['variable']);
        foreach ($bodyVariable as $key => $value) {
            $postData['template']['components'][0]['parameters'][$key]['type'] = 'text';
            $postData['template']['components'][0]['parameters'][$key]['text'] = STRVAL($value);
        }

        $requestNewToken = $this->curlPostRequest('messages', $postData, $parameters['nama_nomor']);

        $this->response([
            'success' => true,
            'message' => 'sending message success',
            'data' => $requestNewToken,
        ], 200);
    }

    public function curlPostRequest($endPoint, $postData, $phoneName = null)
    {
        $url = getenv('WAAPI_URL').'/'.$endPoint;
        $header[] = 'Content-Type: application/json';
        if ($phoneName) {
            $getToken = $this->WaModel->getToken($phoneName);
            $header[] = 'Authorization: Bearer '.$getToken->token;
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
