<?php
/**
 * 邮件发送函数
 */
function sendMail($to, $subject, $content)
{
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new PHPMailer(); //实例化
    $mail->IsSMTP(); // 启用SMTP
    $mail->Host = C('MAIL_HOST'); //smtp服务器的名称（这里以126邮箱为例）
    $mail->SMTPAuth = C('MAIL_SMTPAUTH'); //启用smtp认证
    $mail->Username = C('MAIL_USERNAME'); //你的邮箱名
    $mail->Password = C('MAIL_PASSWORD'); //邮箱密码
    $mail->From = C('MAIL_FROM'); //发件人地址（也就是你的邮箱地址）
    $mail->FromName = C('MAIL_FROMNAME'); //发件人姓名
    $mail->AddAddress($to, "name");
    $mail->WordWrap = 50; //设置每行字符长度
    $mail->IsHTML(C('MAIL_ISHTML')); // 是否HTML格式邮件
    $mail->CharSet = C('MAIL_CHARSET'); //设置邮件编码
    $mail->Subject = $subject; //邮件主题
    $mail->Body = $content; //邮件内容
    $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //邮件正文不支持HTML的备用显示
    if (!$mail->Send()) {
        echo "Message could not be sent. <p>";
        echo "Mailer Error: " . $mail->ErrorInfo;
        exit();
    } else {
        return true;
    }
}

/**
 * 短信发送
 * @param $mobile 手机号
 * @param $tpl_id 短信模板
 * @param $tpl_value 短信内容（urlencode）
 * @return bool|string json格式
 */
function sendMessage($mobile, $tpl_id, $tpl_value)
{
    $key = 'a69a5764f670ccd1f473f21e1f4dad9c';//应用APPKEY
    $url = 'http://v.juhe.cn/sms/send?mobile=' . $mobile . '&tpl_id=' . $tpl_id . '&tpl_value=' . $tpl_value . '&key=' . $key;
    $ress = file_get_contents($url);
    return $ress;
}

/**
 * 上传图片到本地
 */
function upload_local()
{
    $config = array(
        'rootPath' => './Images',//根目录
        'savePath' => '/Upload/big/',//保存路径
        /*'saveName'=>$savename[0],//保存的文件名*/
    );
    $Upload = new \Think\Upload($config);
    $Aliyun = new Think\Upload\Driver\Aliyun ();
    $info = $Upload->upload($_FILES);
    if ($info) {
        foreach ($info as $key => $value) {
            $value['tmp_name'] = "./Images" . $value['savepath'] . $value['savename'];
            $save = $Aliyun->save($value);
        }

        //制作缩略图
        $image = new \Think\Image();
        $file_small = "./Images/Upload/small/";
        $file_center = "./Images/Upload/center/";
        /*保存小图*/
        foreach ($info as $key => $value) {

            $savename = explode('_', $value['savename']);
            $value['tmp_name'] = $file_small . date("Y-m-d", time()) . '/small_' . $savename[1];
            $file_path = "./Images/" . $value['savepath'] . $value['savename'];
            /*打开原图片*/
            $image->open($file_path);
            /*判读是否有文件夹*/
            if (!file_exists($file_small)) {
                mkdir($file_small);
            }
            if (!file_exists($file_small . date("Y-m-d", time()) . '/')) {
                mkdir($file_small . date("Y-m-d", time()) . '/');
            }
            $image->thumb(200, $image->height(), 1)->save($file_small . date("Y-m-d", time()) . '/small_' . $savename[1]);
            $save = $Aliyun->save($value);
        }
        /*保存中图*/
        foreach ($info as $key => $value) {
            $savename = explode('_', $value['savename']);
            $value['tmp_name'] = $file_center . date("Y-m-d", time()) . '/center_' . $savename[1];
            $file_path = "./Images/" . $value['savepath'] . $value['savename'];
            /*打开原图片*/
            $image->open($file_path);
            if (!file_exists($file_center)) {
                mkdir($file_center);
            }
            if (!file_exists($file_center . date("Y-m-d", time()) . '/')) {
                mkdir($file_center . date("Y-m-d", time()) . '/');
            }
            $image->thumb(500, $image->height(), 1)->save($file_center . date("Y-m-d", time()) . '/center_' . $savename[1]);
            $save = $Aliyun->save($value);
        }
        return $info;
    } else {
        return false;
    }
}

