<?php
//$_GET['contest_id']比赛编号
//下面是需要密码时,提交密码
//$_POST['contest_id']
//$_POST['att_pwd']

	require_once 'include/function.php';
	
	if(isset($_POST['contest_id'])){
		$contest_id=intval(get_to_mysql($_POST['contest_id']));
		$contest_pwd=get_to_mysql($_POST['att_pwd']);
		$query="select att_pwd from contest where contest_id=$contest_id";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if(isset($row[0])&&$row[0]==$contest_pwd)$_SESSION['contest' . $contest_id]=1;
		redirect("showcontest.php?contest_id=$contest_id");
	}
	
	if(!isset($_GET['contest_id']))redirect('contestlist.php');
	$contest_id=intval(get_to_mysql($_GET['contest_id']));
	$query="select *,start_time>now() as pending,end_time<now() as ended from contest where contest_id=$contest_id ";
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);
	if(!isset($row['title']))redirect('error.php?msg=' . urlencode('No such contest.'));
	
	$contest_title=htmlspecialchars($row['title']);
	$contest_start=htmlspecialchars($row['start_time']);
	$contest_end  =htmlspecialchars($row['end_time']);
	if(stristr($row['description'],'<center>'))
		$contest_desc = str_replace("\n","<br>\n",$row['description']);
	else
		$contest_desc = str_replace("\n","<br>\n",htmlspecialchars($row['description']));
	$contest_permit=true;
	if($row['ispub']==0){
		$contest_type='<font color=red>Private</font>';
		if($_SESSION['contest' . $contest_id]!=1)$contest_permit=false;
	}else $contest_type='<font color=blue>Public</font>';

	if($row['pending']!=0)$contest_state='<font color=green>Pending</font>';
	else if($row['ended']!=0)$contest_state='<font color=red>Ended</font>';
	else $contest_state='<font color=blue>Running</font>';
	$is_admin=(isset($_SESSION['user_id'])&&permission_admin($_SESSION['user_id']));
	$is_pending=$row['pending'];
	$is_ended=$row['ended'];

	set_ojinfo('title', "ECUST Online Judge - Contest - $contest_title");
	include 'include/header.php';
?>
<table align=center>
	<tr>
		<td colspan=2><h2>
<?php
	echo $contest_title;
	if($is_admin){
		echo "<a class='edit' href='admin_editcontest.php?contest_id=$contest_id'>[Edit]</a>";
	}
?>
		</h2></td>
	</tr>
	<tr>
		<td colspan=2 class=mid><?php echo $contest_desc;?></td>
	</tr>
	<tr>
		<td>Start time:</td>
		<td><?php echo $contest_start;?></td>
	</tr>
	<tr>
		<td>End time:</td>
		<td><?php echo $contest_end;?></td>
	</tr>
	<tr>
		<td>Current Server Time:</td>
		<td><?php echo date('Y-m-d H:i:s',time());?></td>
	</tr>
	<tr>
		<td>Type:<?php echo $contest_type;?></td>
		<td>State:<?php echo $contest_state;?></td>
	</tr>
	<tr>
		<td colspan=2>
<?php
	if($contest_permit){
		if($is_pending==0||$is_admin){
			echo '<table class="table_list wide_list" border=1>';
			echo '<tr>';
			if(isset($_SESSION['user_id']))echo '<th>Tags</th>';
			echo '<th>ID</th>';
			echo '<th>Title</th>';
			echo '</tr>';
			$query='select contest_problems.*,problems.title from contest_problems '
				. 'inner join problems on problems.pro_id=contest_problems.pro_id '
				. "where contest_id=$contest_id "
				. 'order by new_id asc';
			$ret=oj_query($query);
			while($row=mysql_fetch_assoc($ret)){
				echo '<tr>';
				$contest_pro=$row['pro_id'];
				if(isset($_SESSION['user_id'])){
					echo '<td align=center>';
					Draw_tags($contest_pro,$contest_id);
					echo '</td>';
				}
				echo "<td>{$row['new_id']}</td>";
				echo "<td align=left><a href='problemshow.php?pro_id=$contest_pro";
				if(!$is_ended)echo "&contest_id=$contest_id";
				echo "'>" . htmlspecialchars($row['title']) . '</a>';
				if($is_admin&&$is_pending){
					echo "<a href='admin_modifycontestproblem.php?action=Delete&contest_id=$contest_id&pro_id=$contest_pro'>[Delete]</a>";
				}
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '<center>';
			echo "[<a href='status.php?contest_id=$contest_id'>Status</a>]";
			echo "[<a href='contestrank.php?contest_id=$contest_id'>Standing</a>]";
			echo "[<a href='conteststatistics.php?contest_id=$contest_id'>Statistics</a>]";
			echo '</center>';
		}
	if($is_admin&&$is_pending){
?>
<form action='admin_modifycontestproblem.php' method='get'>
<input name='contest_id' type='hidden' value="<?php echo $contest_id;?>"/>
<input name='action' type='hidden' value='Add'/>
<table align=center>
	<tr>
		<td colspan=2 align=center><font color=green>Add problem to contest</font></td>
	</tr>
	<tr>
		<td>Problem&nbsp;ID:</td>
		<td><input name='pro_id'/></td>
	</tr>
	<tr>
		<td>Problem&nbsp;ID&nbsp;in&nbsp;contest:</td>
		<td><input name='new_id'/></td>
	</tr>
	<tr>
		<td title='It will add (pro_id,new_id),(pro_id+1,new_id+1),..,(pro_id+n,new_id+n)'>Continuous&nbsp;problem:</td>
		<td><input name='new_cnt' value='1'/></td>
	</tr>
	<tr>
		<td></td>
		<td><input name='submit' type='submit' value='Add'/></td>
	</tr>
</table>
</form>
<?php }
	}else{
?>
<form action="" method="post" onsubmit="return check_contest()">
<input name="contest_id" type="hidden" value="<?php echo $contest_id;?>"/>
<table border=0>
	<tr>
		<td>Password:</td>
		<td><input name="att_pwd" id="contest_pwd" type="password"/></td>
	</tr>
	<tr>
		<td><input name="submit" type="submit" value="Submit"/></td>
		<td><input name="reset" type="reset" value="Reset"/></td>
	</tr>
</table>
</form>
<script src="include/md5.js"></script>
<script language="javascript">
function check_contest() {
	var ps = document.getElementById('contest_pwd');
	if (ps.value.length <= 0) {
		alert("Please input your Password.");
		return false;
	}
	ps.value = MD5(ps.value);
	return true;
}
</script>
<?php
	}
?>
		</td>
	</tr>
</table>
<div style="text-align: center;"><?php if($contest_permit && 0 != $is_ended && $is_admin) { ?><div><a href="admin/download.php?cid=<?php echo $contest_id; ?>" target="_blank">Get All Accepted Source</a></div>
<span style="color: Grey; font-style: Italic;">Notice:<br />Everybody can download it after any of administrators has built the source package.<br />But only administrators can see the "Get All Accepted Source" link.</span></div><?php }
	include 'include/footer.php';
?>

