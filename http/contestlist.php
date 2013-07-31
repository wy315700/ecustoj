<?php
//$_GET['top']指定contest_id小于该值
//$_GET['show']指定显示哪类比赛,默认all,{'Scheduled'|'Past'|'Current'|'All'}

	require_once 'include/function.php';

	set_ojinfo('title', 'ECUST Online Judge - Contest List');

	include 'include/header.php';
?>

<table class="table_list wide_list" border=1>
	<tr>
		<th>ID</th>
		<th width=60%>Title</th>
		<th>Start time</th>
		<th>Type</th>
		<th>State</th>
	</tr>
<?php
	$query="select *,start_time>now() as pending,end_time<now() as ended from contest ";
	$where='';
	if(isset($_GET['top'])){
		$clist_top=intval(get_to_mysql($_GET['top']));
		$where="contest_id<$clist_top ";
	}
	if(isset($_GET['show']))$clist_show=$_GET['show'];
	if($clist_show=='Scheduled'){
		if(strlen($where)>0)$where=$where . 'and ';
		$where=$where . 'start_time>now() ';
	}else if($clist_show=='Past'){
		if(strlen($where)>0)$where=$where . 'and ';
		$where=$where . 'end_time<now() ';
	}else if($clist_show=='Current'){
		if(strlen($where)>0)$where=$where . 'and ';
		$where=$where . 'end_time>now() and start_time<now() ';
	}else $clist_show='All';
	if(strlen($where)>0)$where='where ' . $where;
	$query=$query . $where . "order by contest_id desc limit $contest_limit ";
	$ret=oj_query($query);
	$is_admin=(isset($_SESSION['user_id'])&&permission_admin($_SESSION['user_id']));
	while($row=mysql_fetch_assoc($ret)){
		if(!isset($clist_max)){
			$clist_max=$row['contest_id'];
			$clist_min=$row['contest_id'];
		}else $clist_min=$row['contest_id'];
		$clist_contest_id=$row['contest_id'];
		echo '<tr>';
		echo "<td>$clist_contest_id</td>";

		$clist_title=htmlspecialchars($row['title']);
		echo "<td align=left><a href='showcontest.php?contest_id=$clist_contest_id'>$clist_title</a>";
		if($is_admin){
			echo "<a href='admin_editcontest.php?contest_id=$clist_contest_id'>[Edit]</a>";
		}
		echo '</td>';

		echo "<td>{$row['start_time']}</td>";
		if($row['ispub']!=0)$clist_type='<font color=blue>Public</font>';
		else $clist_type='<font color=red>Private</font>';
		echo "<td>$clist_type</td>";
		if($row['pending']!=0)$clist_state='Pending';
		else if($row['ended']!=0)$clist_state='Ended';
		else $clist_state='Running';
		echo "<td>$clist_state</td>";
		echo '</tr>';
	}
	
?>
</table>
<center>
<?php
	echo "[<a href='contestlist.php?show=$clist_show'>Top</a>]&nbsp;";
	if(isset($clist_max)){
		$clist_max=$clist_max+$contest_limit+1;
		echo "[<a href='contestlist.php?show=$clist_show&top=$clist_max'>Previous</a>]&nbsp;";
		echo "[<a href='contestlist.php?show=$clist_show&top=$clist_min'>Next</a>]&nbsp;";
	}
?>
</center>
<?php
	include 'include/footer.php';
?>

