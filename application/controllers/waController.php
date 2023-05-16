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

        $res = $this->curlPostRequest('messages',$postData, $phoneName);

        $this->response([
            'success' => true,
            'message' => 'sending message success',
            'data' => $res,
        ], 200);
    }

    public function bulkSendingFile($flag)
    {
        $getBulkData = $this->WaModel->getBulkData($flag);
        $exportCsv = $this->exportCsv($flag, $getBulkData);
        $res['total_user'] = COUNT($getBulkData);
        $res['filename'] = $exportCsv;
        $res['fileurl'] = base_url(getenv('BULK_DIRECTORY').'/'.$exportCsv);

        $this->response([
            'success' => true,
            'message' => 'bulk sending message success',
            'data' => $res,
        ], 200);
    }

    public function bulkSending($flag)
    {
        $getBulkData = $this->WaModel->getBulkData($flag);
        $test = [];
        foreach ($getBulkData as $key => $value) {
            $bodyVariable = explode("|",$value->variable);
            $postData = [];

            $postData['to'] = $value->phone_number;
            $postData['type'] = 'template';
            $postData['template']['name'] = $value->template;
            $postData['template']['language']['policy'] = 'deterministic';
            $postData['template']['language']['code'] = 'id';
            $postData['template']['components'][0]['type'] = 'body';
            foreach ($bodyVariable as $varKey => $varValue) {
                $postData['template']['components'][0]['parameters'][$varKey]['type'] = 'text';
                $postData['template']['components'][0]['parameters'][$varKey]['text'] = STRVAL($varValue);
            }
            $requestMessage = $this->curlPostRequest('messages', $postData, $value->phone_sender_name);
            if (($key+1) === COUNT($getBulkData)) {
                sleep(2);
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
        $expiredDate = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +$minuteExpired minutes"));

        $dataToInsert['category'] = $category;
        $dataToInsert['phone_number'] = $phoneNumber;
        $dataToInsert['code'] = $otp;
        $dataToInsert['status'] = 'WAITING';
        $dataToInsert['created_at'] = $dateNow;
        $dataToInsert['expired_at'] = $expiredDate;

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
            $getToken = $this->WaModel->getToken($token);
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

    public function getAlphabet($index)
    {
        $alphabet[0] = 'A';
        $alphabet[1] = 'B';
        $alphabet[2] = 'C';
        $alphabet[3] = 'D';
        $alphabet[4] = 'E';
        $alphabet[5] = 'F';
        $alphabet[6] = 'G';
        $alphabet[7] = 'H';
        $alphabet[8] = 'I';
        $alphabet[9] = 'J';
        $alphabet[10] = 'K';
        $alphabet[11] = 'L';
        $alphabet[12] = 'M';
        $alphabet[13] = 'N';
        $alphabet[14] = 'O';
        $alphabet[16] = 'P';
        $alphabet[17] = 'Q';
        $alphabet[18] = 'R';
        $alphabet[19] = 'S';
        $alphabet[20] = 'T';
        $alphabet[21] = 'U';
        $alphabet[22] = 'V';
        $alphabet[23] = 'W';
        $alphabet[24] = 'X';
        $alphabet[25] = 'Y';
        $alphabet[26] = 'Z';

        return $alphabet[$index];
    }

    public function exportCsv($flag, $exportCsvData){
        include BASEPATH.'PHPExcel/PHPExcel.php';
        $bodyVariable = explode("|",$exportCsvData[0]->variable);

        $csv = new PHPExcel();
        $csv->setActiveSheetIndex(0)->setCellValue('A1', "phone_number");
        $counter = 0;
        foreach($bodyVariable as $index => $val){
            $counter++;
            $csv->setActiveSheetIndex(0)->setCellValue($this->getAlphabet($counter)."1", "body".($index+1)."_type");
            $counter++;
            $csv->setActiveSheetIndex(0)->setCellValue($this->getAlphabet($counter)."1", "body".($index+1)."_text");
        }
        $numrow = 2;
        foreach($exportCsvData as $value){
            $csv->setActiveSheetIndex(0)->setCellValue('A'.$numrow, $value->phone_number);
            $counterValue = 0;
            $bodyVariableVal = explode("|",$value->variable);
            foreach($bodyVariableVal as $index => $val){
                $counterValue++;
                $csv->setActiveSheetIndex(0)->setCellValue($this->getAlphabet($counterValue).$numrow, 'text');
                $counterValue++;
                $csv->setActiveSheetIndex(0)->setCellValue($this->getAlphabet($counterValue).$numrow, $val);
            }
            $numrow++;
        }
        $write = new PHPExcel_Writer_CSV($csv);
        $dateFileName = date("YmdHis");
        $filename = $flag.'-'.$dateFileName.random_int(100, 999).'.csv';
        $write->save(getenv('BULK_DIRECTORY').'/'.$filename);

        return $filename;
      }
}
