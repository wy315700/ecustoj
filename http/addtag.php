<?php
//$_GET['pro_id']题目
//下面是提交后的
//$_GET['pro_id']
//$_POST['new_tag']
//admin的添加新tag:
//$_POST['mark_des']
//$_POST['fore_color_r'],g,b
//$_POST['back_color_r'],g,b

	require_once 'include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first.'));
	if(!isset($_GET['pro_id']))redirect('problemlist.php');
	$user_id=$_SESSION['user_id'];
	$pro_id=intval($_GET['pro_id']);
	$is_admin=(isset($_SESSION['user_id'])&&permission_admin($_SESSION['user_id']));
	
	if(strlen($_POST['mark_des'])>0){
		$new_mark_des  =get_to_mysql($_POST['mark_des']);
		$new_fore_color=$_POST['fore_color_r']*65536+$_POST['fore_color_g']*256+$_POST['fore_color_b'];
		$new_back_color=$_POST['back_color_r']*65536+$_POST['back_color_g']*256+$_POST['back_color_b'];
		$query='insert into config_mark (mark_des,fore_color,back_color) values '
			. "('$new_mark_des',$new_fore_color,$new_back_color)";
		$ret=oj_query($query);
	}
	
	$query="select mark_id from user_mark where user_id=" . intval($user_id) . " and pro_id=" . intval($pro_id);
	$ret=oj_query($query);
	while($row=mysql_fetch_row($ret)){
		$tag_marked[$row[0]]=1;
	}
	
	if(isset($_POST['action'])){
		$old_marked=$tag_marked;
		if(!empty($_POST['new_tag'])){
			foreach($_POST['new_tag'] as $key=>$val){
				if(isset($old_marked[(int)$val])){
					unset($old_marked[(int)$val]);
				}else{
					$query='insert into user_mark (user_id,pro_id,mark_id) values '
						. "($user_id,$pro_id,$val)";
					$ret=oj_query($query);
					$tag_marked[(int)$val]=1;
					//echo $query . '<br>';
				}
			}
		}
		if(!empty($old_marked)){
			foreach($old_marked as $key=>$val){
				$query="delete from user_mark where user_id=" . intval($user_id) . " and pro_id=" . intval($pro_id) . " and mark_id=$key";
				$ret=oj_query($query);
				unset($tag_marked[$key]);
				//echo $query . '<br>';
			}
		}
	}
	
	$query='select mark_id,count(user_id) from user_mark '
		. "where pro_id=" . intval($pro_id) . " group by mark_id";
	$ret=oj_query($query);
	while($row=mysql_fetch_row($ret)){
		$tag_count[$row[0]]=$row[1];
	}

	set_ojinfo('title', 'ECUST Online Judge - Add tag');
	include 'include/header.php';
?>

<form action="" method="post">
<table align=center width=100% >
<?php
	echo "<tr><td colspan='5'><h2><a href='problemshow.php?pro_id=$pro_id'>Tags of Problem $pro_id</a></h2></td></tr>";
	$query='select * from config_mark';
	$ret=oj_query($query);
	$i=0;
	while($row=mysql_fetch_assoc($ret)){
		if($i==0)echo '<tr>';
		$now_mark_id=$row['mark_id'];
		$now_tag_count=0;
		if(isset($tag_count[$now_mark_id]))$now_tag_count=$tag_count[$now_mark_id];
		echo '<td align=center width=20% title="';
		echo $now_tag_count . ' people marked this problem as ' . $row['mark_des'];
		echo '">';
			echo "<input type=checkbox name='new_tag[]' value='$now_mark_id' ";
			if(isset($tag_marked[$now_mark_id]))echo 'checked';
			echo '>';
			Draw_single_tag($row['mark_des'] . "($now_tag_count)",rgb_to_html($row['back_color']),rgb_to_html($row['fore_color']));
			
		echo '</td>';
		$i++;
		if($i==5){
			echo '</tr>';
			$i=0;
		}
	}
	if($i!=0)echo '</tr>';
?>
</table>
<center><input type=submit value='Update' name='action'></center>
</form>
<?php
	if(isset($_POST['action']))echo '<center color=blue>Tags has been updated.</center>';
	if($is_admin){
?>
<form action="" method="post" align=center>
Tag&nbsp;name:<input name='mark_des'><br>
Text&nbsp;color:R=<input name='fore_color_r' value=0>,G=<input name='fore_color_g' value=0>,B=<input name='fore_color_b' value=0><br>
Back&nbsp;color:R=<input name='back_color_r' value=255>,G=<input name='back_color_g' value=255>,B=<input name='back_color_b' value=255><br>
<input type=submit value='Add new tag'>
</form>
<?php
	}
	include 'include/footer.php';
?>

