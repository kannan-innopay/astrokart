<?php

	echo "Received callback<br>";
	echo "--------------------------";
	$transaction_data = $_POST['transaction_data'];
	$request_data = json_decode($transaction_data,true)['request_data'];
	$order_id = json_decode($transaction_data,true)['order_id'];
	$status = json_decode($transaction_data,true)['status'];
	$pg_payload = json_decode($transaction_data,true)['pg_payload'];
	echo "<br>request_data => ".$request_data;
	echo "<br><br>order_id => ".$order_id;
	echo "<br><br>status => ".$status;
	echo "<br><br>pg_payload => ".$pg_payload;
	echo "<br>--------------------------<br>";
	