<?php
//$_GET['submit_id']:提交编号

	require_once 'include/function.php';
	
	if(!isset($_GET['submit_id']))redirect('status.php');
	$ce_id=get_to_mysql($_GET['submit_id']);
	$query="select ce_info from ce_info where submit_id=" . intval($ce_id);
	$ret=oj_query($query);
	$row=mysql_fetch_row($ret);
	if((!isset($row[0]))||strlen($row[0])==0)$row[0]='No compile error info. It seems that the code you sent is not friendly.';

	set_ojinfo('title', 'ECUST Online Judge - View compile error info');
	include 'include/header.php';
?>

<pre>
<?php
	echo htmlspecialchars($row[0]);
?>
</pre>
<center><a href='javascript:history.go(-1);'>Go Back</a></center>

<?php
	include 'include/footer.php';
?>

