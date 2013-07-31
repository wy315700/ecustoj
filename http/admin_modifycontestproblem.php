<?php
//$_GET['action']操作{Add|Delete}
//$_GET['contest_id']
//$_GET['pro_id']
//$_GET['new_id']仅add需要该值
//$_GET['new_cnt']仅add多个题目时需要该值

	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first'));
	if(!permission_admin($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Access denied'));
	if(!isset($_GET['contest_id']))redirect('error.php?msg=' . urlencode('Please select contest'));
	$contest_id=get_to_mysql($_GET['contest_id']);
	$query="select contest_id from contest where contest_id=$contest_id";
	$ret=oj_query($query);
	$row=mysql_fetch_row($ret);
	if(!isset($row[0]))redirect('error.php?msg=' . urlencode('No such contest'));
	
	if($_GET['action']=='Add'){
		$pro_id=get_to_mysql($_GET['pro_id']);
		$new_id=get_to_mysql($_GET['new_id']);
		$new_cnt=get_to_mysql($_GET['new_cnt']);
		while($new_cnt>0){
			$query="select pro_id from contest_problems where contest_id=$contest_id and (pro_id=$pro_id or new_id=$new_id)";
			$ret=oj_query($query);
			$row=mysql_fetch_row($ret);
			if(isset($row[0]))redirect('error.php?msg=' . urlencode('The problem id or problem id in contest already exsist'));
			$query="insert into contest_problems (contest_id,pro_id,new_id) values ($contest_id,$pro_id,$new_id)";
			$ret=oj_query($query);
			
			$new_cnt--;
			$pro_id++;
			$new_id++;
		}
	}else if($_GET['action']=='Delete'){
		$pro_id=get_to_mysql($_GET['pro_id']);
		$query="delete from contest_problems where contest_id=$contest_id and pro_id=$pro_id";
		$ret=oj_query($query);
	}
	redirect('showcontest.php?contest_id=' . $_GET['contest_id']);
?>
