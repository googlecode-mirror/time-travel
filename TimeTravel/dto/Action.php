<?php
class Action {

	public $name;
	public $serviceClass;
	public $parameters;
	public $postlogon;
	public $forker;

	public function __construct($actionName)  {
		$this->name = $actionName;
		$this->initializeAction($actionName);
	}


	private function initializeAction($actionName){
		$objDOM = $this->getDOM();
		$action = $objDOM->getElementsByTagName("action");
		$className = "";

		foreach($action as $value )
		{
			$name = $value->getElementsByTagName("name");

			if ($name->item(0)->nodeValue == $actionName){
				error_log("ACTION: ".$name->item(0)->nodeValue);

				$this->postlogon = $value->getElementsByTagName("postlogon")->item(0)->nodeValue;
				error_log("POSTLOGIN: ".$this->postlogon);
				
				
				$this->forker =$value->getElementsByTagName("forker")->item(0)->nodeValue;

				$classElement = $value->getElementsByTagName("service-class-name");
				$className = $classElement->item(0)->nodeValue;
				$this->serviceClass = $className;

				$paramArray = array();

				$params = $value->getElementsByTagName("param");
				foreach($params as $param){

					foreach ($param->attributes as $attrName => $attrNode) {
						if ($attrName == "required"){
							array_push($paramArray, new Parameter($param->nodeValue, $attrNode->value));
						}
					}

				}
				$this->parameters = $paramArray;
				break;
			}
		}

	}

	private function getDOM(){
		$objDOM = new DOMDocument();
		$objDOM->load("navigators/controller.xml");
		return $objDOM;
	}

}

class Parameter {
	public $name;
	public $required;

	public function __construct($name, $required)  {
		$this->name = $name;
		$this->required = $required;
	}
}
?>