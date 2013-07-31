<?php
//$_GET['pro_id']:题目编号
//以下是可选:
//$_GET['contest_id']比赛中的题目
//$_GET['order']:按{时间(time)|内存(memory)|代码(code)|提交时间(submit)}排序
//$_GET['reverse']:相反顺序排序
//$_GET['top']:rank大于该值开始

	require_once 'include/function.php';
	
	if(!isset($_GET['pro_id']))redirect('error.php?msg=' . urlencode('No such problem'));
	$pro_id=intval(get_to_mysql($_GET['pro_id']));
	$contest_id=intval(get_to_mysql($_GET['contest_id']));

	$query="select title,accepted,submits,solved_users,submit_users,banned from problems where pro_id=$pro_id";
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);
	if(!isset($row['title']))redirect('error.php?msg=' . urlencode('No such problem'));
	$pro_banned=$row['banned'];

function status_draw_statistics($left,$right){
	echo '<tr>';
	echo '<td>' . htmlspecialchars($left) . '</td>';
	echo '<td>' . $right . '</td>';
	echo '</tr>';
}
function status_draw_each_result(){
	global $pro_id,$result_array;
	foreach($result_array as $key=>$val){
		if($val!='All'&&$val!='Waiting'){
			$query="select count(*) from submit_status where result=$key and pro_id=$pro_id";
			$ret=oj_query($query);
			$row=mysql_fetch_row($ret);
			if($row[0]!=0)
				status_draw_statistics($val,"<a href='status.php?pro_id=$pro_id&result=$key'>{$row[0]}</a>");
		}
	}
}
	
	$pro_sort_pre0='pro_id=' . $pro_id;
	if(!empty($contest_id))$pro_sort_pre0=$pro_sort_pre0 . '&contest_id=' . $contest_id;
	$pro_sort_pre  ='problemstatus.php?' . $pro_sort_pre0;
	$pro_sort_id   ='&order=submit';
	$pro_sort_mem  ='&order=memory';
	$pro_sort_time ='&order=time';
	$pro_sort_code ='&order=code';
	$pro_sort_mark =array(0=>'',1=>'',2=>'',3=>'',);
	if(isset($_GET['order'])){
		if($_GET['order']=='submit'){
			if(!isset($_GET['reverse']))$pro_sort_id  =$pro_sort_id   . '&reverse=1';
			$pro_sort_mark_sel=0;
		}else if($_GET['order']=='time'){
			if(!isset($_GET['reverse']))$pro_sort_time=$pro_sort_time . '&reverse=1';
			$pro_sort_mark_sel=2;
		}else if($_GET['order']=='code'){
			if(!isset($_GET['reverse']))$pro_sort_code=$pro_sort_code . '&reverse=1';
			$pro_sort_mark_sel=3;
		}else{
			if(!isset($_GET['reverse']))$pro_sort_mem =$pro_sort_mem  . '&reverse=1';
			$pro_sort_mark_sel=1;
		}
		if(!isset($_GET['reverse']))$pro_sort_mark[$pro_sort_mark_sel]='▲';
		else $pro_sort_mark[$pro_sort_mark_sel]='▼';
	}
	$pro_page_top=$pro_sort_pre;
	if(isset($_GET['order']))$pro_page_top=$pro_page_top . '&order=' . $_GET['order'];
	if(isset($_GET['reverse']))$pro_page_top=$pro_page_top . '&reverse=1';

	set_ojinfo('title', 'ECUST Online Judge - Problem Status');
	
	include 'include/header.php';
?>

<table width=100%>
	<tr>
		<td colspan=2><h2>
<?php
	echo "<a href='problemshow.php?$pro_sort_pre0'>$pro_id&nbsp;-&nbsp;" . htmlspecialchars($row['title']) . '</a>';
?>
		</h2></td>
	</tr>
	<tr>
		<td>
<table class='table_list' border=1>
	<tr>
		<th colspan=2>Statistics</th>
	</tr>
<?php
	status_draw_statistics('Total Submissions',"<a href='status.php?pro_id=$pro_id'>{$row['submits']}</a>");
	status_draw_statistics('Users (Submitted)',$row['submit_users']);
	status_draw_statistics('Users (Solved)',$row['solved_users']);
	if(!($pro_banned&&!$_SESSION['is_admin'])){
		status_draw_each_result();
	}
?>
</table>
		</td>
		<td>
