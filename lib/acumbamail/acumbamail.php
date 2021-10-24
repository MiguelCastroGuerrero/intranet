<?php

class acumbamail {

	private $api_uri;
	private $auth_token;

	function __construct() {
		$this->api_uri = 'https://acumbamail.com/api/1/';
		$this->auth_token = 'RXK6MBEOXLqxmRj39VDk';
	}
	
	public function getCreditsSMS() {
		return $this->apiCall('getCreditsSMS');
	}

	public function sendSMS() {
		$response = $this->getCreditsSMS();
		$credits = $response['Creditos'];

		if ($credits) {
			return $this->apiCall('sendSMS', '{ "recipient": "+34628209623", "body": "message test", "sender": "IES Monterroso" }');
		}
		else {
			return 'No tiene créditos SMS para enviar.';
		}
	}

	function apiCall($request, $data = array()) {

		$params = array(
			'auth_token' => $this->auth_token
		);

		if (! empty($data)) {
			array_push($params, $data);
		}

		if (function_exists("curl_init")) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $this->api_uri . $request . '/',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $params
			));

			$response = curl_exec($curl);
			$http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);

			switch ($http_status) {
				case 200: 
					return json_decode($response, true);
				case 400:
					return 'Petición incorrecta: Algún argumento ha sido incorrecto.';
				case 401:
					return 'No autorizado, el proceso de autenticación ha sido incorrecto.';
				case 500:
					return 'Se ha producido algún error en el servidor. Infórmanos para que lo arreglemos.';
				default: 
					return 'HTTP Error ' . $http_status;
			}
		}
		else {
			return 'Para utilizar este plugin necesitas tener cURL instalado en su servidor.';
		}
	}

	
}

$sms = new acumbamail();
echo $sms->sendSMS();
