<?php

class ErrorCodes {

	public function getErrorMessage($errCode){
		
		if (!isset($errCode)){
			$errCode = "004";
		}

		$messages = array
		(
		"001" => "Could not log user in. Possible DB error.",
		"002" => "Invalid credentials. Try again.",
		"003" => "Could not log user out.",
		"004" => "Unknown error occured",
		"005" => "Could not create day",
				
				
		"006" => "Could not delete budget item.",
		"007" => "Could not save account details.",
		"008" => "Could not get budget items.",
		"009" => "Could not get accounts as budget items.",
		"010" => "Could not save transaction.",
		"011" => "Error. Parameter not set.",
		"012" => "Error. Could not getSavingsTransactions.",
		"013" => "Error. Could not registerSavings.",
		"014" => "Error. Could not doSavingsReconcile.",
		"015" => "Error. Could not deleteTransaction.",
		"016" => "Error. Could not createAccount.",
		"016" => "Error. Could not getBudgetItemsForBudget.",
		"017" => "Error. Could not deleteBudget.",
		"018" => "Error. Could not deleteBudget.",
		"019" => "Error. Could not save your details. Please try again later.",
		"020" => "Error. The username supplied does not exist.",
		"021" => "Error. Could not send email.",
		"022" => "Error. The username you have selected already exists. Try another one.",
		"023" => "Error. We have picked up that the email address you supplied already exists. Did you forget your password? Click on 'Login' then 'Forgot Password'",
		"024" => "Error. Your account has been disabled. Send an email to 'sabside@16thnote.co.za' if you feel this is inappropriate.",
		"027" => "Error. Could not find user details.",
		"028" => "No username or email address supplied. Cannot get user details",
		"029" => "We could not send your details to your email address. Please try again later.",
		"030" => "We could not reset your password, please try again later",
		"031" => "We cannot find your details. Try another email address or username.",
		"032" => "Could not update password",
		"033" => "Could not get user password",
		"034" => "The current password you have entered is incorrect.",
		"035" => "The email address you picked already exists.",
		"036" => "Could not save lift.",
		"037" => "You are trying to access data that you are not authorized to.",
		"038" => "Username not set in session.",
		"039" => "Could not copy files to main directory.",
		"040" => "Could not save Gmail details. Please try again.",

		//GENERAL
		"025" => "Error. supplied field is too short.",
		"026" => "You need to login to use this function."
		);


		if (!isset($messages[$errCode])){
			return $errCode;
		} else{
			return $messages[$errCode];
		}
	}
}

?>