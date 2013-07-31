<?php
//$_GET['contest_id']比赛编号

	require_once 'include/function.php';
	
	if(!isset($_GET['contest_id']))redirect('error.php?msg=' . urlencode('Please select a contest'));
	$contest_id=intval(get_to_mysql($_GET['contest_id']));
	$query="select title,ispub,end_time<now() as ended from contest where contest_id=" . $contest_id;
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);
	if(!isset($row['ispub']))redirect('error.php?msg=' . urlencode('No such contest'));
	if($row['ispub']==0){
		if($_SESSION['contest' . $contest_id]!=1)redirect("showcontest.php?contest_id=$contest_id");
	}
	
	$contest_title=htmlspecialchars($row['title']);
	$is_ended=$row['ended'];
	
function get_contest_statistics(){
	global $contest_id,$pro_count;
//ret[pro_i]['result'][result]
//ret[pro_i]['lang'][language]
//ret[pro_i]['total']
//ret[pro_i]['pro_id']
//ret[pro_i]['new_id']
//ret['total']['result'][result]
//ret['total']['lang'][language]
//ret['total']['total']
	//problems
	$query="select pro_id,new_id from contest_problems where contest_id=$contest_id order by new_id asc";
	$ret=oj_query($query);
	$pro_count=0;
	while($row=mysql_fetch_row($ret)){
		$contest_stat[$pro_count]['pro_id']=$row[0];
		$contest_stat[$pro_count]['new_id']=$row[1];
		$pro_count++;
	}
	//each problem
	for($i=0;$i<$pro_count;$i++){
		$pro_id=intval($contest_stat[$i]['pro_id']);
		//result
		$query='select result,count(result) from submit_status ' . 
			"where contest_id=$contest_id and pro_id=" . $pro_id .
			' and result!=15 and result!=0 ' . //waiting,compiling
			'group by result ';
		$ret=oj_query($query);
		while($row=mysql_fetch_row($ret)){
			$contest_stat[$i]['result'][$row[0]]=$row[1];
			$contest_stat[$i]['total']+=$row[1];
			$contest_stat['total']['result'][$row[0]]+=$row[1];
			$contest_stat['total']['total']+=$row[1];
		}
		//language
		$query='select language,count(language) from submit_status ' .
			"where contest_id=$contest_id and pro_id=" . $pro_id .
			' group by language ';
		$ret=oj_query($query);
		while($row=mysql_fetch_row($ret)){
			$contest_stat[$i]['lang'][$row[0]]+=$row[1];
			$contest_stat['total']['lang'][$row[0]]+=$row[1];
		}
	}
	return $contest_stat;
}

	set_ojinfo('title', 'ECUST Online Judge - Contest Statistics');
	include 'include/header.php';
?>

<table align=center>
	<tr>
		<td><h2>
<?php echo "<a href='showcontest.php?contest_id=$contest_id'>$contest_title</a>";?>
		</h2></td>
	</tr>
</table>
<table class='table_list wide_list' border=1>
<?php
	$contest_stat=get_contest_statistics();
	//header
	echo '<tr>';
	echo '<th></th>';
	for($i=0;$i<$pro_count;$i++){
		$pro_id=$contest_stat[$i]['pro_id'];
		$new_id=$contest_stat[$i]['new_id'];
		echo "<th><a href='problemshow.php?pro_id=$pro_id";
		if(!$is_ended)echo "&contest_id=$contest_id";
		echo "'>$new_id</a></th>";
	}
	echo '<th>Total</th>';
	echo '</tr>';
	//results
	for($i=1;$i<15;$i++){
		echo '<tr>';
		echo '<td>' . $result_array[$i] . '</td>';
		for($j=0;$j<$pro_count;$j++){
			echo '<td>' . $contest_stat[$j]['result'][$i] . '</td>';
		}
		echo '<td>' . $contest_stat['total']['result'][$i] . '</td>';
		echo '</tr>';
	}
	//total
	echo '<tr>';
	echo '<th>Total</th>';
	for($i=0;$i<$pro_count;$i++){
		echo '<th>' . $contest_stat[$i]['total'] . '</th>';
	}
	echo '<th>' . $contest_stat['total']['total'] . '</th>';
	echo '</tr>';
	//language
	for($i=1;$i<=2;$i++){
		echo '<tr>';
		echo '<td>' . $language_array[$i] . '</td>';
		for($j=0;$j<$pro_count;$j++){
			echo '<td>' . $contest_stat[$j]['lang'][$i] . '</td>';
		}
		echo '<td>' . $contest_stat['total']['lang'][$i] . '</td>';
		echo '</tr>';
	}
?>
</table>

<?php
	include 'include/footer.php';
?>

