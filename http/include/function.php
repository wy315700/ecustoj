<?php
require_once 'config.php';

session_start();
//$_SESSION['user_id']
//$_SESSION['user_name']
//$_SESSION['is_admin']
//$_SESSION['is_judger']可以看到status,不能看代码
//$_SESSION['contest' . $contest_id]=1:允许进入

$database =  mysql_connect($mysql_url.':'.$mysql_port, $mysql_user, $mysql_password ) or die("error connecting localhost\n");
mysql_select_db($mysql_dbname) or die("error selecting database\n");
mysql_query("set names 'utf8'");

function redirect($location){
	header("Location: " . $location);
	exit();
}

function get_ip() {
	if (getenv('HTTP_CLIENT_IP'))
		$ip = getenv('HTTP_CLIENT_IP');
	else if (getenv('HTTP_X_FORWARDED_FOR'))
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	else if ($_SERVER['REMOTE_ADDR'])
		$ip = $_SERVER['REMOTE_ADDR'];
	else
		$ip = 'unknown';
	return $ip;
}

function get_to_mysql($s)
{
	if(get_magic_quotes_gpc())
		return mysql_real_escape_string(stripslashes($s));
	else
		return mysql_real_escape_string($s);
}
function get_to_html($s)
{
	if(get_magic_quotes_gpc())
		return htmlspecialchars(stripslashes($s));
	else
		return htmlspecialchars($s);
}
function oj_query($query){
	global $database;
	global $query_num;
	$ret = mysql_query($query,$database) or die(mysql_error());
	$query_num++;
	return $ret;
}

/**
 * it is the records's array.
 * it is recommandeded you to use set_ojinfo and get_ojinfo to control it for safity.
 */
$oj_options_cache = array('oj_option_title' => 'ECUST Online Judge');
/**
 * get the option.
 * if it is not exists, return false
 * @param string $key The key of the option's name
 * @return object the value of the key
 * @example get_ojinfo('title');
 */
function get_ojinfo($key){
	global $oj_options_cache;
	$key = 'oj_option_' . trim($key);
	if(empty($oj_options_cache[$key]))
		return false;
	return $oj_options_cache[$key];
}
/**
 * set the option and return the old one.
 * if the old one is not exists, return false
 * @param string $key The key of the option's name
 * @param object $value The value of the option
 * @return object  the old value of the key
 * @example set_ojinfo('title', 'Ecust Online Judge');
 * @example set_ojinfo('hightlight');
 */
function set_ojinfo($key, $value = true){
	global $oj_options_cache;
	$oldValue = get_ojinfo($key);
	$key = 'oj_option_' . trim($key);
	$oj_options_cache[$key] = $value;

	return $oldValue;
}

function user_id_to_name($user_id){
	$query = "select name from users where user_id=" . intval($user_id);
	$ret = oj_query($query);
	$row = mysql_fetch_row($ret);
	return $row[0];
}

function name_to_user_id($name){
	$query = "select user_id from users where name='$name'";
	$ret = oj_query($query);
	$row = mysql_fetch_row($ret);
	return $row[0];
}

function rgb_to_html($rgb){
	$ret=dechex($rgb);
	while(strlen($ret)<6)$ret='0' . $ret;
	return '#' . $ret;
}

//permission
function user_id_to_permission($user_id){
	$query="select permission from users where user_id=" . intval($user_id);
	$ret=oj_query($query);
	$row=mysql_fetch_row($ret);
	return $row[0];
}

function permission_view_code($user_id){
	$permission=user_id_to_permission($user_id);
	if(isset($permission)&&$permission==10)return true;
	return false;
}

function permission_admin($user_id){
	$permission=user_id_to_permission($user_id);
	if(isset($permission)&&$permission==10)return true;
	return false;
}

function permission_judger($user_id){
	$permission=user_id_to_permission($user_id);
	if(isset($permission)&&$permission==4)return true;
	return false;
}

//permission end

//draw something
function Draw_Options($ary,$name,$selected){
	echo "<select name='$name'>";
	foreach($ary as $key=>$val){
		echo "<option value='$key'";
		if($selected==$key&&strlen($selected)==strlen($key))echo ' selected';
		echo ">$val</option>";
	}
	echo '</select>';
}

