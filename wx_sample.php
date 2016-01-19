<?php
//佛大微博协会的佛大成绩查询接口
//该代码是放置在sae上。

define("TOKEN", "weixin");

$wechatObj = new wechatCallbackapiTest();
if (!isset($_GET['echostr'])) {
    $wechatObj->responseMsg();
}else{
    $wechatObj->valid();
}

class wechatCallbackapiTest
{
    //验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echoStr;
            exit;
        }
    }

    public function responseMsg()
    {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (!empty($postStr)){
            $this->logger("R ".$postStr);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $RX_TYPE = trim($postObj->MsgType);

            $result = "";
            switch ($RX_TYPE)
            {
                case "event":
                    $result = $this->receiveEvent($postObj);
                    break;
                case "text":
                    $result = $this->receiveText($postObj);
                    break;
            }
            $this->logger("T ".$result);
            echo $result;
               $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $mediaid=$postObj->MediaId;
            $megid=$postObj->MsgId;
            $MsgType=$postObj->MsgType;
            $keyword = trim($postObj->Content);
            $j=$postObj->Location_Y;
            $w=$postObj->Location_X;
            $label=$postObj->Label;
            $keyword = trim($postObj->Content);
            $keystr=mb_substr($keyword,0,2,'utf-8');
            $time = time();
            $mem=memcache_init();
            //$ev = $postObj->Event;
            $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>"; 
            $musicTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[music]]></MsgType>
                            <Music>
                            <Title><![CDATA[秋风词]]></Title>
                            <Description><![CDATA[湖南省博物馆]]></Description>
                            <MusicUrl><![CDATA[http://www.hnmuseum.com/hnmuseum/service/download/qfc.mp3]]></MusicUrl>
                            <HQMusicUrl><![CDATA[http://www.hnmuseum.com/hnmuseum/service/download/qfc.mp3]]></HQMusicUrl>
                           
                            </Music>
                            </xml>";   
            $newsTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
                            <ArticleCount>1</ArticleCount>
                            <Articles>
                            <item>
                            <Title><![CDATA[%s]]></Title> 
                            <Description><![CDATA[%s]]></Description>
                            </item>
                            </Articles>
                            <FuncFlag>1</FuncFlag>
                            </xml>";   
            

            if(!empty( $keyword )){
                $msgType = "text";
                $keyword=strtoupper($keyword);
             
//==============看数据库中是否有绑定openid==============
$conn = @mysql_connect(SAE_MYSQL_HOST_M .':'. SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS) or die('数据库连接失败，错误信息：'.mysql_error());
    
            //第二步，选择指定的数据库，设置字符集
         mysql_select_db(SAE_MYSQL_DB) or die('数据库错误，错误信息：'.mysql_error());
        mysql_query('SET NAMES UTF8') or die('字符集设置错误'.mysql_error());
    
    //第三步，从这个数据库里选一张表（grade），然后把这个表的数据库提出来（获取记录集）
    $query = $sql = "SELECT * FROM bangding WHERE openid ='{$fromUsername}'";
    //$query = "SELECT * FROM userinfo";
    $result = @mysql_query($query) or die('SQL错误，错误信息'.mysql_error());
    //$result就是记录集
    
    //第四步，将记录集里的数据显示出来
    //print_r(mysql_fetch_array($result,MYSQL_ASSOC));
    $row = mysql_fetch_array($result);
    //第五步，释放记录集资源
    mysql_free_result($result);
    
    //最后一步，关闭数据库
    mysql_close();    

//============================================

$yh=$row['jwid'];
$kl=$row['jwpwd'];


            if ($keyword=="绑定") {
                if (!empty($row)) {
                    $contentStr ="您已经绑定，无需再次绑定，直接输入“成绩”查看";
                }else{
              $contentStr = "<a href=\"http://3.caserest.sinaapp.com/foda/bangding.php?id={$fromUsername}\">点此绑定教务系统</a>";}
              //此处链接为微信返回的学号密码绑定链接。
               
                   $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                   echo $resultStr;
            }elseif ($keyword=="成绩") {
                # code...
            

//=====================如果已经绑定了开始抓成绩=======================
if (!empty($row)) {
                    
                

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


         //$yh="2011314313";
        // $kl="chengfei0209";

        
          if(empty($viewstate)){
          $grade="教务系统忙，请稍候再试！";
          }

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
        
        $pattern = '/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*)" \/>/i';
        preg_match($pattern, $str, $matches);
        $viewstategenerator = urlencode($matches[1]);

        $pattern = '/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*)" \/>/i';
        preg_match($pattern, $str, $matches);
        $eventvalidation = urlencode($matches[1]);
        //echo $view;
        //print_r($matches);


        if(empty($viewstate)){
        $grade="教务系统忙，请稍候再试！";
        }
        $str= '学期成绩';
        $button=iconv('utf-8', 'gb2312', '$str');
        $score="__VIEWSTATE={$viewstate}&__VIEWSTATEGENERATOR={$viewstategenerator}&__EVENTVALIDATION={$eventvalidation}&xn=&xq=&&Button4=%C8%AB%B2%BF%D1%A7%C6%DA%B2%E9%D1%AF&ddlKCLX=%B1%D8%D0%DE%BF%CE";
        $ch=curl_init("http://100.fosu.edu.cn/xscj.aspx?xh={$yh}");

        //echo $score;
        curl_setopt($ch, CURLOPT_TIMEOUT,60); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $score);
        curl_setopt($ch,CURLOPT_REFERER,"http://100.fosu.edu.cn/xsmainfs.aspx?xh={$yh}");
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $table=curl_exec($ch);
        curl_close($ch);
        


        $data="><span id=\"jqpjf\">";
        $pm1=explode($data,$table);
        $da="。(";  
        $dat=iconv("UTF-8","GB2312",$da);        
        $pm2=explode($dat,$pm1[1]); 
        $zongjidian= $pm2[0];         
                
                
                $table = preg_replace("'<table[^>]*?>'si","",$table);   
        $table = preg_replace("'<tr[^>]*?>'si","",$table);    
        $table = preg_replace("'<td[^>]*?>'si","",$table);    
        $table = str_replace("</tr>","{tr}",$table);    
        $table = str_replace("</td>","{td}",$table);    
        //去掉 HTML 标记      

        
        $table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);   
        //去掉空白字符      
        $table = preg_replace("'([rn])[s]+'","",$table);    
        $table = str_replace(" ","",$table);    
        $table = str_replace(" ","",$table);    
            
        $table = explode('{tr}', $table);    
        array_pop($table);    
        foreach ($table as $key=>$tr) {    
                $td = explode('{td}', $tr);    
                array_pop($td);    
            $td_array[] = $td;     
        }    
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
                
        //$str=mb_convert_encoding($str, "utf-8", "gb2312");
        //print_r($str);
        //echo "<pre>";
       // $cj=get_td_array($str);
       // // print_r($cj) ;
        if (is_array($td_array)) {
            foreach($td_array as $v){
           
           
                if($v[10]){
                 $grade .="{$v[2]}  ---{$v[10]}------{$v[14]}\n";
                   
                }
           }
           $grade=$grade."\n".$zongjidian;
        }else{
            $grade="查询成绩失败!\n原因:绑定学号或者密码错误\n解决办法：请回复“解绑”，随后回复“绑定”重新绑定";
            $grade=mb_convert_encoding($grade, "gb2312","utf-8");
        }
                
                $grade=mb_convert_encoding($grade, "utf-8", "gb2312");

$title="查到的成绩如下";
$resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, $title,$grade);
                echo $resultStr;

        // if(empty($grade)){
        // echo "暂时不可查询，请耐心等待！\n";
        // }
        // else{
        // echo "{$grade}";
        // } 


}else{
    $contentStr = "您还没有绑定\n回复“绑定”后方可查成绩";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
    echo $resultStr;# code...
}





//==============================================
}elseif ($keyword=="解绑") {
    if ($row[!empty($row)]) {

        $conn = @mysql_connect(SAE_MYSQL_HOST_M .':'. SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS) or die('数据库连接失败，错误信息：'.mysql_error());
    
            //第二步，选择指定的数据库，设置字符集
         mysql_select_db(SAE_MYSQL_DB) or die('数据库错误，错误信息：'.mysql_error());
        mysql_query('SET NAMES UTF8') or die('字符集设置错误'.mysql_error());

        $sql = "SELECT * FROM bangding WHERE openid ='{$fromUsername}'";
        $result = mysql_query($sql);
        $row = mysql_fetch_array($result);
        $jwid=$row['jwid'];
        $sql = "DELETE FROM bangding WHERE openid ='{$fromUsername}'";
        if(mysql_query($sql)){
            $contentStr ="你已解除学号：{$jwid}的绑定！回复“绑定”进行重新绑定";
        }else{
            $contentStr = "数据库他妈炸了，所以解绑失败，请老爷您重新尝试！";
        }
        //$contentStr ="已经给您解绑了";
    }else{
        $contentStr = "你还没有绑定，恕无法解绑";
    }
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
    echo $resultStr;# code...
}
























               
                
            


           }else{
                    echo "Input something...";
                }
            
}else {
            echo "";
            exit;
            }
  
}

      
    

    private function receiveText($object)
    {
        $keyword = trim($object->Content);
        $url = "http://apix.sinaapp.com/weather/?appkey=".$object->ToUserName."&city=".urlencode($keyword); 
        $output = file_get_contents($url);
        $content = json_decode($output, true);

        $result = $this->transmitNews($object, $content);
        return $result;
    }

    private function transmitText($object, $content)
    {
        if (!isset($content) || empty($content)){
            return "";
        }
        $textTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return "";
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
             </item>
        ";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $newsTpl = "<xml>
        <ToUserName><![CDATA[%s]]></ToUserName>
        <FromUserName><![CDATA[%s]]></FromUserName>
        <CreateTime>%s</CreateTime>
        <MsgType><![CDATA[news]]></MsgType>
        <Content><![CDATA[]]></Content>
        <ArticleCount>%s</ArticleCount>
        <Articles>
        $item_str</Articles>
        </xml>";

        $result = sprintf($newsTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    private function logger($log_content)
    {
      
    }

   
    


     


   
    
}
?>