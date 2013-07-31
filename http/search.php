<?php
//$_GET['field']={name|email|nick|school|from}|{title|source|tag},默认title
//$_GET['text']搜索的东西,对tag而言:mark_id
//$_GET['order']=对题目搜索:{id|title|solved|ac|submit|ratio|source},默认solved

	require_once 'include/function.php';
	
	if(empty($_GET['field']))redirect('error.php?msg=' . urlencode('Please select field'));
	$search_field=get_to_mysql($_GET['field']);
	$search_text =get_to_mysql($_GET['text']);
	$search_url='search.php?text=' . urlencode($search_text) . '&field=' . urlencode($search_field);
	
	$search_table=$search_array[$search_field];
	if(!isset($search_table))$search_table=$search_array['title'];
	if($search_table[1]=='user_mark'){
		if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first'));
		if(!is_numeric($search_text))redirect('error.php?msg=' . urlencode('Failed to search'));
	}

	set_ojinfo('title', 'ECUST Online Judge - Search');

	include 'include/header.php';
?>
<?php
	Draw_search_box($search_field,get_to_html($_GET['text']));
	echo '<table class="table_list row_hover" border=1>';
	if($search_table[1]=='users'){
		$query='select user_id,name,emailaddr,submits,solved,nick_name,school,fromaddr '
			. "from users where {$search_table[0]} like '%$search_text%' "
			. 'order by solved desc,submits asc ';
		$ret=oj_query($query);
		echo '<tr>';
		echo '<th>No.</th>';
		echo '<th>User ID</th>';
		echo '<th>Nick name</th>';
		echo '<th>Email</th>';
		echo '<th>School</th>';
		echo '<th>Come from</th>';
		echo '<th>Solved</th>';
		echo '<th>Submits</th>';
		echo '</tr>';
		$i=1;
		while($row=mysql_fetch_assoc($ret)){
			echo '<tr>';
			echo "<td>$i</td>";
			echo "<td><a href='user.php?user_id={$row['user_id']}'>" . htmlspecialchars($row['name']) . '</td>';
			echo '<td>' . htmlspecialchars($row['nick_name']) . '</td>';
			echo '<td>' . htmlspecialchars($row['emailaddr']) . '</td>';
			echo '<td>' . htmlspecialchars($row['school']) . '</td>';
			echo '<td>' . htmlspecialchars($row['fromaddr']) . '</td>';
			echo "<td>{$row['solved']}</td>";
			echo "<td>{$row['submits']}</td>";
			echo '</tr>';
			$i++;
		}
		echo '</table>';
	}else{
		$query='select banned,pro_id,title,accepted,submits,solved_users,source ';
		if($search_table[1]=='problems'){
			$query=$query . "from problems where {$search_table[0]} like '%$search_text%' ";
		}else{
			$query=$query . 'from problems where exists ( select pro_id from user_mark '
				. "where user_mark.user_id=" . intval($_SESSION['user_id']) . " and mark_id=$search_text "
				. 'and problems.pro_id = user_mark.pro_id )';
		}
		if($_GET['order']=='id'){
			$query=$query . 'order by pro_id asc ';
		}else if($_GET['order']=='source'){
			$query=$query . 'order by source asc ';
		}else if($_GET['order']=='title'){
			$query=$query . 'order by title asc ';
		}else if($_GET['order']=='ratio'){
			$query=$query . 'order by accepted/submits asc ';
		}else if($_GET['order']=='ac'){
			$query=$query . 'order by accepted asc ';
		}else if($_GET['order']=='submit'){
			$query=$query . 'order by submits asc ';
		}else{
			$query=$query . 'order by solved_users asc ';
		}
		$ret=oj_query($query);
		echo '<tr>';
		echo "<th><a href='$search_url&order=id'>ID</a></th>";
		echo "<th><a href='$search_url&order=title'>Title</a></th>";
		echo "<th><a href='$search_url&order=solved'>Solved</a></th>";
		echo "<th><a href='$search_url&order=ratio'>Ratio</a>" .
			"(<a href='$search_url&order=ac'>AC</a>/<a href='$search_url&order=submit'>Submit</a>)</th>";
		if(isset($_SESSION['user_id']))echo '<th>Tags</th>';
		echo "<th><a href='$search_url&order=source'>Source</a></th>";
		echo '</tr>';
		$i=0;
		while($row=mysql_fetch_assoc($ret)){
			if($row['banned']&&!$_SESSION['is_admin'])continue;
			$pro_id    =$row['pro_id'];
			$pro_title =htmlspecialchars($row['title']);
			$pro_source=htmlspecialchars($row['source']);
			if($row['submits']==0)$pro_ratio=0;
			else $pro_ratio=(int)($row['accepted']/$row['submits']*100);
			echo '<tr>';
			echo "<td>$pro_id</td>";
			echo "<td><a href='problemshow.php?pro_id=$pro_id'>$pro_title</a></td>";
			echo "<td>{$row['solved_users']}</td>";
			echo "<td class=mid>$pro_ratio%({$row['accepted']}/{$row['submits']})</td>";
			if(isset($_SESSION['user_id'])){
				echo '<td>';
				Draw_tags($pro_id);
				echo '</td>';
			}
			echo "<td><a href='search.php?field=source&text=$pro_source'>$pro_source</a></td>";
			echo '</tr>';
			$i++;
		}
		echo '</table>';
		echo "<center><h2>Total problem:$i</h2></center>";
	}

	include 'include/footer.php';
?>

