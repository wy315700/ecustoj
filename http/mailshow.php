<?php
//$_GET['sender']=1表示是从发件箱过来的

	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect("error.php?msg=".urlencode("Please login first!"));
	$my_user_id = get_to_mysql($_SESSION['user_id']);
	
	if(!isset($_GET['mail_id']))redirect("error.php?msg=".urlencode("The Mail is not exsisted!"));
	$mail_id = get_to_mysql($_GET['mail_id']);
	
	$query = "select * from mails where mail_id=" . intval($mail_id);
	$ret = oj_query($query);
	$row = mysql_fetch_assoc($ret);
	if(empty($row['mail_id']))redirect("error.php?msg=".urlencode("The Mail is not exsisted!"));
	
	$to_user_id   = $row['to_user_id'];
	$from_user_id = $row['from_user_id'];
	$mail_time    = htmlspecialchars($row['mail_time']);
	$title        = htmlspecialchars($row['title']);
	$content      = htmlspecialchars($row['content']);
	
	if($my_user_id!=$to_user_id&&$my_user_id!=$from_user_id)
		redirect("error.php?msg=".urlencode("You cannot read this mail!"));
	
	if($to_user_id==$my_user_id){
		$query="update mails set readed=1 where mail_id=" . intval($mail_id);
		oj_query($query);
	}

	set_ojinfo('title', 'ECUST Online Judge - Read mail');
	include 'include/header.php';
	echo "<br><div style='background: #CAF'>";
	if($_GET['sender'])
		echo "To: " . htmlspecialchars(user_id_to_name($to_user_id));
	else
		echo "From: " . htmlspecialchars($from_user_name=user_id_to_name($from_user_id));
	
	echo "<br>Title: $title<br>";
	echo "Time: $mail_time<br>";
	echo "</div>";
	
	echo "<div style='background: #CCF;padding-bottom: 20px'>";
	echo "<hr/><pre class='mail'>$content</pre>";
	echo "</div>";
	
	echo '<center>';
	if(!$_GET['sender'])
		echo '[<a href="mailsend.php?name=' . urlencode($from_user_name) . '">Reply</a>]';
	echo '</center>';
	
	include 'include/footer.php';
?>
