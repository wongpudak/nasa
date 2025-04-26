<?php
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
    require_once $wp_load;
} else {
    die('Could not load WordPress');
}
$options = get_option('cb_pengaturan');

$apiKey = $options['duitku']['api']; // Your api key
$merchantCode = isset($_POST['merchantCode']) ? $_POST['merchantCode'] : null; 
$amount = isset($_POST['amount']) ? $_POST['amount'] : null; 
$merchantOrderId = isset($_POST['merchantOrderId']) ? $_POST['merchantOrderId'] : null; 
$productDetail = isset($_POST['productDetail']) ? $_POST['productDetail'] : null; 
$additionalParam = isset($_POST['additionalParam']) ? $_POST['additionalParam'] : null; 
$paymentMethod = isset($_POST['paymentCode']) ? $_POST['paymentCode'] : null; 
$resultCode = isset($_POST['resultCode']) ? $_POST['resultCode'] : null; 
$merchantUserId = isset($_POST['merchantUserId']) ? $_POST['merchantUserId'] : null; 
$reference = isset($_POST['reference']) ? $_POST['reference'] : null; 
$signature = isset($_POST['signature']) ? $_POST['signature'] : null; 

if (!empty($merchantCode) && !empty($amount) && !empty($merchantOrderId) && !empty($signature)) {
    $params = $merchantCode . $amount . $merchantOrderId . $apiKey;
    $calcSignature = md5($params);

    if($signature == $calcSignature) {
        //Your code here
		if($resultCode == "00") {
			// echo "SUCCESS"; // Save to database
			aktivasi($additionalParam,'Duitku');
	   	} else {
	        $error = "FAILED"; // Please update the status to FAILED in database
	    }
    } else {
        //throw new Exception('Bad Signature');
        $error = 'Bad Signature';
    }
} else {
    //throw new Exception('Bad Parameter');
    $error = 'Bad Parameter';
}
?>