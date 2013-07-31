<?php
	require_once 'include/function.php';
	
	if(isset($_GET['user_id'])){
		$user_id=intval(get_to_mysql($_GET['user_id']));
	}else if(isset($_GET['user_name'])){
		$user_name=get_to_mysql($_GET['user_name']);
		$query="select user_id from users where name='$user_name'";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if(empty($row[0]))redirect("error.php?msg=" . urlencode("No such User."));
		$user_id=$row[0];
	}else if(isset($_SESSION['user_id'])){
		$user_id=mysql_real_escape_string($_SESSION['user_id']);
	}else{
		redirect("error.php?msg=" . urlencode("No such User."));
	}
	
	$query="select name,emailaddr,submits,solved,last_login_time,reg_time,nick_name,school,fromaddr from users where user_id='$user_id'";
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);
	if(empty($row['name']))redirect("error.php?msg=" . urlencode("No such User."));
	$user_name  =htmlspecialchars($row['name']);
	$user_email =htmlspecialchars($row['emailaddr']);
	$user_submit=htmlspecialchars($row['submits']);
	$user_solved=htmlspecialchars($row['solved']);
	$user_login =htmlspecialchars($row['last_login_time']);
	$user_reg   =htmlspecialchars($row['reg_time']);
	$user_nick  =htmlspecialchars($row['nick_name']);
	$user_school=htmlspecialchars($row['school']);
	$user_addr  =htmlspecialchars($row['fromaddr']);
	
function userinfo_display($left,$right){
	echo '<tr>';
	echo "<td>$left</td>";
	echo "<td class='bottomline'>$right</td>";
	echo '</tr>';
}
	set_ojinfo('title', "ECUST Online Judge - User $user_name");
	include 'include/header.php';
?>
<table align=center>
	<tr>
		<td colspan=2 style='text-align:center;'><h2>
		<?php echo '<a href="mailsend.php?name=' . urlencode($row['name']) . '">' . $user_name . '</a>';?>
		</h2></td>
	</tr>
<?php
	userinfo_display('Nick&nbsp;name:',$user_nick);
	userinfo_display('Last&nbsp;login&nbsp;time:',$user_login);
	userinfo_display('Register&nbsp;time:',$user_reg);
	userinfo_display('Solved:',"<a href='status.php?user_id=$user_id&result=1'>$user_solved</a>");
	userinfo_display('Submissions:',"<a href='status.php?user_id=$user_id'>$user_submit</a>");
	userinfo_display('Email:',$user_email);
	userinfo_display('School:',$user_school);
	userinfo_display('Come&nbsp;from:',$user_addr);
?>
</table>
<table align=center class='table_list' style='width:80%'>
<?php
	$query="select pro_id,result from user_submit where user_id='$user_id' order by pro_id asc";
	$ret=oj_query($query);
	$user_solved_count=0;
	$user_submit_count=0;
	while($row=mysql_fetch_row($ret)){
		if($row[1]==0){
			$user_submit_pro[$user_submit_count]=$row[0];
			$user_submit_count++;
		}else{
			$user_solved_pro[$user_solved_count]=$row[0];
			$user_solved_count++;
		}
	}
	
	echo '<tr><th>Problems solved:</th></tr>';
	echo '<tr><td>';
	for($i=0;$i<$user_solved_count;$i++){
		echo "<a href='problemshow.php?pro_id={$user_solved_pro[$i]}'>{$user_solved_pro[$i]}</a>&nbsp; ";
	}
	echo '</tr></td>';
	echo '<tr><th>Problems tried but failed:</th></tr>';
	echo '<tr><td>';
	for($i=0;$i<$user_submit_count;$i++){
		echo "<a href='problemshow.php?pro_id={$user_submit_pro[$i]}'>{$user_submit_pro[$i]}</a>&nbsp; ";
	}
	echo '</tr></td>';
?>
</table>

<?php
	include 'include/footer.php';
?>

