<?php
/**
 * the header of all pages.
 */

	require_once 'function.php';
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title><?php echo (get_ojinfo('title')); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link href="style/style.css" rel="stylesheet" type="text/css" />
		<link title="Default" rel="oj_theme stylesheet" type="text/css" />
		<script type="text/javascript" src="libs/jQuery/jquery.cookie.js"></script>
		<script type="text/javascript" src="libs/oj.core.js"></script>
		<?php 
        include_once 'multi_theme.php';
		if(get_ojinfo('highlight'))
			include_once ('codehighlight.php');
		
		if (get_ojinfo('attachhead'))
			echo get_ojinfo('attachhead');
		?>
	  
	</head>
<body>
<div class="header">
<div class="wapper">
	<h1 style="display: inline;">Ecust Online Judge</h1>
	<span style="float: right; display: inline;">Theme:&nbsp;&nbsp;<a href="javascript:oj.theme('Default')">Default</a>&nbsp;&nbsp;<a href="javascript:oj.theme('Green(simple)')">Green(simple)</a>&nbsp;&nbsp;<a href="javascript:oj.theme('Blue(simple)')">Blue(simple)</a>&nbsp;&nbsp;<a href="javascript:oj.theme('Purple(simple)')">Purple(simple)</a>&nbsp;&nbsp;</span>
</div>
<table border=1 class="table_list">
	<tr>
	<th width=20%>Online Judge</th>
	<th width=20%>Problem Set</th>
	<th width=20%>Authors</th>
	<th width=20%>Online Contests</th>
	<th>User</th>
	</tr>
	<tr>
	<td>
		<a href="./">Home Page</a><br />
		<a href="bbs.php">Web Board</a><br />
		<a href="faq.php">F.A.Q</a><br />
	</td>
	<td>
<?php
	if($_SESSION['is_admin']){
		echo '<a href="admin_addproblem.php">Add problem</a><br/>';
		echo '<a href="admin_rejudge.php">Rejudge</a><br/>';
	}
?>
		<a href='problemlist.php'>Problems</a><br />
		<a href="status.php">Status</a><br />
		<form action="problemshow.php" method="get">
			<font color=blue>Prob. ID</font>
			<input size=6 name="pro_id" />&nbsp;
			<input type=submit value="Go" />
		</form>
	</td>
	<td>
		<a href="register.php">Register</a><br/>
		<a href='ranklist.php'>Authors ranklist</a>
	</td>
	<td>
<?php
	if($_SESSION['is_admin']){
		echo '<a href="admin_addcontest.php">Add contest</a><br/>';
	}
	$h_query='select contest_id from contest where start_time<=now() and end_time>=now()';
	$h_ret=oj_query($h_query);
	$h_row=mysql_fetch_row($h_ret);
	if(isset($h_row[0]))
		echo '<a href="contestlist.php?show=Current" style="color:red">Current Contest</a><br/>';
	else
		echo '<a href="contestlist.php?show=Current">Current Contest</a><br />';
?>
		
		<a href='contestlist.php?show=Scheduled'>Scheduled Contest</a><br />
		<a href='contestlist.php?show=Past'>Past Contest</a><br />
	</td>
	<td>
<?php
	if(!isset($_SESSION['user_name']) && isset($_COOKIE['oj_user'])){
		$usrInfo = explode('\.', $_COOKIE['oj_user']);
		if(count($usrInfo) >= 3 && $usrInfo[0] == 'Y')
			oj_login (str_replace ('&DOT;', '.', $usrInfo[1]), str_replace ('&DOT;', '.', $usrInfo[2]));
	}
	if(isset($_SESSION['user_name'])){
		echo "<a class='user' href='user.php?user_id={$_SESSION['user_id']}'>" . htmlspecialchars($_SESSION['user_name']) . "</a><br>";
		$h_query="select count(*) from mails where to_user_id={$_SESSION['user_id']}";
		$h_ret=oj_query($h_query);
		$h_row=mysql_fetch_row($h_ret);
		$h_sum_mail=$h_row[0];
		$h_query="select count(*) from mails where to_user_id={$_SESSION['user_id']} and readed=0";
		$h_ret=oj_query($h_query);
		$h_row=mysql_fetch_row($h_ret);
		$h_new_mail=$h_row[0];
		if($h_new_mail>0)$h_new_mail="<b>$h_new_mail</b>";
		echo "<a href='maillist.php'>Mail:$h_sum_mail($h_new_mail)</a><br>";
?>
<a href='modifyuser.php'>Update&nbsp;your&nbsp;Info</a><br />
<a href="logout.php?url=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">Logout</a>
<?php
	}else{
?>
<form id="userLoginForm" action="login.php" method="post" onsubmit="return check_login()">
<input type=hidden name='url' value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']);?>"/>
<table border=0>
	<tr>
		<td>User&nbsp;ID:</td>
		<td><input name="name" id="login_name"/></td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input id="login_pwd_ori" type="password"/><input name="pwd" id="login_pwd" type="hidden"/></td>
	</tr>
	<tr>
		<td><input name="submit" type="submit" value="Login"/></td>
		<td><input type="checkbox" checked="checked" id="login_rememberme" />Remember Me</td>
	</tr>
</table>
</form>
<script src="include/md5.js"></script>
<script language="javascript">
(function (){
	var flag = oj.cookie("oj_user") || "Y..";
	if(flag[0] == "N")
		document.getElementById("login_rememberme").checked = false;
})();
function check_login() {
	if(oj.autologin)
		return true;
	var usr = document.getElementById('login_name');
	if (usr.value.length <= 0) {
		alert("Please input your User ID.");
		return false;
	}
	var ps = document.getElementById('login_pwd');
	var psO = document.getElementById('login_pwd_ori');
	if (psO.value.length <= 0) {
		alert("Please input your Password.");
		return false;
	}
	ps.value = MD5(psO.value);

	try{
		var flag = oj.cookie("oj_user") || "Y..";
		flag = flag.split(".");
		if(document.getElementById("login_rememberme").checked)
			oj.cookie("oj_user", "Y." + usr.value.replace(/\./g, "&DOT;") + "." + ps.value.replace(/\./g, "&DOT;"), {expires: 30});
		else {
			oj.cookie("oj_user", null);
			oj.cookie("oj_user", "N..");
		}
	} catch (e) {}
	return true;
}
</script>
<?php
	}
?>
	</td>
	</tr>
</table>
</div>
