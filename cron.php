<?php
require_once __DIR__ . '/config.php';

$db = new PDO('mysql:host='.MONITOR_MYSQL_HOST.';dbname='.MONITOR_MYSQL_DB.';charset=utf8', MONITOR_MYSQL_USER, MONITOR_MYSQL_PASSWORD);



function sendSMS($message)
{
	try{
		$soap = new SoapClient(MONITOR_SOAP_API_URL);
		$session = $soap->login(MONITOR_NIC_USER, MONITOR_NIC_PASSWORD,"fr", false);
		$result = $soap->telephonySmsSend($session, MONITOR_NIC_SMS_ACCOUNT, MONITOR_USERNAME, MONITOR_USER_MOBILE,htmlspecialchars($message), "", "1", "", "");
		$soap->logout($session);
	}
	catch(SoapFault $fault)
	{
		echo $fault;
	}
	

}


function notify($row,$newhash,$dt)
{
	global $db;
	if($row['p_sms']==1)
		sendSMS($row['p_notification_tpl']);



	if($row['p_mail']==1)
	{
		$parse = parse_url($row['p_url']);
		$to      = MONITOR_EMAIL_NOTIFICATION;
		$subject = 'Notification : ' . $row['p_notification_tpl'];
		$message = 'The url ' . $parse['host'] . ' has change ! Thx Monitor';
		$headers = 'From: '.MONITOR_REPLY_EMAIL.'' . "\r\n" .
		    'Reply-To: '.MONITOR_REPLY_EMAIL.'' . "\r\n" .
		    'X-Mailer: PHP/' . phpversion();

		mail($to, $subject, $message, $headers);

	}
	if($row['p_regularity']==1){
		$stmt = $db->prepare("UPDATE pages SET p_completed=1 WHERE p_id=:id");
		$stmt->bindValue(':id', $row['p_id'], PDO::PARAM_STR);
		$stmt->execute();
	}

		$stmt = $db->prepare("UPDATE pages SET p_date=:ndate,p_lasthash=:hash WHERE p_id=:id");
		$stmt->bindValue(':ndate', (($dt != NULL) ? $dt->format('Y-m-d H:i:s') : ''), PDO::PARAM_STR);
		$stmt->bindValue(':hash', $newhash, PDO::PARAM_STR);
		$stmt->bindValue(':id', $row['p_id'], PDO::PARAM_STR);
		$stmt->execute();


}





$stmt = $db->query('SELECT * FROM pages WHERE p_completed=0');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();

foreach ($rows as $row)
{

		if(strtotime($row['p_lastcheck'])+(60*$row['p_often']) > time())
			continue; 
		
		$newhash = md5(file_get_contents($row['p_url']));
		$dt=null;
		$h = get_headers($row['p_url'], 1);

		if(array_key_exists('Last-Modified', $h))
			$dt = new \DateTime($h['Last-Modified']);

		if($newhash != $row['p_lasthash'] || ($dt != null && $dt->format('Y-m-d H:i:s') != $row['p_date']) )
			notify($row,$newhash,$dt);

		
}

$db->query('UPDATE `pages` SET `p_lastcheck`=NOW()');	










?>