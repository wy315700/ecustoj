<?php
//��ʾ��Ŀ�б�
//$_GET['rank_top']����ָ����һ����Ŀ��ID
//$_GET['rank_size']����ָ����ʾ��Ŀ����
//$_GET['rank_sort']����ָ����ʾʱ��������:1-ID,2-AC,3-submit,4-radio
//��ban����Ŀadmin�ɼ�


	require_once 'include/function.php';

	set_ojinfo('title', 'ECUST Online Judge - Problem Sets');

	include 'include/header.php';
	
	$query = "select max(pro_id),min(pro_id) from problems";
	$ret = oj_query($query);
	$row = mysql_fetch_row($ret);
	$max_pro = $row[0];
	$min_pro = $row[1];
	
	$rank_top = $min_pro;
	$rank_size = 100;
	$rank_sort = 1;
	
	if(isset($_GET['rank_top']))$rank_top = get_to_mysql(intval($_GET['rank_top']));
	if(isset($_GET['rank_size']))$rank_size = get_to_mysql(intval($_GET['rank_size']));
	if(isset($_GET['rank_sort']))$rank_sort = get_to_mysql($_GET['rank_sort']);
	
	$query_t = "select pro_id, title, add_time, banned, accepted, submits from problems ";
	$rank_max = $rank_top+$rank_size;
	$query_t = $query_t."where pro_id>=$rank_top and pro_id<$rank_max order by ";
	if($rank_sort==1){
		$query = $query_t."pro_id asc ";
	}else if($rank_sort==2){
		$query = $query_t."accepted asc, submits asc ";
	}else if($rank_sort==3){
		$query = $query_t."submits asc, accepted asc ";
	}else if($rank_sort==4){
		$query = $query_t."accepted/submits asc, accepted asc, submits asc ";
	}
	//echo $rank_top."<br/>";
	//echo $query."<br/>";
	$ret = oj_query($query);
	echo "<center><h2>Volume ";
	$next_top=$min_pro;
	//for($index=1; $index<=(int)(($max_pro-1)/$rank_size)+1; $index++){
	for($index=1; $next_top<=$max_pro; $index++){
		//$next_top = $rank_size * ($index - 1);
		echo " <a href='problemlist.php?rank_top=$next_top'>$index</a>";
		$next_top+=$rank_size;
	}
	echo "</h2></center>";
	Draw_search_box();
?>

<table border=1 class="table_list row_hover">
	<tr>
		<th width="10%"><a href="problemlist.php?rank_sort=1&rank_top=<?php echo $rank_top;?>" style="color:#FFCC00">ID</a></th>
		<th>Title</th>
		<?php if(isset($_SESSION['user_id']))echo '<th>Tag</th>'; ?>
		<th width="20%"><a href="problemlist.php?rank_sort=4&rank_top=<?php echo $rank_top;?>">Ratio</a>
					(<a href="problemlist.php?rank_sort=2&rank_top=<?php echo $rank_top;?>" style="color:#FF0066">AC</a>/
					<a href="problemlist.php?rank_sort=3&rank_top=<?php echo $rank_top;?>" style="color:#00FF33">Submit</a>)</th>
		<th width="20%">Date</th>
	</tr>
	<?php
		$is_admin=$_SESSION['is_admin'];
		while($row=mysql_fetch_row($ret)){
			$banned = $row[3];
			if($banned!=0){
				if(!$is_admin)continue; //����
			}
			$pro_id = $row[0];
			$title = htmlspecialchars($row[1]);
			$add_time = $row[2];
			$accepted = $row[4];
			$submits = $row[5];
			echo "<tr>";
				echo "<td class='mid'>$pro_id</td>";
				echo "<td><a href='problemshow.php?pro_id=$pro_id'>$title";
				if($is_admin){
					echo "<a href='admin_editproblem.php?pro_id=$pro_id'>[Edit]</a>";
					if($banned!=0)echo "<a href='admin_editproblem.php?do_unban=$pro_id' style='color:#f70;'>[Banned]</a>";
					echo "<a href='admin_rejudge.php?pro_id=$pro_id&confirm=1'>[Rejudge]</a>";
				}
				echo "</a></td>";
				if(isset($_SESSION['user_id'])){
					echo '<td align=center>';
					Draw_tags($pro_id);
					echo '</td>';
				}
				if($submits==0)$radio = 0;
				else $radio = (int)($accepted/$submits*100);
				echo "<td class='mid'>$radio%($accepted/$submits)</td>";
				echo "<td class='mid'>$add_time</td>";
			echo "</tr>";
		}
	?>
</table>

<?php
	$next_top = $rank_top+$rank_size;
	echo "<center>[<a href='problemlist.php?rank_top=0'>Top</a>] [<a href='problemlist.php?rank_top=$next_top'>Next</a>]</center>";
	include 'include/footer.php';
?>
