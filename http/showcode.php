<?php
//$_GET['submit_id']:�ύ���

	require_once 'include/function.php';
	
	if(!isset($_GET['submit_id']))redirect('status.php');
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first'));
	$code_id=get_to_mysql($_GET['submit_id']);
	$query="select user_id from submit_status where submit_id=" . intval($code_id);
	$ret=oj_query($query);
	$row=mysql_fetch_row($ret);
	if(!isset($row[0]))redirect('error.php?msg=' . urlencode('No such submission'));
	if($row[0]!=$_SESSION['user_id']&&(!$_SESSION['is_admin']))redirect('error.php?msg=' . urlencode('This is not your code'));
	
	$query="select code_content from code_info where submit_id=" . intval($code_id);
	$ret=oj_query($query);
	$row=mysql_fetch_row($ret);
	if(!isset($row[0]))redirect('error.php?msg=' . urlencode("Can't find the code"));

	set_ojinfo('title', 'ECUST Online Judge - View code');
	set_ojinfo('highlight');
	include 'include/header.php';
?>

<div id="highlight_styleswitcher" class="hl_ss"></div>
<div id="highlight_code">
<pre><code>
<?php
	echo htmlspecialchars($row[0]);
?>
</code></pre></div>
<center><a href='javascript:history.go(-1);'>Go Back</a></center>

<?php
	include 'include/footer.php';
?>

