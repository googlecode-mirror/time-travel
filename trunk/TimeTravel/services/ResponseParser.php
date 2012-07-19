<?php
class Responder{

	public function constructErrorResponse($reponseObject){
		return "<result><code>1</code><errMessage>".  $reponseObject ."</errMessage></result>";
	}

	public function constructResponse($reponseObject){
		if (!isset($reponseObject)) {
			return "<result><code>0</code><errMessage></errMessage></result>";
		} else if (is_string($reponseObject)){
			return "<result><code>0</code><errMessage>".  $reponseObject ."</errMessage></result>";
		} else {

			if (is_array($reponseObject)){
				return $this->constructResponseForArray($reponseObject);
			}

			$response = "<result><code>0</code><errMessage></errMessage>";

			$response = $response . $this->getDOMforObject($reponseObject);

			$response = $response . "</result>";
		}

		error_log($response);
		return $response;
	}

	public function constructResponseForArray($reponseObject){
		$response = "<result><code>0</code><errMessage></errMessage>";

		$response = $response ."<list>";

		foreach($reponseObject as $object){
			$response = $response . $this->getDOMforObject($object);
		}

		$response = $response ."</list>";

		$response = $response . "</result>";
		return $response;
	}


	public function constructResponseForKeyValue($reponseObject){
		$response = "<result><code>0</code><errMessage></errMessage>";

		$response = $response ."<list>";

		foreach($reponseObject as $key => $value){
			$response = $response ."<". $key . ">" . $value ."</" .$key . ">";
		}

		$response = $response ."</list>";

		$response = $response . "</result>";
		return $response;
	}

	private function getDOMforObject($object){
		$class_vars = get_class_vars(get_class($object));
		$response = "<". get_class($object).">";
		foreach ($class_vars as $name => $value) {
			$response = $response ."<". $name . ">" .$object->$name ."</" .$name . ">";
		}

		$response = $response ."</". get_class($object).">";
		return $response;
	}
}

?>