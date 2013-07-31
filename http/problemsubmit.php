<?php
//$_GET['pro_id']
//$_GET['contest_id']
//�������ύ���
//$_POST['pro_id']����ָ���ύ��Ŀ�ı��
//$_POST['language']����ָ�����Ե�����
//$_POST['code']����ָʾԴ����
//$_POST['contest_id']����ָʾ����id

	require_once 'include/function.php';
	if(!isset($_SESSION['user_id']))redirect("error.php?msg=".urlencode("Please login first!"));
	$user_id = intval($_SESSION['user_id']);
	
	if(!isset($_POST['code']))$code = "";
	else $code = get_to_mysql($_POST['code']);
	$length = strlen($code);
	if($length>0){ //�ύ
		if($length>$code_limit)redirect("error.php?msg=".urlencode("Source code is too long!"));
		
		$pro_id = intval(get_to_mysql($_POST['pro_id']));
		$language = get_to_mysql($_POST['language']);
		$contest_id = 0;
		if(!empty($_POST['contest_id'])){
			$contest_id = intval(get_to_mysql($_POST['contest_id']));
			$query="select pro_id from contest_problems where contest_id=" . $contest_id . " and pro_id=" . $pro_id;
			$ret=oj_query($query);
			$row=mysql_fetch_row($ret);
			if(!isset($row[0]))redirect('error.php?msg=' . urlencode('No such problem in such contest'));
			$query="select end_time>=now() and start_time<=now() from contest where contest_id=" . $contest_id;
			$ret=oj_query($query);
			$row=mysql_fetch_row($ret);
			if(!$row[0])redirect('error.php?msg=' . urlencode('Contest ended'));
		}
		$query = "select * from problems where pro_id=" . $pro_id;
		$ret = oj_query($query);
		$row = mysql_fetch_row($ret);
		if(!isset($row[0]))redirect("error.php?msg=".urlencode("No such Problem!"));
		
		$query = "insert into submit_status(user_id,pro_id,contest_id,submit_time,language,code_length) "
				."values($user_id,$pro_id,$contest_id,now(),$language,$length)";
		oj_query($query);
		$ret = oj_query("select last_insert_id()");
		$row = mysql_fetch_row($ret);
		$submit_id = $row[0];
		$query = "insert into code_info(submit_id,code_content) values($submit_id,'$code')";
		oj_query($query);
		$query = "insert into judge_queue(submit_id) values($submit_id)";
		oj_query($query);
		$query="update problems set submits=submits+1 where pro_id=$pro_id ";
		oj_query($query);
		$query="update users set submits=submits+1 where user_id=$user_id ";
		oj_query($query);
		
		$query="select result from user_submit where user_id=$user_id and pro_id=$pro_id";
		$ret=oj_query($query);
		$row=mysql_fetch_row($ret);
		if(!isset($row[0])){
			$query="insert into user_submit (user_id,pro_id,result) values ($user_id,$pro_id,0)";
			oj_query($query);
			$query="update problems set submit_users=submit_users+1 where pro_id=$pro_id";
			oj_query($query);
		}
		
		redirect("status.php");
	}

	set_ojinfo('title', 'ECUST Online Judge - Submit code');

	include 'include/header.php';
	if(isset($_POST['pro_id']))$_GET['pro_id'] = $_POST['pro_id'];
?>

<form name="form1" method="post" action="problemsubmit.php">
  <label>Problem ID:
  <input type="text" name="pro_id" value="<?php if(isset($_GET['pro_id']))echo htmlspecialchars($_GET['pro_id']);?>">
  </label>
<?php
	if(!empty($_GET['contest_id'])){
		$contest_id=get_to_html($_GET['contest_id']);
		echo "<label>Contest ID:<input name='contest_id' value='$contest_id'></label>";
	}
?>
  <p>
    <label>Language:
<?php
	$submit_language=$language_array;
	unset($submit_language['']);
	unset($submit_language[3]);//pascal
	//unset($submit_language[4]);//java
	unset($submit_language[5]);//python
	Draw_Options($submit_language,'language',2);
?>
    </label>
  </p>
  <p>
    <label>Source:<br>
    <textarea name="code" cols="100" rows="40"></textarea>
    </label>
  </p>
  <p>
    <label>
    <input type="submit" name="Submit" value="Submit">
    </label>
    <label>
    <input type="reset" name="Submit2" value="Reset">
    </label>
  </p>
</form>

<?php
	include 'include/footer.php';
?>
