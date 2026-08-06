<?php
$getClient = isset($_GET['clientId']) ? preg_replace('/[^0-9]/', '', (string)$_GET['clientId']) : '';
$url = 'https://itourssupport.in/model/get-ticket-api.php?cid=' . $getClient;

$dataAll = '';
if (function_exists('curl_init')) {
	$ch = curl_init($url);
	curl_setopt_array($ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_CONNECTTIMEOUT => 10,
		CURLOPT_TIMEOUT => 30,
		CURLOPT_SSL_VERIFYPEER => true,
		CURLOPT_SSL_VERIFYHOST => 2,
		CURLOPT_USERAGENT => 'iTours-CRM-TicketSystem',
	));
	$response = curl_exec($ch);
	$errno = curl_errno($ch);
	curl_close($ch);
	if ($errno === 0 && $response !== false) {
		$dataAll = $response;
	}
} elseif (ini_get('allow_url_fopen')) {
	$dataAll = @file_get_contents($url);
}

echo $dataAll;
?>
<script>
$('#ticketTable').DataTable();
</script>
