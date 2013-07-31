<?php
	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first.'));
	
	$update_id=$_SESSION['user_id'];
	$query="select * from users where user_id=" . intval($update_id);
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);
	if(empty($row['user_id']))redirect('error.php?msg=' . urlencode('No such user.'));
	
	if(isset($_POST['pwd'])){
		$old_pwd     = get_to_mysql($_POST['pwd']);
		$new_pwd     = get_to_mysql($_POST['newpwd']);
		$new_email   = get_to_mysql($_POST['email']);
		$new_nick    = get_to_mysql($_POST['nick']);
		$new_school  = get_to_mysql($_POST['school']);
		$new_fromaddr= get_to_mysql($_POST['fromaddr']);
		
		if($old_pwd!=$row['user_pwd'])redirect('modifyuser.php?msg=' . urlencode('Wrong password.'));
		$query="update users set emailaddr='$new_email', nick_name='$new_nick', school='$new_school', fromaddr='$new_fromaddr' ";
		if(strlen($new_pwd)==32)$query=$query . ",user_pwd='$new_pwd' ";
		$query=$query . "where user_id=" . intval($update_id);
		$ret=oj_query($query);

		set_ojinfo('title', 'ECUST Online Judge - Update your info');
		include 'include/header.php';
		echo "<center><h1>Update success</h1></center>";
	}else{
				
function draw_fillin($left,$name,$right){
	echo '<tr>';
	echo '<td>' . htmlspecialchars($left) . '</td>';
	echo "<td><input name=$name value='" . htmlspecialchars($right) . "'/></td>";
	echo '</tr>';
}

		include 'include/header.php';
?>

<form action="modifyuser.php" method="post" onsubmit="return check_update()">
<table align="center">
<?php
	if(isset($_GET['msg'])){
		echo "<tr><td colspan=2 style='text-align:center'>" . get_to_html($_GET['msg']) . "</td></tr>";
	}
?>
	<tr>
		<td>User&nbsp;ID:</td>
		<td><?php echo htmlspecialchars($row['name']) ?></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input name="pwd" id="old_pwd" type="password"/></td>
	</tr>
	<tr>
		<td>New&nbsp;Password:</td>
		<td><input name="newpwd" id="new_pwd" type="password"/></td>
	</tr>
	<tr>
		<td>Repeat&nbsp;password:</td>
		<td><input name="newpwd2" id="new_pwd2" type="password"/></td>
	</tr>
<?php
	draw_fillin('Email:','email',$row['emailaddr']);
	draw_fillin('Nick name:','nick',$row['nick_name']);
	draw_fillin('School:','school',$row['school']);
	draw_fillin('Come from:','fromaddr',$row['fromaddr']);
?>
	<tr>
		<td></td>
		<td><input name="submit" value="Update" type="submit"/></td>
	</tr>
</table>
</form>
<script src="include/md5.js"></script>
<script language="javascript">
function check_update() {
	var ps = document.getElementById('old_pwd');
	if (ps.value.length <= 0) {
		alert("Please input your Password.");
		return false;
	}
	var new_ps = document.getElementById('new_pwd');
	var new_ps2 = document.getElementById('new_pwd2');
	if(new_ps.value != new_ps2.value){
		alert("New passwords do not match.");
		return false;
	}
	ps.value = MD5(ps.value);
	if(new_ps.value.length > 0){
		new_ps.value = MD5(new_ps.value);
		new_ps2.value = MD5(new_ps2.value);
	}
	return true;
}
</script>
<?php
	}
	
	include 'include/footer.php';
?>

