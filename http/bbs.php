<?php
//��ʾ��������
//$_GET['top']����ָ��reply_id���ó����ֵ
//$_GET['pro_id']ָ������ĳ��Ŀ������
//��ǰ��ҳ��û��
	require_once ('include/function.php');
	include ('include/posts.php');

	set_ojinfo('title', 'ECUST Online Judge - Messages');

	include ('include/header.php');
?>
<table class='table_list'>
<tr><td>
<ul>
<?php	
	$query='select discuss_info.*, LENGTH(discuss_info.content) as content_len, users.name '
		. 'from discuss_info inner join users on discuss_info.user_id=users.user_id '
		. 'where discuss_info.topic_id=discuss_info.message_id ';
	if(isset($_GET['pro_id'])){
		$bbs_pro_id=intval($_GET['pro_id']);
		$query=$query . "and discuss_info.pro_id=$bbs_pro_id ";
	}
	if(isset($_GET['top'])){
		$bbs_top=intval($_GET['top']);
		$query=$query . "and reply_id<$bbs_top ";
	}
	$query=$query . 'order by reply_id desc ';
	$ret=oj_query($query);
	$bbs_count=0;
	$bbs_last_reply=0;
	$bbs_first_reply=0;
	
	while($row=mysql_fetch_assoc($ret)){
		echo '<li>';
		echo draw_single_post($row);
		echo draw_post_tree($row);
		echo '<hr/></li>';
		if($bbs_first_reply==0)$bbs_first_reply=$row['reply_id'];
		$bbs_last_reply=$row['reply_id'];
		if($bbs_count>=$bbs_limit)break;
	}
?>
</ul>
</td></tr>
</table>
<center>
<?php
	$bbs_top='bbs.php';
	$bbs_next='bbs.php?top=' . $bbs_last_reply;
	if(isset($_GET['pro_id'])){
		$bbs_next=$bbs_next . '&pro_id=' . $bbs_pro_id;
		$bbs_top=$bbs_top . '?pro_id=' . $bbs_pro_id;
	}
	echo "[<a href='$bbs_top'>Top</a>]&nbsp";
	echo "[<a href='$bbs_next'>Next</a>]&nbsp;";
?>
</center>
<?php
	draw_post_message(0,$bbs_pro_id,0);
?>

<?php
	include 'include/footer.php';
?>

