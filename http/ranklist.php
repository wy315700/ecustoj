<?php
//$_GET['top']:显示大于该值的人
//$_GET['size']:一次显示多少人

	require_once 'include/function.php';

	set_ojinfo('title', 'ECUST Online Judge - Ranklist');

	include 'include/header.php';
	Draw_search_box('user');
	
	$rank_top=0;
	$rank_size=$ranklist_limit;
	if(isset($_GET['top']))$rank_top=$_GET['top'];
	if(isset($_GET['size']))$rank_size=$_GET['size'];
	
	$query="select user_id,name,submits,solved,nick_name from users order by solved desc,submits asc,user_id asc limit $rank_top,$rank_size";
	$ret=oj_query($query);
	
?>

<table border=1 class='table_list row_hover'>
	<tr>
		<th>Rank</th>
		<th>Name</th>
		<th>Nick&nbsp;name</th>
		<th>Solved</th>
		<th>Submit</th>
	</tr>
<?php
	$rank_i=$rank_top;
	while($row=mysql_fetch_assoc($ret)){
		$rank_i++;
		$rank_uid    =$row['user_id'];
		$rank_name   =htmlspecialchars($row['name']);
		$rank_nick   =htmlspecialchars($row['nick_name']);
		$rank_solved =$row['solved'];
		$rank_submit =$row['submits'];
		echo '<tr>';
		echo "<td>$rank_i</td>";
		echo "<td><a href='user.php?user_id=$rank_uid'>$rank_name</a></td>";
		echo "<td>$rank_nick</td>";
		echo "<td>$rank_solved</td>";
		echo "<td>$rank_submit</td>";
		echo '</tr>';
	}

?>
</table>
<center>
	[<a href="ranklist.php">Top</a>]&nbsp;
<?php
	if($rank_top>0){
		$rank_prev=$rank_top-$rank_size;
		if($rank_prev<0)$rank_prev=0;
		echo "[<a href='ranklist.php?top=$rank_prev&size=$rank_size'>Previous Page</a>]&nbsp;";
	}
	
	$rank_next=$rank_top+$rank_size;	
	if($rank_i==$rank_next){
		echo "[<a href='ranklist.php?top=$rank_next&size=$rank_size'>Next Page</a>]&nbsp;";
	}
?>
</center>

<?php
	include 'include/footer.php';
?>

