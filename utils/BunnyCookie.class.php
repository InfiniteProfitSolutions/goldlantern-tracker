<?php
if (! class_exists ( 'Mongo' )) exit ( 'No Mongo DB driver installed' );

/**
 * Check if a cookie can be restored from the webpage_visitors collection
 *
 * @author dino.keco@gmail.com
 */
class BunnyCookie {

	private $webpageVisitor;

	function __construct($webpageVisitorCollection = 'webpage_visitors') {
		try {
			$connection = new MongoClient ( MONGO_CONNECTION_STRING, ['connectTimeoutMS' => '1000'] );
			$connection->setReadPreference ( MongoClient::RP_PRIMARY_PREFERRED, array () );
			$this->webpageVisitor = $connection->selectCollection ( MONGO_SCHEMA, $webpageVisitorCollection );
		} catch ( Exception $e ) {
			throw new Exception ( 'Failed to access to collection ' . $webpageVisitorCollection, 0, $e );
		}
	}
	function restoreCookie() {
		$restoredCookie = null;
		if ($this->webpageVisitor) {
			try {
				$client = $this->is_val ( $_GET ['c'] ) ? $_GET ['c'] : $_GET ['client'];
				$ipAddress = $this->get_ip ();
				$userAgent = $this->get_user_agent ();
				$cookie = $this->webpageVisitor->findOne ( array (
						'client' => $client,
						'profile.ip' => $ipAddress,
						'profile.ua' => $userAgent
				) );

				if ($cookie) {
					$restoredCookie = $cookie ['master_cookie'];
					$this->set_cookie($restoredCookie);
				}else{
					$this->set_cookie($this->rnd_str ());
				}
			} catch (Exception $e) {
				$this->set_cookie($this->rnd_str ());
				throw new Exception ( 'Failed to find a cookie in db '.$e->getMessage(), 0, $e );
			}
		}else{
			$this->set_cookie($this->rnd_str ());
		}
	}

	private function set_cookie($cookie){
		setcookie ( '_gl_c_', $cookie, time () + 157680000, '/', '.ips.ms' );
		$_COOKIE ['_gl_c_'] = $cookie;
	}

	private function is_val($val) {
		return (isset ( $val ) && trim ( $val ) != '' && trim ( $val ) != 'unknown');
	}
	private function get_ip() {
		if ($this->is_val ( @$_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
			return $this->clean_ip ( $_SERVER ['HTTP_X_FORWARDED_FOR'] );
		} else {
			return $_SERVER ['REMOTE_ADDR'];
		}
	}
	private function get_user_agent() {
		if ($this->is_val ( @$_SERVER ['HTTP_USER_AGENT'] )) {
			return preg_replace('/[[:^print:]]/', '', @$_SERVER ['HTTP_USER_AGENT']);
		}
		return null;
	}

	private function clean_ip($ip) {
		$ips = explode(",", $ip);
		$filtered_ip = end($ips);
		return trim($filtered_ip);
	}

	private function rnd_str($length = 30) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen ( $characters );
		$randomString = '';
		for($i = 0; $i < $length; $i ++) {
			$randomString .= $characters [rand ( 0, $charactersLength - 1 )];
		}
		return $randomString;
	}
}