<table class='table_list wide_list' border=1>
	<tr>
		<th>Rank</th>
		<th><a href="<?php echo $pro_sort_pre . $pro_sort_id; ?>">Run&nbsp;ID<?php echo $pro_sort_mark[0];?></a></th>
		<th>User</th>
		<th><a href="<?php echo $pro_sort_pre . $pro_sort_mem; ?>">Memory<?php echo $pro_sort_mark[1];?></a></th>
		<th><a href="<?php echo $pro_sort_pre . $pro_sort_time; ?>">Time<?php echo $pro_sort_mark[2];?></a></th>
		<th>Language</th>
		<th><a href="<?php echo $pro_sort_pre . $pro_sort_code; ?>">Code&nbsp;Length<?php echo $pro_sort_mark[3];?></a></th>
		<th>Submit&nbsp;Time</th>
	</tr>
<?php
	$query='select submit_status.*,users.name from submit_status '
		. 'inner join users on submit_status.user_id=users.user_id '
		. "where submit_status.pro_id=$pro_id and result=1 ";
	if(isset($_GET['reverse'])){
		$pro_asc='desc';
		$pro_desc='asc';
	}else{
		$pro_asc='asc';
		$pro_desc='desc';
	}
	$pro_order="order by cost_time $pro_asc,cost_mem $pro_asc,code_length $pro_asc,submit_id $pro_asc ";
	if(isset($_GET['order'])){
		if($_GET['order']=='memory'){
			$pro_order="order by cost_mem $pro_asc,cost_time $pro_asc,code_length $pro_asc,submit_id $pro_asc ";
		}else if($_GET['order']=='code'){
			$pro_order="order by code_length $pro_asc,cost_time $pro_asc,cost_mem $pro_asc,submit_id $pro_asc ";
		}else if($_GET['order']=='submit'){
			$pro_order="order by submit_id $pro_asc ";
		}
	}
	$pro_limit="limit $status_limit ";
	$pro_rank=1;
	if(isset($_GET['top'])){
		$pro_limit_top=get_to_mysql($_GET['top']);
		$pro_limit="limit $pro_limit_top,$status_limit ";
		$pro_rank=$pro_limit_top+1;
	}
	$pro_page_pre=$pro_rank-$status_limit-1;
	if($pro_page_pre<0)$pro_page_pre=0;
	$pro_page_next=$pro_rank+$status_limit-1;
	
	//$query=$query . $pro_order . $pro_limit;
	$query='select accept.*,count(*) as cnt from ( ' . $query . $pro_order . ') as accept group by accept.user_id ' . $pro_order . $pro_limit;
	$ret=oj_query($query);
	while($row=mysql_fetch_assoc($ret)){
		$pro_stat_hide=($pro_banned&&$row['user_id']!=$_SESSION['user_id']&&!$_SESSION['is_admin']);
		
		echo '<tr>';
		echo '<td>' . $pro_rank . '</td>';
		if($row['cnt']>1){
			echo "<td><a href='status.php?user_id={$row['user_id']}&pro_id=$pro_id&result=1'>{$row['submit_id']}({$row['cnt']})</a></td>";
		}else{
			echo '<td>' . $row['submit_id'] . '</td>';
		}
		echo '<td><a href="user.php?user_id=' . $row['user_id'] . '">' . htmlspecialchars($row['name']) . '</a></td>';
		if($pro_stat_hide){
			echo '<td></td><td></td>';
		}else{
			echo '<td>' . $row['cost_mem'] . 'K</td>';
			echo '<td>' . $row['cost_time'] . 'MS</td>';
		}
		echo '<td>' . $language_array[$row['language']] . '</td>';
		if($pro_stat_hide)
			echo '<td></td>';
		else if((isset($_SESSION['user_id'])&&$_SESSION['user_id']==$row['user_id'])||$_SESSION['is_admin'])
			echo "<td><a href='showcode.php?submit_id={$row['submit_id']}'>{$row['code_length']}B</a></td>";
		else
			echo '<td>' . $row['code_length'] . 'B</td>';
		echo '<td>' . htmlspecialchars($row['submit_time']) . '</td>';
		echo '</tr>';
		$pro_rank++;
	}
	
	
?>
</table>
<center>
[<a href="<?php echo $pro_page_top ;?>">Top</a>]
[<a href="<?php echo $pro_page_top . '&top=' . $pro_page_pre;?>">Previous</a>]
[<a href="<?php echo $pro_page_top . '&top=' . $pro_page_next;?>">Next</a>]
</center>
		</td>
	</tr>
</table>

<?php
	include 'include/footer.php';
?>