/**
 * @Description:生成订单号(可根据自身的业务需求更改)
 * @Return:
 * @Author: 潘玉婷 @panyuting
 * @Date: 2016/9/19 13:12
 * @Version 2.0
 */
function createOrderNo()
{
    $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    return $year_code[intval(date('Y')) - 2010] .
    strtoupper(dechex(date('m'))) . date('d') .
    substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
}

/**
 * 加密方法
 * @param $string
 * @param string $operation
 * @param string $key
 * @param int $expiry
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙   
    $ckey_length = 4;

    // 密匙   
    $key = md5($key ? $key : $GLOBALS['discuz_auth_key']);

    // 密匙a会参与加解密   
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证   
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文   
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙   
    $cryptkey = $keya . md5($keya . $keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)， 
    //解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确   
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿   
    for ($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度   
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分   
    for ($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符   
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if ($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式   
        if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
            return substr($result, 26);
        } else {
            return '';
        }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因   
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码   
        return $keyc . str_replace('=', '', base64_encode($result));
    }
}

/**
 * 生成随机邀请码方法
 */
function randomkeys()
{
    $pattern = '1234567890';
    $key = '';
    for ($i = 0; $i <= 5; $i++) {
        $key .= $pattern{mt_rand(0, 9)};    //生成php随机数
    }
    $res = M('user_4s')->where(array('my_num' => $key))->find();
    if ($res) {
        $this->randomkeys();
    } else {
        return $key;
    }
}

/**
 * 获得当前的域名
 * @return  string
 */
function get_domain()
{
    /* 协议 */
    $protocol = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? 'https://' : 'http://';

    /* 域名或IP地址 */
    if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
        $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
    } elseif (isset($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else {
        /* 端口 */
        if (isset($_SERVER['SERVER_PORT'])) {
            $port = ':' . $_SERVER['SERVER_PORT'];

            if ((':80' == $port && 'http://' == $protocol) || (':443' == $port && 'https://' == $protocol)) {
                $port = '';
            }
        } else {
            $port = '';
        }

        if (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'] . $port;
        } elseif (isset($_SERVER['SERVER_ADDR'])) {
            $host = $_SERVER['SERVER_ADDR'] . $port;
        }
    }

    return $protocol . $host;
}

/**
 * 获得网站的URL地址
 * @return  string
 */
function site_url()
{
    return get_domain() . substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
}

/**
 * 导出为word
 * @param $data
 * @param string $fileName
 * @return string
 */
function output_word($data, $fileName = '')
{

    if (empty($data)) return '';

    $data = '
        <html xmlns:v="urn:schemas-microsoft-com:vml"
        xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/TR/REC-html40">
        <head><meta http-equiv=Content-Type content="text/html;
        charset=utf-8">
        <meta name=ProgId content=Word.Document>
        <meta name=Generator content="Microsoft Word 11">
        <meta name=Originator content="Microsoft Word 11">
        <xml><w:WordDocument><w:View>Print</w:View></xml></head>
        <body>' . $data . '</body></html>';

    $filepath = tmpfile();
    $len = strlen($data);
    fwrite($filepath, $data);
    header("Content-type: application/octet-stream");
    header("Content-Disposition: attachment; filename={$fileName}.doc");
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . $fileName . '.doc');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . $len);
    rewind($filepath);
    echo fread($filepath, $len);
}

/**
 * 模拟post进行url请求
 * @param string $url
 * @param array $post_data
 */
function request_post($url = '', $post_data = array())
{
    if (empty($url) || empty($post_data)) {
        return false;
    }

    $o = "";
    foreach ($post_data as $k => $v) {
        $o .= "$k=" . urlencode($v) . "&";
    }
    $post_data = substr($o, 0, -1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}

?>