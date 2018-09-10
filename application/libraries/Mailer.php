<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mailer {

	public $mail = null;

	function __construct() {
		require_once('PHPMailer/PHPMailerAutoload.php');
		$this->mail = new PHPMailer(true);
	}

	public function send_mail($mailset, $xmail, $body) {
		try{
			$mail = &$this->mail;

//			$mail->isSendmail();
			$mail->isSMTP();	// 設定使用 SMTP 方式寄信

			if($mailset['xauth'] == 'yes') $auth = true;
			else $auth = false;
			$mail->SMTPAuth = $auth;

			$mail->Host = $mailset['xserver'];

			$mail->Username = $mailset['xusername'];
			$mail->Password = $mailset['xpassword'];

			$mail->Port = $mailset['xport'];
			// $mail->SMTPSecure = 'ssl';

			$mail->From = $mailset['xfrom'];
			$mail->FromName = $mailset['xfromname'];

			if(count($xmail)>0) {
				foreach ($xmail as $value) {
					$mail->addAddress($value['xmail']);	// Add a recipient
				}
			}

			// $mail->addReplyTo($from/*, $from_name*/);

			$mail->IsHTML(true);	// 設定郵件內容為HTML

			$mail->CharSet = 'UTF-8';
			$mail->Encoding = 'base64';
			$mail->Subject = $xmail[0]['xmailsubject'];
			$mail->Body = $body;

			if($mail->send()) return true;
			return false;

		} catch (phpmailerException $e) {

			// echo $e;
			return false;

			//Pretty error messages from PHPMailer
//			throw new Exception($e->errorMessage(), Errid::UNKNOWN);
		}
	}
}

/* End of file Mailer.php */
/* Location: ./application/libraries/Mailer.php */
