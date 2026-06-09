<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

$token = "487|vz9uPSacsqTB2mZx4iN4Rqpi7eQxD46l7UseFr7B";
$user_uuid  = "swp_sm_2a19e910-c5c4-43fc-aed2-5afca37796e8";


$api_url = "https://www.switchpay.in/api/createTransaction";


$ch = curl_init();
$URL = "amount=".$_POST['amount']."&description=".$_POST['description']."&name=".$_POST['name']
."&email=".$_POST['email']."&mobile=".$_POST['mobile']."&user_uuid=".$user_uuid."&enabledModesOfPayment=".$_POST['enabledModesOfPayment']."&payment_method=".$_POST['payment_method']."&callback_url=".$_POST['callback_url']."&source=api";



curl_setopt($ch, CURLOPT_URL,$api_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,$URL);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
   'Authorization: Bearer ' . $token
));

$data = curl_exec($ch);

$info = curl_getinfo($ch);

curl_close($ch);