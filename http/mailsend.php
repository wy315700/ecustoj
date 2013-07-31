<?php
//$_GET['name']收件人

	require_once 'include/function.php';

	if(!isset($_SESSION['user_id']))redirect("error.php?msg=".urlencode("Please login first!"));
	$from_user_id=$_SESSION['user_id'];
	if(isset($_GET['name']))
		$to_user_name=$_GET['name'];
	else if(isset($_POST['to']))
		$to_user_name=$_POST['to'];

	if(strlen($to_user_name)>0)
		$to_user_id=name_to_user_id(get_to_mysql($to_user_name));

	set_ojinfo('title', 'ECUST Online Judge - SendMail');

	include 'include/header.php';
?>
<?php
	if(isset($_POST['to'])){
		$title = get_to_mysql($_POST['title']);
		$text  = get_to_mysql($_POST['textarea']);
		$query = "insert into mails (to_user_id,from_user_id,mail_time,title,content,readed,reader_del,writer_del)"
			." values($to_user_id, $from_user_id, now(), '$title', '$text', 0, 0, 0)";
		oj_query($query);
		echo "<center><h1>Send Mail Successfully!</h1></center>";
	}else{
?>
<center><h1 style="color:#00f;">Write Mail</h1></center>
<form name="form1" method="post" action="mailsend.php" onsubmit="return check_mail()">
  <label><strong>To(name):</strong>
  <input name="to" type="text" id="mail_to" size="25" <?php if(strlen($to_user_name)>0)echo 'value="' . get_to_html($to_user_name) . '"';?>>
  </label>
  <p>
    <label><strong>Title:</strong>
    <input name="title" type="text" id="mail_title" size="50">
    </label>
  </p>
  <p>
    <label>
    <textarea name="textarea" id="mail_text" cols="100" rows="25"></textarea>
    </label>
  </p>
  <p>
    <label>
    <input type="submit" name="Submit" value="Send">
    </label>
  </p>
</form>
<script language="javascript">
function check_mail(){
	var mail_to = document.getElementById('mail_to');
	var mail_title = document.getElementById('mail_title');
	var mail_text = document.getElementById('mail_text');
	if( mail_to.value.length<=0 || mail_title.value.length<=0
		|| mail_text.value.length<=0){
		alert("User name, Title and Text should not be empty!");
		return false;
	}
	return true;
}
</script>
<?php
	}
	
	include 'include/footer.php';
?>
