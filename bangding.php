 <!--佛大新媒体协会的成绩查询 -->
<!DOCTYPE html>
<html>
<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>绑定账号</title>
<!-- <meta name="description" content=""> -->
<meta name="viewport" content="width=device-width">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<link href='http://fonts.googleapis.com/css?family=Lato:400,700' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="css/normalize.css">

<link rel="stylesheet" href="css/style.css" media="screen" type="text/css" />
</head>
<body>
<section class="login-form-wrap">

	<img src="http://3.caserest.sinaapp.com/6681215561.gif" height="80" align="middle"> 
<h1>	绑定教务系统
</h1>

<!-- 这里其实应该加个检测数据库中是否有openid的 -->

<form class="login-form" method="post" action="jump.php">
	<label>
<input type="text" name="xuehao" required placeholder="学号">
</label>
<label>
<input type="password" name="password" required placeholder="密码">
</label>
<input type="hidden" name="openid" value="<?php 
	echo $openid=$_GET['id'];
?>">
	<input type="submit" value="绑定">
</form>


<!-- 呵呵呵呵呵呵我觉得一定会有人看这个的，我就是个小小的代码搬运工，技术牛逼的人就不要乱搞我这个了哈 -->
<!-- 我微信：caserest -->

</section>
<div style="text-align:center;margin:50px 0; font:normal 14px/24px 'MicroSoft YaHei';">

</div>
<?php require_once 'cs.php';echo '<img src="'._cnzzTrackPageView(1255478933).'" width="0" height="0"/>';?>
</body>
</html>