<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class welcome extends REST_Controller {
	public function index()
	{
		$result = [];
		$this->response([
			'success' 	=> true, 
			'message' 	=> 'Welcome to UNTAR API' ,
			'data'		=> $result
		], 200);
	}
}
