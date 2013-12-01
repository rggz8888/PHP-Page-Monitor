<?php
require_once __DIR__ . '/config.php';

$db = new PDO('mysql:host='.MONITOR_MYSQL_HOST.';dbname='.MONITOR_MYSQL_DB.';charset=utf8', MONITOR_MYSQL_USER, MONITOR_MYSQL_PASSWORD);

$message = '';

if(isset($_GET['delid']) && is_numeric($_GET['delid']))
{
$stmt = $db->prepare("DELETE FROM pages WHERE p_id=:id");
$stmt->bindValue(':id', $_GET['delid'], PDO::PARAM_STR);
$stmt->execute();
}
if(isset($_POST['add']))
{

	/**
	quick & dirty check
	**/    
	if(preg_match('/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/', $_POST['url']) 
		&& is_numeric($_POST['sms']) && is_numeric($_POST['mail']) 
		&& is_numeric($_POST['often']) && is_numeric($_POST['regularity']))
	{
		$h = get_headers($_POST['url'], 1);
		$dt=null;
		if(array_key_exists('Last-Modified', $h))
		$dt = new \DateTime($h['Last-Modified']);

		
		$stmt = $db->prepare("INSERT INTO  `pages` (`p_id`,`p_url`,`p_date`,`p_sms`,`p_mail`,`p_often`,`p_regularity`,`p_lasthash`,`p_completed`,`p_notification_tpl`, `p_lastcheck`)
			VALUES (NULL ,  ?,  ?,  ?,  ?, ?, ?, ?,  '0', ?, NOW());");
		$stmt->bindValue(1, $_POST['url'], PDO::PARAM_STR);		
		$stmt->bindValue(2, (($dt != NULL) ? $dt->format('Y-m-d H:i:s') : ''), PDO::PARAM_STR);
		$stmt->bindValue(3, $_POST['sms'], PDO::PARAM_INT);
		$stmt->bindValue(4, $_POST['mail'], PDO::PARAM_INT);
		$stmt->bindValue(5, $_POST['often'], PDO::PARAM_INT);
		$stmt->bindValue(6, $_POST['regularity'], PDO::PARAM_STR);
		$stmt->bindValue(7, md5(file_get_contents($_POST['url'])), PDO::PARAM_STR);
		$stmt->bindValue(8, $_POST['notification_tpl'], PDO::PARAM_STR);
		$stmt->execute();
		$message = '<p><span style="color:green; font-size:25px;"> Url added. </span></p>'; 

	}
}


$stmt = $db->query('SELECT * FROM pages');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();


?>




<!DOCTYPE html>
<html>
<head>
	<title> Monitor </title>
</head>
<body>
<h1> Monitor </h1>

<?php echo $message; ?>


<h3> Urls </h3>
<table style="border:1px solid;">
<tr>
	<th>Url</th>
	<th>Interval Comparison(minutes)</th>
	<th>N.Often</th> 
	<th>N.SMS</th>
	<th>N.Email</th>
	<th>N.Tpl</th>
	<th>Completed</th>
	<th>Delete?</th>
</tr>
<?php 
$boolarray = Array(false => 'false', true => 'true');

if(!empty($rows))
foreach($rows as $row)
{
	$parse = parse_url($row['p_url']);
	echo '<tr>
	<td><a href="'.$row['p_url'].'">'. $parse['host'].'</a></td>
	<td>'.$row['p_often'].'</td>
	<td>'.$row['p_regularity'].'</td>
	<td>'.$boolarray[$row['p_sms']].'</td>
	<td>'.$boolarray[$row['p_mail']].'</td>
	<td>'.htmlspecialchars($row['p_notification_tpl']).'</td>
	<td>'.$boolarray[$row['p_completed']].'</td>
	<td><a href="index.php?delid='.$row['p_id'].'">delete?</a></td>
	</tr>';

?>

<?php
}
?>
</table>

<h3> Add url </h3>


<form action="index.php" method="post">
<p>
<label for="url">Url : <input type="text" size="50" name="url" id="url" /></label><br/>Notification by : 
<input type="checkbox" value="1" name="sms" id="sms" /> <label for="sms">SMS ?</label> 
<input type="checkbox" value="1" name="mail" id="mail" /> <label for="mail">eMail ?</label> <br/>
<label for="often">Interval Comparison frequency (minute) : </label><input type="text" size="3" name="often" id="often" value="" /><br/>
<label for="regularity">Often notification : </label>
<select name="regularity" id="regularity"><option value="1">Just one changement</option><option value="2">all changements</option></select><br/>
<label for="notification_tpl">Notification template (140 chars) :</label><input type="text" maxlength="140" size="50" name="notification_tpl" id="notification_tpl" /><br/>
<input type="submit" name="add" value="add" />
</p>
</form>
</body>
</html>