<?php
/**
 * EOJ problem template
 * @author OWenT
 */
require_once dirname(__FILE__) .  '/../include/function.php';

$pro_id = 0;
$pro_ban = 1;
$pro_spj = 0;
$row = array(
	'title' => 'Undefined',
	'description' => 'There is no description.',
	'input' => 'unknown',
	'output' => 'unknown',
	'sample_input' => 'No sample',
	'sample_output' => 'No sapmle',
	'hint' => '',
	'source' => 'Unknown',
	'time_limit' => '1000',
	'case_time_limit' => '1000',
	'mem_limit' => '65536',
	'banned' => 1,
	'isspj' => 0
		);

if (!isset($_SESSION['user_id']))
	redirect('error.php?msg=' . urlencode('Please login first'));
if (!permission_admin($_SESSION['user_id']))
	redirect('error.php?msg=' . urlencode('Access denied'));
if (isset($_GET['do_unban'])) {
	$pro_unban = implode(',', explode(' ', $_GET['do_unban']));
	$query = "update problems set banned=0 where pro_id in ($pro_unban)";
	$ret = oj_query($query);
	redirect('problemlist.php');
}
if (isset($_GET['do_ban'])) {
	$pro_unban = implode(',', explode(' ', $_GET['do_ban']));
	$query = "update problems set banned=1 where pro_id in ($pro_unban)";
	$ret = oj_query($query);
	redirect('problemlist.php');
}
if (isset($_GET['pro_id']))
	$pro_id = get_to_html($_GET['pro_id']);

if (isset($_POST['title'])) {
	$pro_title = get_to_mysql($_POST['title']);
	$pro_des = get_to_mysql($_POST['description']);
	$pro_in = get_to_mysql($_POST['input']);
	$pro_out = get_to_mysql($_POST['output']);
	$pro_samin = get_to_mysql($_POST['sample_input']);
	$pro_samout = get_to_mysql($_POST['sample_output']);
	$pro_hint = get_to_mysql($_POST['hint']);
	$pro_source = get_to_mysql($_POST['source']);
	$pro_tle = get_to_mysql($_POST['time_limit']);
	$pro_ctle = get_to_mysql($_POST['case_time_limit']);
	$pro_mle = get_to_mysql($_POST['mem_limit']);
	$pro_ban = get_to_mysql($_POST['banned']);
	$pro_spj = get_to_mysql($_POST['isspj']);
	if (empty($pro_ban))
		$pro_ban = 0;
	if (empty($pro_spj))
		$pro_spj = 0;
	if (0 != $pro_id) {
		$query = "update problems set title='$pro_title',description='$pro_des',input='$pro_in',output='$pro_out', "
				. "sample_input='$pro_samin',sample_output='$pro_samout',hint='$pro_hint',source='$pro_source', "
				. "time_limit=$pro_tle,case_time_limit=$pro_ctle,mem_limit=$pro_mle,add_time=now(), "
				. "banned=$pro_ban,isspj=$pro_spj "
				. "where pro_id=$pro_id ";
	} else {
		$query = 'insert into problems (title,description,input,output,sample_input,sample_output,hint '
				. ',source,time_limit,case_time_limit,mem_limit,add_time,banned,isspj) '
				. "values ('$pro_title','$pro_des','$pro_in','$pro_out','$pro_samin','$pro_samout','$pro_hint','$pro_source', "
				. "$pro_tle,$pro_ctle,$pro_mle,now(),$pro_ban,$pro_spj) ";
	}
	$ret = oj_query($query);

	if (0 == $pro_id) {
		$ret = oj_query('select last_insert_id()');
		$row = mysql_fetch_row($ret);
		$pro_id = $row[0];
	}
	redirect("problemshow.php?pro_id=$pro_id");
}

if (0 != $pro_id) {
	$query = 'select * from problems where pro_id=' . $pro_id;
	$ret = oj_query($query);
	$row = mysql_fetch_assoc($ret);
}

function draw_fillin($left, $tag_name, $name, $prop='', $right='', $value='') {
	echo '<tr>';
	echo '<td>' . htmlspecialchars($left) . '</td>';
	echo "<td><$tag_name name='$name' id='$name' $prop>" . htmlspecialchars($value) . "</$tag_name>$right</td>";
	echo '</tr>';
}

if (0 == $pro_id)
	set_ojinfo('title', 'ECUST Online Judge - Add a Problem');
else
	set_ojinfo('title', 'ECUST Online Judge - Edit Problem ' . $pro_id);

include dirname(__FILE__) .  '/../include/header.php';
?>

<form method=post action="admin_editproblem.php?pro_id=<?php echo $pro_id; ?>">
    <table align=center>
        <tr>
            <td>Problem&nbsp;ID:</td>
            <td><?php echo $pro_id; ?></td>
        </tr>
		<?php
		draw_fillin('Title:', 'input', 'title', ' maxlength="200" style="width:600px; Color: Blue; Font-weight: bolder; Font-size:large;" value="' . htmlspecialchars($row['title']) . '" ');
		draw_fillin('Description:', 'textarea', 'description', '', '', $row['description']);
		draw_fillin('Input:', 'textarea', 'input', '', '', $row['input']);
		draw_fillin('Output:', 'textarea', 'output', '', '', $row['output']);
		draw_fillin('Sample Input:', 'textarea', 'sample_input', '', '', $row['sample_input']);
		draw_fillin('Sample Output:', 'textarea', 'sample_output', '', '', $row['sample_output']);
		draw_fillin('Hint:', 'textarea', 'hint', '', '', $row['hint']);
		draw_fillin('Source:', 'input', 'source', ' maxlength="200" style="width:800px;" value="' . htmlspecialchars($row['source']) . '" ');
		draw_fillin('Time limit:', 'input', 'time_limit', ' value="' . htmlspecialchars($row['time_limit']) . '" ', 'MS');
		draw_fillin('Case time limit:', 'input', 'case_time_limit', ' value="' . htmlspecialchars($row['case_time_limit']) . '" ', 'MS'
				. ' (if no case time limit, set case time limit=time limit)');
		draw_fillin('Memory limit:', 'input', 'mem_limit', ' value="' . htmlspecialchars($row['mem_limit']) . '" ', 'K');
		if ($row['banned'] != 0)
			$pro_ban = 'checked';
		else
			$pro_ban='';
		if ($row['isspj'] != 0)
			$pro_spj = 'checked';
		else
			$pro_spj='';
		draw_fillin('Banned:', 'input', 'banned', ' value="1" type=checkbox ' . $pro_ban);
		draw_fillin('IsSpj:', 'input', 'isspj', ' value="1" type=checkbox ' . $pro_spj);
		?>
        <tr>
            <td></td>
            <td><input type='submit' name='submit' value='Submit'/></td>
        </tr>
    </table>
</form>
<script type="text/javascript" src="libs/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="libs/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
function replaceEditor(name, style) {
	style = style || 'OJBasic';
	var editor = CKEDITOR.replace(name, {toolbar: style});
	CKFinder.setupCKEditor( editor, 'libs/ckfinder/' ) ;
}

(function(){
	replaceEditor('description', 'OJFull');
	replaceEditor('input', 'OJBasic');
	replaceEditor('output', 'OJBasic');
	replaceEditor('sample_input', 'OJBasic');
	replaceEditor('sample_output', 'OJBasic');
	replaceEditor('hint', 'OJBasic');
})();
</script>
<?php include dirname(__FILE__) .  '/../include/footer.php'; ?>