function validateInputs(div){
	var passed = true;
	
	resetValidationMessages(div);
	
	$(div).find("input").each(function(){
		if ($(this).attr("validation") != null){
			response = validateInput($(this), $(this).attr("validation"));
			if (response != "success"){
				passed = false;
				insertMessage($(this), response)
			}
		}
	});
	
	return passed;
}

function resetValidationMessages(div){
	$(div).find(".valResponse").each(function(){
		$(this).remove();
	});
}

function validateInput(input, type){
	var value = $(input);
	if (type == "username"){
		return validateUsername(value);
	} else 	if (type == "password"){
		return validatePassword(value);
	} else if (type == "mandatory"){
		return validateMandatory(value);
	} else if (type == "email"){
		return validateEmailAddress(value);
	} else if (type == "phonenumber") {
		return validatePhoneNumber(value);
	}
	
	return "success";
}

function validatePhoneNumber(input){
	var inputString = $(input).val();
	var response = isStringEmpty(inputString);
	if (response != "success"){
		return response;
	}
	
	var re = /[\D]/g;
	
	if (inputString.substr(0,1) == "+"){
		inputString = inputString.substr(1);
	}
	if (re.test(inputString)) return "Invalid phone number";
	return "success";
}

function validateEmailAddress(input){
	var response = isStringEmpty($(input).val());
	if (response != "success"){
		return response;
	}
	
	response = isEmailAddressValid($(input).val());
	if (response != "success"){
		return response;
	}
	
	return "success";
}

function validateMandatory(input){
	var response = isStringEmpty($(input).val());
	if (response != "success"){
		return response;
	}
	return "success";
}

function isEmailAddressValid(address) {
	var result = "success";   
	var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	   if(reg.test(address) == false) {
	      return "Invalid email address.";
	   }
   return "success";
}

function validateUsername(username){
	var response = isStringEmpty($(username).val());
	if (response != "success"){
		return response;
	}
	
	response = isStringContainSpecialChars($(username).val());
	if (response != "success"){
		return response;
	}
	
	response = validateLength($(username), 20, 4);
	if (response != "success"){
		return response;
	}
	
	return "success";
}

function validatePassword(password){
	var response = isStringEmpty($(password).val());
	if (response != "success"){
		return response;
	}
	
	response = validateLength($(password), 20, 4);
	if (response != "success"){
		return response;
	}
	
	return "success";
}

function isStringEmpty(string){
	if (string.length > 0){
		return "success";
	} else {
		return "This value cannot be empty";
	}
}

function isStringContainSpecialChars(string){
	var result = "success";
	var iChars = "!@#$%^&*()+=-[]\\\';,./{}|\":?";

	for (var i = 0; i < string.length; i++) {
	 	if (iChars.indexOf(string.charAt(i)) != -1) {
	 		return "No special characters allowed.";
	 	}
	}
	return result;
}

function validateLength(inputElement, max, min){
	var result = "success";
	if ($(inputElement).val().length < min){
		return $(inputElement).attr("nicename") +" cannot be shorter than "+min +" characters.";
	}
	
	if ($(inputElement).val().length > max){
		return $(inputElement).attr("nicename") +" cannot be longer than "+max +" characters.";
	}
	return result;
}

function insertMessage(element, message){
	$(element).parent().append(wrapMessage(message));
}

function wrapMessage(message){
	return "<span class='valResponse' style='color: red;'><br/>"+ message+"</span>"
}