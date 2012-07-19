<?php

class CommunicationServices{

	public function sendEmail($toAddress, $subject, $body){
		$result = true;
		if (!mail($toAddress, $subject, $body)) {
			error_log("Exception. Could not send email [".$toAddress."][".$subject."]");
			$result = false;
			throw new Exception('029');
		}
		return $result;
	}
}

?>