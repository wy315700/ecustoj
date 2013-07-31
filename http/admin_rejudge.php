<?php
//$_GET['pro_id']题目号
//$_GET['confirm']表示需要经过确认

	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first'));
	if(!$_SESSION['is_admin'])redirect('error.php?msg=' . urlencode('Access denied'));
	if(!empty($_GET['pro_id'])){
		$pro_id=get_to_mysql($_GET['pro_id']);
		$query="select pro_id from problems where pro_id=$pro_id";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if(!isset($row[0]))redirect('error.php?msg=' . urlencode('No such problem'));
	}

	set_ojinfo('title', 'ECUST Online Judge - Rejudge');
	
	include 'include/header.php';
?>
<?php		
	if(empty($_GET['pro_id'])){
		echo '<center>';
		echo '<form action="admin_rejudge.php" method="get">';
		echo 'Problem ID:<input name="pro_id"><br>';
		echo '<input type="hidden" name="confirm" value="1">';
		echo '<input type="submit" value="Rejudge">';
		echo '</form>';
		echo '</center>';
	}else{
		
		if(isset($_GET['confirm'])){
			echo '<h2>';
			echo "Are you sure to rejudge the <a href='problemshow.php?pro_id=$pro_id'>Problem $pro_id</a>?";
			echo '</h2><br>';
			echo "<center><a href='admin_rejudge.php?pro_id=$pro_id'>Yes</a></center>";
		}else{
			$query='update users,user_submit set users.solved=users.solved-1,user_submit.result=0 '
				. 'where users.user_id=user_submit.user_id and user_submit.result=1 and '
				. "user_submit.pro_id=$pro_id ";
			oj_query($query);
		
			$query="update problems set accepted=0,solved_users=0 where pro_id=$pro_id";
			oj_query($query);
			
			$query="update submit_status set result=0 where pro_id=$pro_id";
			oj_query($query);
			
			$query='delete ce_info from ce_info,submit_status '
				. "where ce_info.submit_id=submit_status.submit_id and submit_status.pro_id=$pro_id ";
			oj_query($query);
			
			$query='insert into judge_queue '
				. 'select submit_id,0 as status from submit_status '
				. "where pro_id=$pro_id ";
			oj_query($query);
			echo '<h2>Rejudge started</h2>';
		}
	}
	
	include 'include/footer.php';
?>