function Draw_single_tag($value,$back_color,$font_color){
	//$value=htmlspecialchars($value);
	echo '<table class="tag">';
	echo "<tr><td height=1 width=1></td><td bgcolor='$back_color'></td><td width=1></td></tr>";
	echo "<tr><td bgcolor='$back_color'></td><td bgcolor='$back_color'><font color='$font_color'>$value</font></td><td bgcolor='$back_color'></td></tr>";
	echo "<tr><td height=1></td><td bgcolor='$back_color'></td><td></td></tr>";
	echo '</table>';
}

function Draw_tags($pro_id,$contest_id=0){
	if(!isset($_SESSION['user_id']))return;
	$user_id=$_SESSION['user_id'];
	echo '<table border=0><tr>';
	//是否解决/提交
	$query="select result from user_submit where user_id=" . intval($user_id) . " and pro_id=" . intval($pro_id);
	$ret=oj_query($query);
	$row=mysql_fetch_row($ret);
	if(isset($row[0])){
		if($contest_id!=0){
			$query="select max(result=1) from submit_status where user_id=" . intval($user_id) . " and pro_id=" . intval($pro_id) . " and contest_id=" . intval($contest_id);
			$ret=oj_query($query);
			$row=mysql_fetch_row($ret);
			if(isset($row[0]))$state=$row[0];
		}else $state=$row[0];
	}
	if(isset($state)){
		echo '<td>';
		if($state==0)Draw_single_tag('Submitted','#ccccff','#333377');
		else Draw_single_tag('Solved','red','white');
		echo '</td>';
	}
	//其他tag
	$query='select config_mark.* from user_mark '
		. 'inner join config_mark on user_mark.mark_id=config_mark.mark_id '
		. " where user_id=" . intval($user_id) . " and pro_id=" . intval($pro_id);
	$ret=oj_query($query);
	while($row=mysql_fetch_assoc($ret)){
		echo '<td>';
		$tag_fore_color=rgb_to_html($row['fore_color']);
		$tag_entity="<a href='search.php?field=tag&text={$row['mark_id']}' style='color:$tag_fore_color'>{$row['mark_des']}</a>";
		Draw_single_tag($tag_entity,rgb_to_html($row['back_color']),'');
		echo '</td>';
	}
	//添加tag
	echo '<td>';
	Draw_single_tag("<a title='Edit tag' href='addtag.php?pro_id=$pro_id'>+</a>",'#ccffcc','');
	echo '</td>';
	echo '</tr></table>';
}

function Draw_search_box($field='title',$text=''){
	global $search_array;
	echo '<form action="search.php" method="get">';
	echo "Search:<input name='text' value='$text'>&nbsp;";
	echo "<select name='field'>";
	foreach($search_array as $key=>$val){
		echo "<option value='$key'";
		if($field==$key)echo ' selected';
		echo ">{$val[1]}-$key</option>";
	}
	echo '</select>&nbsp;';
	echo '<input type=submit value="Search">';
	echo '</form>';
}
//draw end
// log in and out begin
/**
 * log in.
 * if the user id and password is correct, return success
 * @param string $login_name The key of the option's name
 * @param string $login_pwd The key of the option's name
 * @return string return success if the user id and password is correct, or return error message
 * @example get_ojinfo('title');
 */
function oj_login($login_name, $login_pwd){
	if(strlen($login_pwd)!=32)
		return 'Please make sure that you have enabled the Javascript.';

	$login_name = get_to_mysql($login_name);
	$login_pwd  = get_to_mysql($login_pwd);
	$query="select user_pwd,user_id,name from users where name='$login_name'";
	$ret = oj_query($query);
	$row = mysql_fetch_row($ret);
	if(empty($row[0]))
		return 'Wrong User ID.';
	if($row[0] != $login_pwd)
		return 'Wrong Password.';
	// banned?

	$login_id = $row[1];
	$login_name = $row[2];
	$_SESSION['user_name'] = $login_name;
	$_SESSION['user_id'] = $login_id;
	$_SESSION['is_admin'] = permission_admin($login_id);
	$_SESSION['is_judger'] = permission_judger($login_id);

	$login_ip=get_ip();
	$query="update users set last_login_ip='$login_ip', last_login_time=now() where user_id=" . intval($login_id);
	$ret=oj_query($query);

	return 'success';
}
// log in and out end
?>
