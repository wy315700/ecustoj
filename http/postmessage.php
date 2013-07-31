<?php
//$_SESSION['user_id']发贴人
//$_POST['title']标题
//$_POST['content']内容
//下面是可选
//$_POST['problem_id']题目号
//$_POST['contest_id']比赛号
//$_POST['reply_id']回复该贴

	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first.'));
	$problem_id=0;
	$contest_id=0;
	$reply_id=0;
	if(isset($_POST['problem_id']))$problem_id=intval(get_to_mysql($_POST['problem_id']));
	if(isset($_POST['contest_id']))$contest_id=intval(get_to_mysql($_POST['contest_id']));
	if(isset($_POST['reply_id']))  $reply_id  =intval(get_to_mysql($_POST['reply_id']));
	$title  =get_to_mysql($_POST['title']);
	$content=get_to_mysql($_POST['content']);
	if(strlen($title)==0)$title='No title';
	
	$query='insert into discuss_info (user_id,pro_id,contest_id,title,content,reply_time) '
		. "values ({$_SESSION['user_id']},$problem_id,$contest_id,'$title','$content',now())";
	$ret=oj_query($query);
	$ret=oj_query('select last_insert_id()');
	$row=mysql_fetch_row($ret);
	$message_id=$row[0];
	if($reply_id==0)$reply_id=$message_id;
	$query="update discuss_info set reply_id=$message_id,topic_id=$reply_id where message_id=$message_id";
	$ret=oj_query($query);

	while($reply_id!=0){
		$query="update discuss_info set reply_id=$message_id where message_id=$reply_id";
		$ret=oj_query($query);
		$query="select topic_id from discuss_info where message_id=$reply_id";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if($reply_id==$row[0])break;
		$reply_id=$row[0];
	}
	
	redirect("showmessage.php?message_id=$message_id");

?>
