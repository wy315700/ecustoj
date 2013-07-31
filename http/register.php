<?php
	require_once 'include/function.php';

	set_ojinfo('title', 'ECUST Online Judge - Register');
	
	if(isset($_POST['name'])){
		$reg_name    = get_to_mysql($_POST['name']);
		$reg_pwd     = get_to_mysql($_POST['pwd']);
		$reg_email   = get_to_mysql($_POST['email']);
		$reg_nick    = get_to_mysql($_POST['nick']);
		$reg_school  = get_to_mysql($_POST['school']);
		$reg_fromaddr= get_to_mysql($_POST['fromaddr']);
		
		if(strlen($reg_pwd)!=32)redirect('register.php?msg=' . urlencode("Please make sure that you have enabled the Javascript."));
		
		$query="select name from users where name='$reg_name'";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if(!empty($row[0]))
		{
			if(get_magic_quotes_gpc())
				redirect('register.php?msg=' . urlencode("User " . stripslashes($_POST['name']) . " already exsist."));
			else
				redirect('register.php?msg=' . urlencode("User " . $_POST['name'] . " already exsist."));
		}
			
		
		$query="insert into users (name, user_pwd, emailaddr, reg_time, nick_name, school, fromaddr)"
			. " values ('$reg_name', '$reg_pwd', '$reg_email', now(), '$reg_nick', '$reg_school', '$reg_fromaddr')";
		$ret=oj_query($query);

		include 'include/header.php';
		echo "<center><h1>Register success</h1></center>";
	}else{
	
	
		include 'include/header.php';
?>

<form action="register.php" method="post" onsubmit="return check_reg()">
<table align="center">
<?php
	if(isset($_GET['msg'])){
		echo "<tr><td colspan=2 style='text-align:center'>" . get_to_html($_GET['msg']) . "</td></tr>";
	}
?>
	<tr>
		<td>User&nbsp;ID:</td>
		<td><input name="name" id="reg_name" maxlength=32 /></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input name="pwd" id="reg_pwd" type="password"/></td>
	</tr>
	<tr>
		<td>Repeat&nbsp;password:</td>
		<td><input name="pwd2" id="reg_pwd2" type="password"/></td>
	</tr>
	<tr>
		<td>Email:</td>
		<td><input name="email" maxlength=100 /></td>
	</tr>
	<tr>
		<td>Nick&nbsp;name:</td>
		<td><input name="nick" maxlength=100 /></td>
	</tr>
	<tr>
		<td>School:</td>
		<td><input name="school" maxlength=100 /></td>
	</tr>
	<tr>
		<td>Come&nbsp;from:</td>
		<td><input name="fromaddr" maxlength=100 /></td>
	</tr>
	<tr>
		<td></td>
		<td><input name="submit" value="Register" type="submit"/></td>
	</tr>
</table>
</form>
<script src="include/md5.js"></script>
<script language="javascript">
function check_reg() {
	var usr = document.getElementById('reg_name');
	if (usr.value.length <= 0) {
		alert("Please input your User ID.");
		return false;
	}
	var ps = document.getElementById('reg_pwd');
	var ps2 = document.getElementById('reg_pwd2');
	if (ps.value.length <= 0 || ps2.value.length <= 0) {
		alert("Please input and repeat your Password.");
		return false;
	}
	if(ps.value != ps2.value){
		alert("Password do not match.");
		return false;
	}
	ps.value = MD5(ps.value);
	ps2.value = MD5(ps2.value);
	return true;
}
</script>
<?php
	}
	
	include 'include/footer.php';
?>

