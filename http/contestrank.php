<?php
//$_GET['contest_id']比赛编号

	require_once 'include/function.php';

	if(!isset($_GET['contest_id']))redirect('error.php?msg=' . urlencode('Please select a contest'));
	$contest_id=intval(get_to_mysql($_GET['contest_id']));
	$query="select *,start_time>now() as pending,end_time<now() as ended from contest where contest_id=" . $contest_id;
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);
	if(!isset($row['ispub']))redirect('error.php?msg=' . urlencode('No such contest'));
	if($row['ispub']==0){
		$contest_type='<font color=red>Private</font>';
		if($_SESSION['contest' . $contest_id]!=1)redirect("showcontest.php?contest_id=" . $contest_id);
	}else $contest_type='<font color=blue>Public</font>';
	
	$contest_title=htmlspecialchars($row['title']);
	$contest_desc =htmlspecialchars($row['description']);
	$contest_start=htmlspecialchars($row['start_time']);
	$contest_end  =htmlspecialchars($row['end_time']);
	if($row['pending']!=0)$contest_state='<font color=green>Pending</font>';
	else if($row['ended']!=0)$contest_state='<font color=red>Ended</font>';
	else $contest_state='<font color=blue>Running</font>';
	
	$is_ended=$row['ended'];
	$contest_start_time=strtotime($row['start_time']);

function contest_ranklist_cmp($a,$b){
	if($a['solved']>$b['solved'])return -1;
	if($a['solved']<$b['solved'])return 1;
	if($a['penalty']>$b['penalty'])return 1;
	if($a['penalty']<$b['penalty'])return -1;
	return 0;
}

function get_contest_ranklist(){
	global $contest_id,$contest_pro,$contest_start_time,$contest_summary;
//ret[user_i]['user_id']
//ret[user_i]['nick_name']
//ret[user_i]['solved']
//ret[user_i]['penalty']
//ret[user_i][pro_i][0:is_accepted|1:failed_submissions|2:first_accepted]
//contest_summary[pro_i][0:ac_count|1:submit_count|2:first_ac]
	//problems
	$query="select pro_id,new_id from contest_problems where contest_id=$contest_id order by new_id asc";
	$ret=oj_query($query);
	$pro_count=0;
	while($row=mysql_fetch_row($ret)){
		$contest_pro[$pro_count]=$row;
		$pro_index[$row[0]]=$pro_count;
		$pro_count++;
	}
	//summary
	for($i=0;$i<$pro_count;$i++){
		$contest_summary[$i][0]=0;//ac_count
		$contest_summary[$i][1]=0;//submit_count
		$contest_summary[$i][2]=-1;//first_ac
	}
	//each submitted users
	$user_count=0;
	$query="select distinct user_id from submit_status where contest_id=" . $contest_id;
	$ret=oj_query($query);
	while($row=mysql_fetch_row($ret)){
		//user_id,nick_name
		$user_id=$row[0];
		$query="select name,nick_name from users where user_id=$user_id";
		$ret2=oj_query($query);
		$row2=mysql_fetch_row($ret2);
		if(strlen($row2[1])==0)$user_name=$row2[0];
		else $user_name=$row2[1];
		$contest_rank[$user_count]['user_id']=$user_id;
		$contest_rank[$user_count]['nick_name']=$user_name;
		//init
		for($i=0;$i<$pro_count;$i++){
			$contest_rank[$user_count][$i][0]=0;//accept
			$contest_rank[$user_count][$i][1]=0;//fails
			$contest_rank[$user_count]['solved']=0;
			$contest_rank[$user_count]['penalty']=0;
		}
		//submissions
		$query="select pro_id,result,submit_time from submit_status "
			. "where contest_id=$contest_id and user_id=$user_id "
			. 'order by submit_id asc';
		$ret2=oj_query($query);
		while($row2=mysql_fetch_assoc($ret2)){
			$now_pro_index=$pro_index[$row2['pro_id']];
			if($contest_rank[$user_count][$now_pro_index][0]==0){
				//submissions before accept
				if($row2['result']==1){
					$contest_rank[$user_count][$now_pro_index][0]=1;
					$contest_rank[$user_count]['solved']++;
					$now_ac_time=floor((strtotime($row2['submit_time'])-$contest_start_time)/60);
					$contest_rank[$user_count][$now_pro_index][2]=$now_ac_time;
					if($contest_summary[$now_pro_index][2]<0||$contest_summary[$now_pro_index][2]>$now_ac_time)
						$contest_summary[$now_pro_index][2]=$now_ac_time;
				}else $contest_rank[$user_count][$now_pro_index][1]++;
			}
			if($row2['result']==1)$contest_summary[$now_pro_index][0]++;
			$contest_summary[$now_pro_index][1]++;
		}
		//penalty
		for($i=0;$i<$pro_count;$i++){
			if($contest_rank[$user_count][$i][0]!=0){
				$contest_rank[$user_count]['penalty']+=$contest_rank[$user_count][$i][2]+$contest_rank[$user_count][$i][1]*20;
			}
		}
		$user_count++;
	}
	//sort
	if(isset($contest_rank))
		usort($contest_rank,'contest_ranklist_cmp');
	
	return $contest_rank;
}

	set_ojinfo('title', 'ECUST Online Judge - Contest Ranklist');
	include 'include/header.php';
?>

<table align=center>
	<tr>
		<td colspan=2><h2>
<?php echo "<a href='showcontest.php?contest_id=$contest_id'>$contest_title</a>";?>
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
</table>
<table class='table_list wide_list' border=1>
<?php
	$contest_user=get_contest_ranklist();
	$contest_pro_count =count($contest_pro);
	$contest_user_count=count($contest_user);
	//table head
	echo '<tr>';
	echo '<th>Rank</th>';
	echo '<th>Nick name</th>';
	echo '<th>Solved</th>';
	echo '<th>Penalty</th>';
	for($i=0;$i<$contest_pro_count;$i++){
		$pro_id=$contest_pro[$i][0];
		$new_id=$contest_pro[$i][1];
		echo "<th><a href='problemshow.php?pro_id=$pro_id";
		if(!$is_ended)echo "&contest_id=$contest_id";
		echo "'>$new_id</a></th>";
	}
	echo '</tr>';
	//ranklist
	for($i=0;$i<$contest_user_count;$i++){
		$user_id=$contest_user[$i]['user_id'];
		echo '<tr>';
		echo '<td>' . ($i+1) . '</td>';
		echo "<td><a href='user.php?user_id=$user_id'>{$contest_user[$i]['nick_name']}</a></td>";
		echo "<td><a href='status.php?user_id=$user_id&contest_id=$contest_id'>{$contest_user[$i]['solved']}</a></td>";
		echo "<td>{$contest_user[$i]['penalty']}</td>";
		for($j=0;$j<$contest_pro_count;$j++){
			echo '<td>';
			$state=$contest_user[$i][$j];
			if($state[0]!=0)echo $state[2]; //accepted and penalty
			if($state[1]!=0)echo "({$state[1]})"; //failed submissions
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '<tr>';
	echo '<th colspan=4>Summary: AC/Submit/First AC Time</th>';
	for($i=0;$i<$contest_pro_count;$i++){
		echo '<th>';
		if($contest_summary[$i][2]<0)echo '-/-/-';
		else echo $contest_summary[$i][0] . '/' . $contest_summary[$i][1] . '/' . $contest_summary[$i][2];
		echo '</th>';
	}
	echo '</tr>';
?>
</table>

<?php
	include 'include/footer.php';
?>

