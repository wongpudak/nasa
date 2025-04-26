<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } ?>
<?php
    $merchantCode = $options['duitku']['merchant']; // from duitku
    $merchantKey = $options['duitku']['api']; // from duitku
    $paymentAmount = $harga;
    $paymentMethod = $metode; // WW = duitku wallet, VC = Credit Card, MY = Mandiri Clickpay, BK = BCA KlikPay
    $merchantOrderId = time(); // from merchant, unique
    $productDetails = $namaproduk;
    $email = $datamember->email; // your customer email
    $phoneNumber = $datamember->telp; // your customer phone number (optional)
    $additionalParam = $idorder; // optional
    $merchantUserInfo = ''; // optional
    $customerVaName = $datamember->nama; // display name on bank confirmation display
    $callbackUrl = site_url().'/wp-content/plugins/wp-affiliasi/duitkucall.php'; // url for callback
    $returnUrl = site_url().'/?page_id='.$options['successpage']; // url for redirect
    $expiryPeriod = '10'; // set the expired time in minutes

    $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $merchantKey);

    $item1 = array(
        'name' => $namaproduk,
        'price' => $harga,
        'quantity' => 1);

    $itemDetails = array(
        $item1
    );

    $params = array(
        'merchantCode' => $merchantCode,
        'paymentAmount' => $paymentAmount,
        'paymentMethod' => $paymentMethod,
        'merchantOrderId' => $merchantOrderId,
        'productDetails' => $productDetails,
        'additionalParam' => $additionalParam,
        'merchantUserInfo' => $merchantUserInfo,
	'customerVaName' => $customerVaName,
        'email' => $email,
        'phoneNumber' => $phoneNumber,
        'itemDetails' => $itemDetails,
        'callbackUrl' => $callbackUrl,
        'returnUrl' => $returnUrl,
        'signature' => $signature,
	'expiryPeriod' => $expiryPeriod
    );


    $params_string = json_encode($params);
    if (isset($options['duitkusand'])) {
        $url = 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'; // Sandbox
    } else {
        $url = 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'; // Production
    }
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url); 
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);                                                                  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
        'Content-Type: application/json',                                                                                
        'Content-Length: ' . strlen($params_string))                                                                       
    );   
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    //execute post
    $request = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode == 200) {
        $result = json_decode($request, true);
        header('location: '. $result['paymentUrl']);
        echo "paymentUrl :". $result['paymentUrl'] . "<br />";
        echo "merchantCode :". $result['merchantCode'] . "<br />";
        echo "reference :". $result['reference'] . "<br />";
    	echo "vaNumber :". $result['vaNumber'] . "<br />";
    	echo "amount :". $result['amount'] . "<br />";
    	echo "statusCode :". $result['statusCode'] . "<br />";
    	echo "statusMessage :". $result['statusMessage'] . "<br />";
    } else {
        echo $httpCode;
        echo $request;
    }
?>