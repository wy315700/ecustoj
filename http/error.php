<?php
	require_once 'include/function.php';

	set_ojinfo('title', 'ECUST Online Judge - Error');

	include 'include/header.php';
?>

<center><h1>Error:
<?php
echo get_to_html($_GET['msg']);
?>
</h1>
<br/>
<a href='javascript:history.go(-1);'>Go Back</a>
</center>
<?php
	include 'include/footer.php';
?>

