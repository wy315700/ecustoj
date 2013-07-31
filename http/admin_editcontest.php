<?php
//$_GET['contest_id']待修改的比赛
//下面是提交后的
//$_POST['title']
//$_POST['description']
//$_POST['start_time']字符串yyyy-mm-dd hh:mm:ss
//$_POST['end_time']
//$_POST['att_pwd']如果为空则是public的比赛
//$_POST['att_pwd2']无用

	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first'));
	if(!permission_admin($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Access denied'));
	if(!isset($_GET['contest_id']))redirect('error.php?msg=' . urlencode('Please select contest'));
	$editc_id=get_to_html($_GET['contest_id']);
	
	if(isset($_POST['title'])){
		$editc_title=get_to_mysql($_POST['title']);
		$editc_desc =get_to_mysql($_POST['description']);
		$editc_start=get_to_mysql($_POST['start_time']);
		$editc_end  =get_to_mysql($_POST['end_time']);
		$editc_pwd  =get_to_mysql($_POST['att_pwd']);
		
		if(empty($editc_pwd))$editc_pub=1;
		else $editc_pub=0;
		
		$query="update contest set title='$editc_title',start_time='$editc_start',end_time='$editc_end',"
			. "description='$editc_desc',ispub=$editc_pub,att_pwd='$editc_pwd' "
			. "where contest_id=$editc_id ";
		$ret=oj_query($query);
		
		redirect("showcontest.php?contest_id=$editc_id");
	}
	$query='select * from contest where contest_id=' . $editc_id;
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);

	set_ojinfo('title', 'ECUST Online Judge - Edit contest');

	include 'include/header.php';

function draw_fillin($left,$input,$name,$prop='',$right='',$value=''){
	echo '<tr>';
	echo '<td>' . htmlspecialchars($left) . '</td>';
	echo "<td><$input name='$name' $prop>" . htmlspecialchars($value) . "</$input>$right</td>";
	echo '</tr>';
}

?>

<form method=post action="admin_editcontest.php?contest_id=<?php echo $editc_id;?>" onsubmit="return check_editc();">
<table align=center>
	<tr>
		<td>Contest&nbsp;ID:</td>
		<td><?php echo $editc_id;?></td>
	</tr>
<?php
	draw_fillin('Title:','input','title'," maxlength=200 value='{$row['title']}'");
	draw_fillin('Description:','textarea','description','','',$row['description']);
	draw_fillin('Start time:','input','start_time'," value='{$row['start_time']}'");
	draw_fillin('End time:','input','end_time'," value='{$row['end_time']}'");
	draw_fillin('Password:','input','att_pwd',' type=password id="editc_pwd" ');
	draw_fillin('Repeat password:','input','att_pwd2',' type=password id="editc_pwd2" ');
?>
	<tr>
		<td></td>
		<td><input type='submit' name='submit' value='Submit'/></td>
	</tr>
</table>
</form>
<script src="include/md5.js"></script>
<script language="javascript">
function check_editc() {
	var ps = document.getElementById('editc_pwd');
	var ps2= document.getElementById('editc_pwd2');
	if (ps.value!=ps2.value) {
		alert("Password do not match.");
		return false;
	}
	if (ps.value.length <= 0 && ps.value.length <= 0 ) return true;
	ps.value = MD5(ps.value);
	ps2.value = MD5(ps2.value);
	return true;
}
</script>

<?php
	include 'include/footer.php';
?>

