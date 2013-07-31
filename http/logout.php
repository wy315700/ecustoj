<?php
//$_GET['url']原先所在页面的地址

	session_start();
	session_unset();
	session_destroy();
	setcookie('oj_user', '', time() - 3600);
	if(empty($_GET['url']))
		header("Location: ./");
	else
	{
		if(get_magic_quotes_gpc())
			header("Location: " . stripslashes($_GET['url']));
		else
			header("Location: " . $_GET['url']);
	}
		
?>
