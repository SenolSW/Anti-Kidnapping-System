<?php

$passphrase = '78589cef696570f4f96ada9e22d16abd6f6c33d3253e49842e8017771844df99';

function encryptthis($data, $passphrase) {
    $secret_key = hex2bin($passphrase);
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted_64 = openssl_encrypt($data, 'aes-256-cbc', $secret_key, 0, $iv);
    $iv_64 = base64_encode($iv);
    $json = new stdClass();
    $json->iv = $iv_64;
    $json->data = $encrypted_64;
    return base64_encode(json_encode($json));
} 

function decryptthis($data, $passphrase) {
    $secret_key = hex2bin($passphrase);
    $json = json_decode(base64_decode($data));
    $iv = base64_decode($json->{'iv'});
    $encrypted_64 = $json->{'data'};
    $data_encrypted = base64_decode($encrypted_64);
    $decrypted = openssl_decrypt($data_encrypted, 'aes-256-cbc', $secret_key, OPENSSL_RAW_DATA, $iv);
    return $decrypted;
}

class Config
{
	public $base_url = 'http://localhost/SalvationJewellers/';
	public $connect;
	public $query;
	public $statement;
	public $now;

	public function __construct()
	{
		$this->connect = new PDO("mysql:host=localhost;dbname=salvation_db", "root", "");

		date_default_timezone_set('Asia/Colombo');

		session_start();

		$this->now = date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
	}

	function execute($data = null)
	{
		$this->statement = $this->connect->prepare($this->query);
		if($data)
		{
			$this->statement->execute($data);
		}
		else
		{
			$this->statement->execute();
		}		
	}

	function row_count()
	{
		return $this->statement->rowCount();
	}

	function statement_result()
	{
		return $this->statement->fetchAll();
	}

	function get_result()
	{
		return $this->connect->query($this->query, PDO::FETCH_ASSOC);
	}

	function is_login()
	{
		if(isset($_SESSION['admin_id']))
		{
			return true;
		}
		return false;
	}


	function clean_input($string)
	{
	  	$string = trim($string);
	  	$string = stripslashes($string);
	  	$string = htmlspecialchars($string);
	  	return $string;
	}

	function get_total_products()
	{
		$this->query = "
		SELECT * FROM product_table 
		";
		$this->execute();
		return $this->row_count();
	}	
	
	function get_total_customers()
	{
		$this->query = "
		SELECT * FROM customer_table 
		";
		$this->execute();
		return $this->row_count();
	}
	
	function get_total_profiles()
	{
		$this->query = "
		SELECT * FROM profile_table 
		";
		$this->execute();
		return $this->row_count();
	}	
	
	function get_total_emergency()
	{
		$this->query = "
		SELECT * FROM emergency_table 
		";
		$this->execute();
		return $this->row_count();
	}	

	function get_total_feedbacks()
	{
		$this->query = "
		SELECT * FROM feedback_table 
		";
		$this->execute();
		return $this->row_count();
	}	

}

?>