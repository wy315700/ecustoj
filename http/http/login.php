<?php
//$_POST['name']
//$_POST['pwd']
//$_POST['url']登录时的地址

	require_once '../include/function.php';
	if(!isset($_POST['name']) || $_POST['name'] != 'adminadminadminadmin')
		redirect('../');
	else
        $login=1;
?>
