<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="content-type" content="text/html;charset=utf-8">
<?php
 require 'medoo.php';


 $jwid=$_POST['xuehao'];
 $jwpwd=$_POST['password'];
 $openid=$_POST['openid'];
 $jwid = trim($jwid);
        $jwid = htmlspecialchars($jwid);

        $jwpwd = trim($jwpwd);
        $jwpwd = htmlspecialchars($jwpwd);

        $openid = trim($openid);
        $openid = htmlspecialchars($openid);
//================判断学生信息是否正确==================


$cookie_file = tempnam(SAE_TMP_PATH, 'cookie');
        $ch=curl_init("http://100.fosu.edu.cn/default2.aspx");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        $str=curl_exec($ch);
        $info=curl_getinfo($ch);
        curl_close($ch);
        $pattern = '/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*)" \/>/i';
        preg_match($pattern, $str, $matches);
        $viewstate = urlencode($matches[1]);
        
        $pattern = '/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*)" \/>/i';
        preg_match($pattern, $str, $matches);
        $viewstategenerator = urlencode($matches[1]);

        $pattern = '/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*)" \/>/i';
        preg_match($pattern, $str, $matches);
        $eventvalidation = urlencode($matches[1]);


         $yh=$jwid;
         $kl=$jwpwd;

        
          

        $login_url="http://100.fosu.edu.cn/default2.aspx";
        $login="__VIEWSTATE={$viewstate}&__VIEWSTATEGENERATOR={$viewstategenerator}&__EVENTVALIDATION={$eventvalidation}&yh={$yh}&kl={$kl}&RadioButtonList1=%D1%A7%C9%FA&Button1=%B5%C7++%C2%BC&CheckBox1=on";
        //echo $login;
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL,$login_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $login);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        $str=curl_exec($ch);
        
        curl_close($ch);
       
        $ch=curl_init("http://100.fosu.edu.cn/xscj.aspx?xh={$yh}");
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_REFERER,"http://100.fosu.edu.cn/xsmainfs.aspx?xh={$yh}");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $str=curl_exec($ch);
        //echo $str;
        $info=curl_getinfo($ch);
        //print_r($info);
        curl_close($ch);
        

        $pattern = '/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*)" \/>/i';
        preg_match($pattern, $str, $matches);
        $viewstate = urlencode($matches[1]);
        

 //===========判断学生信息是否正确========================









 if (!$viewstate) {
 	echo '<h1 style="text-align:center;margin-top: 50">请检查下你写的学号密码是不是正确</h1>';
 }else{
if ($openid) {
$databaseinfo= array('database_type' => 'mysql',
    'database_name' => SAE_MYSQL_DB,
    'server' => SAE_MYSQL_HOST_M,
    'username' => SAE_MYSQL_USER,
    'password' => SAE_MYSQL_PASS,
    'port'=> SAE_MYSQL_PORT,
    'charset' => 'utf8');
 $database = new medoo($databaseinfo);       
       
       
       
$info=array('id' =>'',
        'jwid' => $jwid,
        'jwpwd' => $jwpwd,
    'openid' =>$openid);

 $database->insert('bangding', $info);

 echo '<h1 style="text-align:center;margin-top: 50">绑定成功</h1>'; 
 echo '<h2 style="text-align:center;">点击<span style="color:red;">左</span>上角的X，关闭绑定页</h2>';
 echo '<h2 style="text-align:center;">输入“<span style="color:red;">成绩</span>”即可查询成绩</h2>';
 echo '<h2 style="text-align:center;">输入“<span style="color:red;">解绑</span>”即可解除绑定</h2>';
 echo '<h3 style="text-align:center;">不过我一般不推荐解除绑定。</h3>';
 echo '<h3 style="text-align:center;">因为挂科的人才会解除绑定。</h3>';
           
echo '<h2 style="text-align:center;margin-top:30"> <img src="http://3.caserest.sinaapp.com/111.jpg" height="80" align="middle"></h2> ';
    
}else{ //下面是我之前没判断被爆库之前加的一些提醒，现在已经用不到了
   echo '<h1 style="text-align:center;margin-top: 50">总有刁民想陷害朕！！！</h1>';
            echo '<h2 style="text-align:center;margin-top:30"> <img src="http://ww4.sinaimg.cn/large/6810001bgw1et2d11g70dj20a608rmxe.jpg" height="150" align="middle"></h2> ';
            echo '<h1 style="text-align:center;margin-top: 50">有话好好说，微信微博联系我：caserest</h1>';
}

}







?>

