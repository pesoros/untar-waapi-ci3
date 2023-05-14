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
        $exportCsv = $this->exportCsv($flag, $getBulkData);

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
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$flag.random_int(10000000, 99999999).'.csv"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $write = new PHPExcel_Writer_CSV($csv);
        $write->save('php://output');
      }
}
