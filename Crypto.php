<?php 
	class Crypto{
		private static $method = 'AES-256-CBC'; 
		private static $crypt_strong;
		private static $skey = '?81!-`g}3||vsHm';
		private static $siv = 'A5A6BCE1EDEA65C99F6D82738E752';
		
		public static function init(){
			self::$siv = openssl_random_pseudo_bytes(16, self::$crypt_strong);
		}

		public static function encrypt($data){
	        	return base64_encode(openssl_encrypt($data, self::$method, hash('sha256', self::$skey), 0, substr(hash('sha256', self::$siv), 0, 16)));
		}

		public static function decrypt($data){
			return openssl_decrypt(base64_decode($data), self::$method, hash('sha256', self::$skey), 0, substr(hash('sha256', self::$siv), 0, 16));
		}
	}
 ?>