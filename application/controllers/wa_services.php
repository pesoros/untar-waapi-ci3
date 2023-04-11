<?php
defined('BASEPATH') or exit('No direct script access allowed');

class wa_services extends REST_Controller
{
	public function __construct() {
        parent::__construct();
		$this->checkToken();
    }

    public function generateToken()
    {
        $postData["username"] = "use";
        $postData["password"] = "pas";

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

        $res = $this->curlPostRequest('messages',$postData);

        $this->response([
            'success' => true,
            'message' => 'sending message success',
            'data' => $res,
        ], 200);
    }

    public function curlPostRequest($endPoint, $postData)
    {
        $watoken = 'mwemfooepfwpfpofmo22fmp2o3f2pfmofpo3fmpp23kdfpok3';
        $baseUrl = 'https://api-whatsapp.kata.ai/v1';
        $url = $baseUrl.'/'.$endPoint;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: '.$watoken,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, true);
        return $result;
    }

    public function curlPostFormDataRequest($endPoint, $postData)
    {
        $watoken = 'mwemfooepfwpfpofmo22fmp2o3f2pfmofpo3fmpp23kdfpok3';
        $baseUrl = 'https://api-whatsapp.kata.ai/v1';
        $url = $baseUrl.'/'.$endPoint;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data',
            'Authorization: '.$watoken,
        ));
        $result = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($result, true);
        return $result;
    }
}
