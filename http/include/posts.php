<?php

function draw_single_post($row,$show_proid=1){
	global $bbs_count;
	$bbs_count++;
	
	$bbs_title =htmlspecialchars($row['title']);
	$bbs_msgid =$row['message_id'];
	$bbs_len   =$row['content_len'];
	$bbs_usrid =$row['user_id'];
	$bbs_usr   =htmlspecialchars($row['name']);
	$bbs_time  =htmlspecialchars($row['reply_time']);
	$bbs_proid =$row['pro_id'];
	$bbs_conid =$row['contest_id'];
	//$bbs_ban
	$ret="<a href='showmessage.php?message_id=$bbs_msgid'>$bbs_title</a>";
	if($bbs_len==0)
		$ret=$ret . '<span class="no_content">(0)</span>';
	else
		$ret=$ret . "<span class='has_content'>($bbs_len)</span>";
	$ret=$ret . "<a class='user' href='user.php?user_id=$bbs_usrid'>$bbs_usr</a>&nbsp;";
	$ret=$ret . $bbs_time;
	if($bbs_conid!=0){
		//todo
	}
	if($bbs_proid!=0&&$show_proid!=0){
		$ret=$ret . "&nbsp;<a class='problem' href='problemshow.php?pro_id=$bbs_proid'>Problem&nbsp;$bbs_proid</a>&nbsp;";
	}
	return $ret;
}

function draw_post_tree($parent){
//不绘制自身
	$buff='';
	$query='select discuss_info.*, LENGTH(discuss_info.content) as content_len, users.name '
		. 'from discuss_info inner join users on discuss_info.user_id=users.user_id '
		. "where discuss_info.topic_id={$parent['message_id']} and discuss_info.message_id<>{$parent['message_id']} "
		. 'order by discuss_info.reply_id desc ';
	$ret=oj_query($query);
	if($row=mysql_fetch_assoc($ret)){
		$buff=$buff . '<ul>';
		do{
			$buff=$buff . '<li>' . draw_single_post($row,0) . draw_post_tree($row) . '</li>';
		}while($row=mysql_fetch_assoc($ret));
		$buff=$buff . '</ul>';
	}
	return $buff;
}

function draw_post_message($reply_id,$problem_id,$contest_id){
	if(!isset($_SESSION['user_id']))return;
	echo '<form method=post action="postmessage.php">';
	if($reply_id!=0)echo "<input type=hidden name='reply_id' value='$reply_id'/>";
	if($problem_id!=0)echo "<input type=hidden name='problem_id' value='$problem_id'/>";
	if($contest_id!=0)echo "<input type=hidden name='contest_id' value='$contest_id'/>";
?>
<table>
	<tr>
		<td>User:</td>
		<td><?php echo htmlspecialchars($_SESSION['user_name']);?></td>
	</tr>
	<tr>
		<td>Title:</td>
		<td><input name="title" maxlength=200></td>
	</tr>
	<tr>
		<td>Content:</td>
		<td><textarea name="content"></textarea></td>
	</tr>
	<tr>
		<td></td>
		<td><input name="submit" value="Post message" type="submit"></td>
	</tr>
</table>
</form>
<?php
}

?>
