<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class wa_services extends REST_Controller {
	public function single()
	{
		$error = [];
		if( !$this->getPost('phone')) $error[] = 'phone must be filled';
        if( !$this->getPost('message')) $error[] = 'message must be filled';

		if( count($error) > 0 )
        {
			$this->response([
				'success' 	=> false, 
				'message' 	=> $error ,
			], 400);
        }

		$this->response([
			'success' 	=> true, 
			'message' 	=> 'sending message success' ,
		], 200);
	}
}
