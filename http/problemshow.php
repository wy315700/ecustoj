<?php
//$_GET['pro_id']参数指示要显示的题目ID
//$_GET['contest_id']比赛中的题目,可选

	require_once 'include/function.php';
	
	if(empty($_GET['pro_id']))redirect('error.php?msg='.urlencode('Problem not found!'));
	if(!is_numeric($_GET['pro_id']))redirect('search.php?field=title&text=' . urlencode(stripslashes($_GET['pro_id'])));
	$pro_id = intval(get_to_mysql($_GET['pro_id']));
	$is_admin=$_SESSION['is_admin'];
	if(!empty($_GET['contest_id'])){
		$contest_id=intval(get_to_mysql($_GET['contest_id']));
		$query="select start_time>now() from contest where contest_id=$contest_id ";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if((!isset($row[0]))||($row[0]&&!$is_admin))redirect('error.php?msg=' . urlencode('Hey, what are you doing?'));
		$query="select pro_id from contest_problems where contest_id=$contest_id and pro_id=$pro_id";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if(!isset($row[0]))redirect('error.php?msg=' . urlencode('The contest does not include the problem.'));
	}
	$query = "select title,description,input,output,sample_input,sample_output,"
		."hint,source,time_limit,case_time_limit,mem_limit,accepted,submits,banned,isspj from problems where pro_id=$pro_id";
	$ret = oj_query($query);
	$row = mysql_fetch_row($ret);
	if(!isset($row[0]))redirect("error.php?msg=".urlencode("Problem not found!"));

	$title = htmlspecialchars($row[0]);
	//$desc = htmlspecialchars($row[1]);
	$desc = $row[1];
	$input = $row[2];
	$output = $row[3];
	$sample_input = $row[4];
	$sample_output = $row[5];
	$hint = $row[6];

	$source = htmlspecialchars($row[7]);
	$time_limit = htmlspecialchars($row[8]);
	$case_time_limit = htmlspecialchars($row[9]);
	$mem_limit = htmlspecialchars($row[10]);
	$accepted = htmlspecialchars($row[11]);
	$submits = htmlspecialchars($row[12]);
	$banned = $row[13];
	$isspj = $row[14];
	
	if($banned!=0&&!isset($contest_id)){
		if(!$is_admin)redirect('error.php?msg=' . urlencode('Problem banned'));
	}

	$id = $pro_id;

	set_ojinfo('title', "ECUST Online Judge - Problem $id - $title");
	
	include 'include/header.php';
	
	echo '<div class="problem_content">';
	echo "<center>";
		echo "<p class='subtitle'>$title";
		if($is_admin)echo "<a href='admin_editproblem.php?pro_id=$id'>[Edit]</a>";
		echo '</p>';
		echo "<table>";
		echo "<tr><td>Time Limit: {$time_limit}MS </td><td width=10></td><td> Memory Limit: {$mem_limit}K</td></tr>";
		if($case_time_limit!=0&&$case_time_limit<$time_limit)echo "<tr><td colspan=3>Case Time Limit: {$case_time_limit}MS</td></tr>";
		if($isspj!=0)echo '<tr><td colspan=3><font color=red>Special judge</font></td></tr>';
		echo "<tr><td>Total Submits: $submits </td><td></td><td> Accepted: $accepted</td></tr>";
		echo '<tr><td colspan=3>';
			Draw_tags($id);
		echo '</td></tr>';
		echo "</table>";
	echo "</center>";
	echo "<p class='subtitle'>Description</p>";
	echo "<div class='content'>$desc</div>";
	if($input!=''){
		echo "<p class='subtitle'>Input</p>";
		echo "<div class='content'>$input</div>";
	}
	if($output!=''){
		echo "<p class='subtitle'>Output</p>";
		echo "<div class='content'>$output</div>";
	}
	echo "<p class='subtitle'>Sample Input</p>";
	echo "<div class='sample'>$sample_input</div>";
	echo "<p class='subtitle'>Sample Output</p>";
	echo "<div class='sample'>$sample_output</div>";
	if($hint!=""){
		echo "<p class='subtitle'>Hint</p>";
		echo "<div class='content'>$hint</div>";
	}
	if($source!=""&&!isset($contest_id)){
		echo "<p class='subtitle'>Source</p>";
		echo "<div class='content'><a href='search.php?field=source&text=" . urlencode($row[7]) . "'>$source</a></div>";
	}
	
	echo '</div>';
	
	echo '<br/><center>';
	echo '[<a href="javascript:history.go(-1);">Back</a>] ';
	echo "[<a href='problemsubmit.php?pro_id=$id&contest_id=$contest_id'>Submit</a>] ";
	echo "[<a href='problemstatus.php?pro_id=$id&contest_id=$contest_id'>Status</a>] ";
	echo "[<a href='bbs.php?pro_id=$id'>Discuss</a>] ";
	echo '</center>';
	include 'include/footer.php';
?>

