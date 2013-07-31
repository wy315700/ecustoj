<?php
	require_once ('include/function.php');

	set_ojinfo('title', 'ECUST Online Judge - Faq');
	include ('include/header.php');
?>
<center><h1>Frequently Asked Questions</h1></center><br>
Q:How to use 64-bit integer types ?<br>
A:For C/C++, use <font color=blue>long long</font> as type name and use <font color=red>"%lld"</font> for scanf/printf.<br>
<br>
Q: why I get WA doing SPJ problem while I confirm that the code is correct?<br>
A: In some cases of the SPJ problems, there will be WA replacing the PE. So be more careful when solving an SPJ problem.<br>

<?php
	include 'include/footer.php';
?>

