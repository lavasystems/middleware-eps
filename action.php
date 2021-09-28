<?php
$config_filename = 'config.json';

if (!file_exists($config_filename)) {
    throw new Exception("Can't find ".$config_filename);
}

$config = json_decode(file_get_contents($config_filename), true);

$id = filter_var($_REQUEST['id'], FILTER_SANITIZE_STRING);

$response['status'] = 'failed';
$response['message'] = NULL;
$response['data'] = NULL;

switch ($id) {

	case 'process-payment':
	
		require_once('php/payment.php');
		$payment = new Payment();

		$data = $_POST;
		$data['type'] = 'html';

		return $payment->process($data);
		
	break;

	case 'request':
	
		require_once('php/payment.php');
		$payment = new Payment();

		$data = $_POST;
		$data['type'] = 'json';

		return $payment->process($data);
		
	break;

	case 'response':

        require_once('php/payment.php');
        $payment = new Payment();

        $data = $_POST;

        return $payment->response($data);
		
	break;
}
