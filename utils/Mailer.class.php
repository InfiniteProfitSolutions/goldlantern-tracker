<?php
/**
* Gold Lantern mailer class used to send notifications to user in
* case of errors on tracker side.
*
*	Save errors into notification folder for displaying on url page notifications.php
*
* @author dino.keco@gmail.com
*/
class Mailer{

	const NAME = '';

	public static function error($message){
		Mailer::send_mail('GL TRACKER ERROR', $message);
	}

	public static function warning($message){
		Mailer::send_mail('GL TRACKER WARNING', $message);
	}

	private static function send_mail($title, $message){
		$filename = 'notifications/'.date('Y-m-d').'-'.$title;

		if (!file_exists($filename)){
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: '. Mailer::NAME . "\r\n";
			mail(MAIL_TO, $title, $message, $headers);
			file_put_contents($filename, $message);
		}
	}
}
