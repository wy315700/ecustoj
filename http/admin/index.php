<?php
	require_once '../include/function.php';
	
	if(!isset($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Please login first'));
	if(!permission_admin($_SESSION['user_id']))redirect('error.php?msg=' . urlencode('Access denied'));

	
	set_ojinfo('title', 'ECUST Online Judge - Admin');
	include '../include/header.php';
?>

Hello?

<?php
	include '../include/footer.php';
?>

