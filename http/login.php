<?php
//$_POST['name']
//$_POST['pwd']
//$_POST['url']登录时的地址

	require_once 'include/function.php';
	
	if(!isset($_POST['name']))redirect('./');
	
	$msg = oj_login($_POST['name'], $_POST['pwd']);
	if($msg != "success")
		redirect('error.php?msg=' . urlencode($msg));
	if(get_magic_quotes_gpc())
		redirect(stripslashes($_POST['url']));
	else
		redirect($_POST['url']);
?>
