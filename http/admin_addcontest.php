<?php
//$_POST['title']
//$_POST['description']
//$_POST['start_time']字符串yyyy-mm-dd hh:mm:ss
//$_POST['end_time']
//$_POST['att_pwd']如果为空则是public的比赛
//$_POST['att_pwd2']无用

	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first'));
	if(!permission_admin($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Access denied'));
	
	if(isset($_POST['title'])){
		$addc_title=get_to_mysql($_POST['title']);
		$addc_desc =get_to_mysql($_POST['description']);
		$addc_start=get_to_mysql($_POST['start_time']);
		$addc_end  =get_to_mysql($_POST['end_time']);
		$addc_pwd  =get_to_mysql($_POST['att_pwd']);
		
		if(empty($addc_pwd))$addc_pub=1;
		else $addc_pub=0;
		
		$query='insert into contest (title,start_time,end_time,description,ispub,att_pwd) values '
			. "('$addc_title','$addc_start','$addc_end','$addc_desc',$addc_pub,'$addc_pwd') ";
		$ret=oj_query($query);
		
		$ret=oj_query('select last_insert_id()');
		$row=mysql_fetch_row($ret);
		redirect("showcontest.php?contest_id={$row[0]}");
	}

	set_ojinfo('title', 'ECUST Online Judge - Add contest');
	include 'include/header.php';

function draw_fillin($left,$input,$name,$prop='',$right=''){
	echo '<tr>';
	echo '<td>' . htmlspecialchars($left) . '</td>';
	echo "<td><$input name='$name' $prop></$input>$right</td>";
	echo '</tr>';
}

?>

<form method=post action="" onsubmit="return check_addc();">
<table align=center>
	<tr>
		<td>Contest&nbsp;ID:</td>
		<td>Auto</td>
	</tr>
<?php
	draw_fillin('Title:','input','title',' maxlength=200 ');
	draw_fillin('Description:','textarea','description');
	draw_fillin('Start time:','input','start_time',' value="' . date('Y-m-d H:i:00',time()) . '" ');
	draw_fillin('End time:','input','end_time',' value="' . date('Y-m-d H:i:00',time()) . '" ');
	draw_fillin('Password:','input','att_pwd',' type=password id="addc_pwd" ');
	draw_fillin('Repeat password:','input','att_pwd2',' type=password id="addc_pwd2" ');
?>
	<tr>
		<td></td>
		<td><input type='submit' name='submit' value='Submit'/></td>
	</tr>
</table>
</form>
<script src="include/md5.js"></script>
<script language="javascript">
function check_addc() {
	var ps = document.getElementById('addc_pwd');
	var ps2= document.getElementById('addc_pwd2');
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

