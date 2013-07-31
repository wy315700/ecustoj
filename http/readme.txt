include/header.php 各种连接，登陆功能，简单搜索（题目，user等)
include/footer.php 底部，copyright等
problemset.php 分页显示题目列表，支持题目搜索，传入参数有title, source等
submit.php  提交页面，完成题目代码的提交。传入参数有题目编号，比赛编号。如果是从比赛中点击提交，则比赛编号非空。

status.php 完成实时状态显示。传入参数有题号，用户号，结果，语言。根据各个参数内容显示status。支持按不同key排序。支持分页
还有个contest参数，某场比赛的status
既然有分页就还要top参数
比赛的能看到自己提交的时间和内存什么的


$_GET和$_POST取出的东西最好stripslashes()一下
还是说是我的配置问题?


关于web board:
message_id为帖子的唯一标识符
reply_id为帖子最新的回复的id,如果为message_id则表示没有回复
topic_id为帖子所回复的上一级帖子,如果是主题贴这项为0
user_id为发帖用户
pro_id为帖子对应的题目id
contest_id为帖子对应的比赛id
title为帖子标题
content为帖子内容
reply_time为帖子发帖时间
banned备用
显示时在topic_id=0的帖子中以reply_id从大到小取出
然后在topic_id=本帖子中以reply_id从大到小取出
递归
发帖时一路往上更新所有父级帖子的reply_id




