<?php


$service_url = 'http://api.cindy.local/v1/accounts';
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_USERPWD, "admin:admin123"); //Your credentials goes here
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//		'Content-Type: application/json',
//		'Content-Length: ' . strlen($data_string))
//);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('member_id' => 2903444291, 'amount' => 2, 'refer_id' => 333)));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate

$curl_response = curl_exec($curl);
$response = json_decode($curl_response);
curl_close($curl);

var_dump($response);