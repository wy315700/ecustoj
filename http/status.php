<?php
//可选项:
//$_GET['user_id']   用户编号或名字
//$_GET['pro_id']    题号
//$_GET['contest_id']比赛号(暂没有完成)
//$_GET['top']       提交编号小于该值
//$_GET['bottom']    提交编号大于该值,如果top存在,此项忽略
//$_GET['result']    judge状态,没有值表示全部
//$_GET['language']  语言,没有值表示全部
//$_GET['size']      一页显示多少

	require_once 'include/function.php';
	
	$query='select submit_status.*,users.name,problems.banned '
		. 'from submit_status inner join users on submit_status.user_id=users.user_id '
		. 'inner join problems on submit_status.pro_id=problems.pro_id ';
	$where='';
	$get_page='';
	$size=$status_limit;
	$is_prev=0;
	
function status_mysql_condition($get, $field){
	global $where,$get_page;
	if(strlen($_GET[$get])>0){
		$temp=intval(get_to_mysql($_GET[$get])); 
		if(strlen($where)>0)$where=$where . ' and ';
		$where=$where . " (submit_status.$field $temp) ";
		if($get!='top'&&$get!='bottom'){
			if(strlen($get_page)>0)$get_page=$get_page . '&';
			$get_page=$get_page . $get . '=' . intval(urlencode(stripslashes($_GET[$get])));
		}
	}
}

	if(strlen($_GET['user_id'])>0){
		if(is_numeric($_GET['user_id'])){
			$status_user_id=get_to_mysql($_GET['user_id']);
			$status_user_name=user_id_to_name($status_user_id);
		}
		if((!is_numeric($_GET['user_id']))||strlen($status_user_name)<=0){
			$status_user_name=get_to_mysql($_GET['user_id']);
			$status_user_id=name_to_user_id($status_user_name);
		}
		if(empty($status_user_id))redirect('error.php?msg=' . urlencode('No such user'));
		$where=" (submit_status.user_id=$status_user_id) ";
		$get_page='user_id=' . $status_user_id;
	}
	
	status_mysql_condition('pro_id','pro_id=');
	status_mysql_condition('contest_id','contest_id=');
	status_mysql_condition('result','result=');
	status_mysql_condition('language','language=');
	status_mysql_condition('top','submit_id<');
	if(strlen($_GET['top'])==0)status_mysql_condition('bottom','submit_id>');
	if(strlen($_GET['size'])>0)$size=get_to_mysql($_GET['size']);
	
	if(strlen($where)>0)$query=$query . 'where ' . $where;
	if(strlen($_GET['top'])==0&&strlen($_GET['bottom'])>0){
		$is_prev=1;
		$query=$query . ' order by submit_id asc ';
	}else{
		$query=$query . ' order by submit_id desc ';
	}
	$query=$query . " limit $size ";
	$ret=oj_query($query);

	set_ojinfo('title', 'ECUST Online Judge - Status list');

	include 'include/header.php';
?>

<form action="" method="get">
User&nbsp;ID:
	<input name="user_id"
	<?php
		if(isset($status_user_name))
			echo ' value="' . htmlspecialchars($status_user_name) . '"';
	?> />&nbsp;
Problem&nbsp;ID:
	<input name="pro_id"
	<?php
		if(!empty($_GET['pro_id']))
			echo ' value="' . get_to_html($_GET['pro_id']) . '"';
	?> />&nbsp;
Result:
<?php Draw_Options($result_array,'result',$_GET['result']); ?>
Language:
<?php Draw_Options($language_array,'language',$_GET['language']); ?>
<input type="submit" value="Go" />
</form>
<table class='table_list wide_list' border=1>
	<tr>
		<th>Run&nbsp;ID</th>
		<th>User</th>
		<th>Problem</th>
		<th>Result</th>
		<th>Memory</th>
		<th>Time</th>
		<th>Language</th>
		<th>Code&nbsp;Length</th>
		<th>Submit&nbsp;Time</th>
	</tr>
<?php

function status_draw_result($result,$submit_id){
	global $result_array;
	$str=$result_array[$result];
	switch($result){
		case 7://ce
			$str="<a href='showcompileinfo.php?submit_id=$submit_id'>$str</a>";
		case 0:case 2:case 14:case 15://wait,pe,system error,compiling
			$str='<font class="not_bad">' . $str . '</font>';
			break;
		case 1://ac
			$str='<font class="good">' . $str . '</font>';
			break;
		default:
			$str='<font class="bad">' . $str . '</font>';
			break;
	}
	return $str;
}

function status_draw_line($row){
	global $status_top,$status_bottom,$language_array;
	$submit_id=$row['submit_id'];
	if($status_top==-1||$submit_id>$status_top)$status_top=$submit_id;
	if($status_bottom==-1||$submit_id<$status_bottom)$status_bottom=$submit_id;
	echo '<tr>';
	echo "<td>$submit_id</td>";
	echo "<td><a href='user.php?user_id={$row['user_id']}'>" . htmlspecialchars($row['name']) . '</a></td>';
	echo "<td><a href='problemshow.php?pro_id={$row['pro_id']}'>{$row['pro_id']}</a></td>";
	if($row['banned']&&$row['user_id']!=$_SESSION['user_id']&&!($_SESSION['is_admin']||$_SESSION['is_judger'])){
		echo '<td></td><td></td><td></td>';
	}else{
		echo '<td>' . status_draw_result($row['result'],$submit_id) . '</td>';
		echo "<td>{$row['cost_mem']}K</td>";
		echo "<td>{$row['cost_time']}MS</td>";
	}
	echo "<td>{$language_array[$row['language']]}</td>";
	if(isset($_SESSION['user_id'])&&($_SESSION['user_id']==$row['user_id']||$_SESSION['is_admin']))
		echo "<td><a href='showcode.php?submit_id=$submit_id'>{$row['code_length']}B</a></td>";
	else if($row['banned']&&!$_SESSION['is_judger'])
		echo '<td></td>';
	else
		echo "<td>{$row['code_length']}B</td>";
	echo "<td>{$row['submit_time']}</td>";
	echo '</tr>';
}
	$status_top=-1;
	$status_bottom=-1;
	if($is_prev==1){
		$row_count=mysql_num_rows($ret);
		for($i=$row_count-1;$i>=0;$i--){
			mysql_data_seek($ret,$i);
			$row=mysql_fetch_assoc($ret);
			status_draw_line($row);
		}
	}else{
		while($row=mysql_fetch_assoc($ret)){
			status_draw_line($row);
		}
	}
	
?>
</table>
<center>
<?php
	echo "[<a href='status.php?$get_page'>Top</a>]&nbsp;";
	if($status_top!=-1){
		echo "[<a href='status.php?bottom=$status_top&$get_page'>Previous</a>]&nbsp;";
	}
	if($status_bottom!=-1){
		echo "[<a href='status.php?top=$status_bottom&$get_page'>Next</a>]&nbsp;";
	}
?>
</center>

<?php
	include 'include/footer.php';
?>

