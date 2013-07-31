<?php
$mysql_url		= SAE_MYSQL_HOST_M;
$mysql_user     = SAE_MYSQL_USER;
$mysql_password = SAE_MYSQL_PASS;
$mysql_port     = SAE_MYSQL_PORT;
$mysql_dbname   = SAE_MYSQL_DB;
$bbs_limit      = 20;    //bbs每页最少显示20条
$code_limit     = 32768; //代码最大长度32k
$status_limit   = 20;    //状态每次显示20条
$contest_limit  = 20;    //比赛列表每次显示20条
$ranklist_limit = 50;    //author ranklist中每页默认显示50条
$time_start = microtime(true);
$query_num  = 0;
$language_array = array(
	''=>'All',
	1=>'C',
	2=>'C++',
	3=>'Pascal',
	4=>'Java',
	5=>'Python',
);

$language_suffix = array(
	''=>'.txt',
	1=>'.c',
	2=>'.cpp',
	3=>'.pas',
	4=>'java',
	5=>'.py',
);

$result_array =array(
	''=>'All',
	0=>'Waiting',
	1=>'Accepted',
	2=>'Presentation Error',
	3=>'Time Limit Exceeded',
	4=>'Memory Limit Exceeded',
	5=>'Wrong Answer',
	6=>'Output Limit Exceeded',
	7=>'Compile Error',
	8=>'Runtime Error(SEGV)',
	9=>'Runtime Error(FPE)',
	10=>'Runtime Error(BUS)',
	11=>'Runtime Error(ABRT)',
	12=>'Runtime Error(UNKNOWN)',
	13=>'Restricted Function',
	14=>'System Error',
	15=>'Compiling',
);

$search_array=array(
	'name'  =>array('name','users'),
	'email' =>array('emailaddr','users'),
	'nick'  =>array('nick_name','users'),
	'school'=>array('school','users'),
	'from'  =>array('fromaddr','users'),
	
	'title' =>array('title','problems'),
	'source'=>array('source','problems'),
	
	'tag'   =>array('mark_id','user_mark'),
);

?>
