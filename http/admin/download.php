<?php
if (!isset($_GET['cid']))
    die ('Unknow contest id!');
require_once dirname(__FILE__) . '/../include/function.php';

$cid = intval(get_to_mysql($_GET['cid']));

$res = oj_query('Select title, start_time, end_time, description, ispub, start_time<now() as start, end_time<now() as end From contest Where contest_id=' . $cid);
$contest = mysql_fetch_array($res);
$problems = array();
if (empty($contest))
    die ('Contest not founded!');
$basedir = '../resource/files/';
$filename = $_GET['cid'] . '_' . $contest['title'] . '_source.zip';

if (false == file_exists($basedir . $filename)) {
    if (!(isset($_SESSION['user_id']) && permission_admin($_SESSION['user_id'])))
        die ('Permission deny!');

    if (0 == intval($contest['start']))
        die ('Contest has not began!');
    if (0 == intval($contest['end']))
        die ('Contest has not ended!');
    
    $readme = "Contest: \t{$contest['title']}\r\n\r\nStart time: \t{$contest['start_time']}\r\nEnd time: \t{$contest['end_time']}\r\n\r\nDescript:\r\n {$contest['description']}\r\n\r\nProblem List:\r\n";
    
    $res = oj_query('Select problems.pro_id, title, new_id From contest_problems, problems Where problems.pro_id=contest_problems.pro_id and contest_id=' . $cid);
    $contest_problem_map = array();
    while ($contest_problem = mysql_fetch_array($res)) {
        $contest_problem_map[$contest_problem['pro_id']] = chr(64 + intval($contest_problem['new_id'])) . '_' . $contest_problem['title'];
        $problems[$contest_problem_map[$contest_problem['pro_id']]] = array();
        $readme .= chr(64 + intval($contest_problem['new_id'])) . " {$contest_problem['title']}\r\n";
    }

    $res = oj_query('Select user_id, name, nick_name From users Where user_id in (Select user_id From submit_status Where result=1 and contest_id=' . $cid . ')');
    $user = array();
    while ($usr = mysql_fetch_array($res)) {
        $user[$usr['user_id']] = "{$usr['name']}_{$usr['nick_name']}";
    }
    
    $res = oj_query('Select submit_status.submit_id, user_id, pro_id, language, code_content From submit_status, code_info Where submit_status.submit_id=code_info.submit_id and result=1 and contest_id=' . $cid);
    while ($submit = mysql_fetch_array($res)) {
        $problems[$contest_problem_map[$submit['pro_id']]][$submit['submit_id'] . '_' . $user[$submit['user_id']] . $language_suffix[intval($submit['language'])]] = $submit['code_content'];
    }
    
    
    $dwn = new ZipArchive();
    $res = $dwn->open($basedir . $filename, ZIPARCHIVE::CREATE);
    
    $dwn->addFromString('readme.txt', $readme);
    foreach ($problems as $pid => $parr) {
        $dwn->addEmptyDir($pid);
        foreach ($parr as $sid => $source) {
            $dwn->addFromString("$pid/$sid", $source);
        }
    }
    $dwn->close();
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename=' . $filename);
readfile($basedir . $filename);

?>
