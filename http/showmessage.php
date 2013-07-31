<?php
//显示指定帖子
//$_GET['message_id']参数指定显示的帖子
//未完成

	require_once 'include/function.php';
	if(!isset($_GET['message_id']))redirect('bbs.php');
	
	include 'include/posts.php';
	
	set_ojinfo('title', 'ECUST Online Judge - Detail of message');

	include 'include/header.php';
?>
<table class='table_list'>
<tr><td>
<?php
	$message_id=intval($_GET['message_id']);
	$query='select discuss_info.*,users.name '
		. 'from discuss_info inner join users on discuss_info.user_id=users.user_id '
		. "where discuss_info.message_id=$message_id ";
	$ret=oj_query($query);
	$row=mysql_fetch_assoc($ret);
	echo '<center><h2>' . htmlspecialchars($row['title']) . '</h2></center>';
	echo '<p>';
	echo "Posted&nbsp;by&nbsp;<a class='user' href='user.php?user_id={$row['user_id']}'>" . htmlspecialchars($row['name']) . '</a>';
	echo "&nbsp;at&nbsp;{$row['reply_time']}";
	if($row['pro_id']!=0){
		echo "&nbsp;on&nbsp;<a class='problem' href='problemshow.php?pro_id={$row['pro_id']}'>Problem&nbsp;{$row['pro_id']}</a>&nbsp;";
	}
	echo '<br>';
	if($row['topic_id']!=$row['message_id']){
		$query='select discuss_info.title,discuss_info.reply_time,discuss_info.user_id,users.name '
			. 'from discuss_info inner join users on discuss_info.user_id=users.user_id '
			. "where discuss_info.message_id={$row['topic_id']}";
		$ret2=oj_query($query);
		$row2=mysql_fetch_assoc($ret2);
		echo 'In&nbsp;Reply&nbsp;To:';
		echo "<a href='showmessage?message_id={$row['topic_id']}'>" . htmlspecialchars($row2['title']) . '</a>';
		echo "Posted&nbsp;by&nbsp;<a class='user' href='user.php?user_id={$row2['user_id']}'>" . htmlspecialchars($row2['name']) . '</a>';
		echo "&nbsp;at&nbsp;{$row2['reply_time']}<br>";
	}
	echo '<hr><pre>';
	echo htmlspecialchars($row['content']);
	echo '</pre><hr><p>';
	echo 'Followed&nbsp;by:<br>';
	echo draw_post_tree($row);
	echo '<hr>';
	
?>
</td></tr>
</table>
<?php
	draw_post_message($message_id,$row['pro_id'],$row['contest_id']);
?>

<?php
	include 'include/footer.php';
?>

