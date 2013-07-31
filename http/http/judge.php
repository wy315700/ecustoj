<?php
//$_POST['name']
//$_POST['pwd']
//$_POST['url']登录时的地址

	require_once 'login.php';
    
	
	if(isset($_POST['checkout']))
    {
        $sql = "select submit_id from judge_queue where status=0 limit 0,1";
        $ret = oj_query($sql);
        $row=mysql_fetch_array($ret);
        if($row == null)
        {
            $submit_id = -1;
        }
        else
        {
            $submit_id = $row['submit_id'];
        }
        echo $submit_id;
    }
    else if(isset($_POST['getnextsubmitinfo']))
    {
        $submit_id = intval($_POST['submit_id']);
        $sql = "select user_id,pro_id,language,type from submit_status where submit_id=".$submit_id;
        $ret = oj_query($sql);
        $row=mysql_fetch_array($ret);
        if($row != null)
        {
            echo $row[user_id]."\n";
            echo $row[pro_id]."\n";
            echo $row[language]."\n";
            echo $row[type]."\n";
        }
    }
    else if(isset($_POST['getnextsubmitcode']))
    {
        $submit_id = intval($_POST['submit_id']);
        $sql = "select code_content from code_info where submit_id=".$submit_id;
        $ret = oj_query($sql);
        $row=mysql_fetch_array($ret);
        if($row != null)
        {
            echo $row[code_content]."\n";
        }
    }
    else if(isset($_POST['deletequeue']))
    {
        $submit_id = intval($_POST['submit_id']);
        $sql = "delete from judge_queue where submit_id=".$submit_id;
        $ret = oj_query($sql);
        $row=mysql_fetch_array($ret);
    }
    else if(isset($_POST['getproinfo']))
    {
        $pro_id = intval($_POST['pro_id']);
        $sql = "select time_limit,mem_limit,case_time_limit,isspj from problems where pro_id=".$pro_id;
        $ret = oj_query($sql);
        $row=mysql_fetch_array($ret);
        if($row != null)
        {
            echo $row[time_limit]."\n";
            echo $row[mem_limit]."\n";
            echo $row[case_time_limit]."\n";
            echo $row[isspj]."\n";
        }
    }    
    else if(isset($_POST['returnstatus']))
    {
        $submit_id = intval($_POST['submit_id']);
        $pro_id    = intval($_POST['pro_id']);
        $user_id   = intval($_POST['user_id']);
        $result    = intval($_POST['result']);
        $cost_time = intval($_POST['cost_time']);
        $cost_mem  = intval($_POST['cost_mem']);
        $sql = "update submit_status set result=".$result.",cost_time=".$cost_time.",cost_mem=".$cost_mem." where submit_id=".$submit_id;
        $ret = oj_query($sql);
        if(mysql_affected_rows()>0)
        echo "1";
        else
        echo "0";
        if($result == 1) //OJ_AC
            UpdateSubmit($pro_id , $user_id);
    }
    else if(isset($_POST['setceinfo']))
    {
        $submit_id  = intval($_POST['submit_id']);
        $ce_info    = $_POST['ce_info'];
        $sql = "insert into ce_info (submit_id,ce_info) values('".$submit_id."','".$ce_info."')";
        $ret = oj_query($sql);
    }
    function UpdateSubmit($pro_id , $user_id)
    {
        $sql = "update problems set accepted=accepted+1 where pro_id=".$pro_id;
        $ret = oj_query($sql);
        if(HaveAC($pro_id , $user_id) != 1)
        {
            $sql = "update users set solved=solved+1 where user_id=".$user_id;
            $ret = oj_query($sql);
            $sql = "update problems set solved_users=solved_users+1 where pro_id=".$pro_id;
            $ret = oj_query($sql);
            $sql = "update user_submit set result=1 where pro_id=".$pro_id." and user_id=".$user_id;
            $ret = oj_query($sql);
        }
    }
    function HaveAC($pro_id , $user_id)
    {
        $sql = "select result from user_submit where user_id=".$user_id." and pro_id=".$pro_id;
        $ret = oj_query($sql);
        $row = mysql_fetch_array($ret);
        if($row == null)
            return -1;
        else
        {
            return intval($row['result']);
        }
    }
?>
