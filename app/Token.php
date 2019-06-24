<?php

	namespace App;

	class Token 
	{

		public $token;

		public function __construct($token=null) {

			if(is_null($token)) {

				$this->token=bin2hex(openssl_random_pseudo_bytes(16));
			}
			else {

				$this->token=$token;	
			}
		}

		public function getToken() {

			return $this->token;
		}

		public function getHash() {

			return hash_hmac('sha256', $this->token, env('TOKEN_SECRET'));			
		}
	}

?>