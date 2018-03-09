
<?php
	error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_DEPRECATED);
	session_start();
	
	
	require_once("includes/globals.php");
	require_once($g_docRoot . "classes/credits.php");
	require_once($g_docRoot . "classes/members.php");
	require_once($g_docRoot . "classes/orders.php");

	require_once($g_docRoot . 'eway-rapid-php-master/include_eway.php');


	//require('vendor/autoload.php');

$apiKey = EWAY_SANDBOX_API_KEY;
$apiPassword = EWAY_SANDBOX_API_PWD;
$apiEndpoint = \Eway\Rapid\Client::MODE_SANDBOX;


$client = \Eway\Rapid::createClient($apiKey, $apiPassword, $apiEndpoint);
$client = \Eway\Rapid::createClient($apiKey, $apiPassword, $apiEndpoint);

$transaction = [
    'Customer' => [
        'TokenCustomerID' => 812976830262,
		  'CardDetails' => [
            'CVN' => '123',
        ]

    ],
    'Payment' => [
        'TotalAmount' => 10,
    ],
    'TransactionType' => \Eway\Rapid\Enum\TransactionType::PURCHASE,
];

$response = $client->createTransaction(\Eway\Rapid\Enum\ApiMethod::DIRECT, $transaction);

var_dump($response);
if ($response->TransactionStatus) {
    echo 'Payment successful! ID: '.$response->TransactionID;
}

echo("<hr>");

?>
