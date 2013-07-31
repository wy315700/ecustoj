<?php
	require_once 'include/function.php';

	if(!isset($_SESSION['user_id']))redirect("error.php?msg=".urlencode("Please longin first!"));
	$user_id = get_to_mysql($_SESSION['user_id']);
	$rank_top = 0;
	$rank_size = 20;
	if(isset($_GET['top']))$rank_top = $_GET['top'];
	if(isset($_GET['size']))$rank_size = $_GET['size'];
	
	$receive = true; //read receive mails
	if(isset($_GET['send']))$receive = false; //send box

	if($receive){
		$query = "select mail_id,from_user_id,mail_time,title,readed from mails "
			. "where to_user_id=" . intval($user_id) . " and reader_del=0 order by mail_time desc limit $rank_top,$rank_size";
		$ret = oj_query($query);
	}else{
		$query = "select mail_id,to_user_id,mail_time,title from mails "
			. "where from_user_id=" . intval($user_id) . " and writer_del=0 order by mail_time desc limit $rank_top,$rank_size";
		$ret = oj_query($query);
	}

	set_ojinfo('title', 'ECUST Online Judge - ShowMails');

	include 'include/header.php';
?>

<center>
<a href="maillist.php" <?php if($receive)echo "style='color:red'"?>>Inbox</a>&nbsp
<a href="maillist.php?send='true'" <?php if(!$receive)echo "style='color:red'"?>>Outbox</a>
<br>
<table border=1 class='table_list row_hover' style="width:60%">
	<tr>
		<th>No.</th>
		<th><?php if($receive)echo "From"; else echo "To"; ?></th>
		<th>Title</th>
		<th>Date</th>
	</tr>
	<?php
		$rank_i = $rank_top;
		while($row=mysql_fetch_row($ret)){
			$rank_i++;
			$mail_id   = $row[0];
			$user_id   = $row[1];
			$user_name = user_id_to_name($user_id);
			$mail_time = $row[2];
			$title     = htmlspecialchars($row[3]);
			if($receive)
				$readed = $row[4];
			else
				$url_sender='&sender=1';
			echo "<tr>";
				echo "<td>$rank_i</td>";
				echo '<td><a href="user.php?user_name=' . urlencode($user_name) . '">' . htmlspecialchars($user_name) . '</a></td>';
				$not_readed='';
				if($receive && $readed==0)$not_readed = 'style="font-weight:bold"';
				echo '<td ' . $not_readed . '><a href="mailshow.php?mail_id=' . $mail_id . $url_sender . '">' . $title . '</a></td>';
				echo "<td>$mail_time</td>";
			echo "</tr>";
		}
	?>
</table>
<?php
	echo "[<a href='mailsend.php'>Send</a>]&nbsp;";
	if ($rank_top != 0) {
		$qpage = $rank_top - $rank_size;
		if ($receive) {
			echo "[<a href='maillist.php?top=$qpage'>Previous</a>]&nbsp;";
		} else {
			echo "[<a href='maillist.php?top=$qpage&send=true'>Previous</a>]&nbsp;";
		}
	}
	if ($rank_i - $rank_top == $rank_size) {
		$qpage = $rank_top + $rank_size;
		if ($receive) {
			echo "[<a href='maillist.php?top=$qpage'>Next</a>]&nbsp;";
		} else {
			echo "[<a href='maillist.php?top=$qpage&send=true'>Next</a>]&nbsp;";
		}
	}
?>
</center>
<?php
	include 'include/footer.php';
?>
