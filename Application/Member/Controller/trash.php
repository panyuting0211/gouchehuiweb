<?php
/**
 *账号信息处理
 */
function accountHandle(){
//判断是否有数据提交
    if(IS_POST){
        $member=M("user");
        $user_name=I('post.user_name');
//判断用户名
        if($user_name){
            $info=$member->where(array('user_name'=>$user_name,'isdelete'=>0))->find();
            if($info){
//判读用户是否为手机注册的用户
                if($this->isMobile($user_name)){
                    $res = $this->noteVerify($user_name);//调用短信发送接口
                    $resu = json_decode($res,ture);
                    if($resu['status'] == 0){
                        $this->redirect('check',array('uname' =>$user_name));
                    }elseif($resu['status'] == 205405){
                        $this->error('同一号码发送次数过于频繁');
                    }else{
                        $this->error('短信发送失败,请联系管理员');
                    }
                }else{
//判读用户是否为邮箱注册的用户
                    if($this->isEmail($user_name) == ''){
//如果既不是手机也不是邮箱注册的用户判读该用户中的邮箱是否有填写
                        if($info['email']){
                            $email = $info['email'];//邮箱
                        }else{
//没有填写去个人中心完善邮箱
                            $this->error('请先完善个人邮箱',U('Members/index'));
                        }
                    }else{
                        $emial = $user_name;//邮箱
                        $token = md5($info['id'].$info['user_name'].$info['password']);
                        $subject="【南京易橙汇】找回密码";//邮件主题
                        $url = "http://".$_SERVER['HTTP_HOST'];//获得域名
                        $content="您好，".$user_name." ：您于".date('Y-m-d H:i',time())."找回密码，请于10分钟之类验证您的链接
请点击下面的链接来重置您的密码。".$url.U('public/checkout',array('user_name'=>$user_name,'token'=>$token))."
如果您的邮箱不支持链接点击，请将以上链接地址拷贝到你的浏览器地址栏中。";//邮件内容
                        $this->assign('uname',$user_name);
//sendMail($emial, $subject, $content);
//发送邮箱
                        if(sendMail($emial, $subject, $content)){
//邮箱链接有效时间为10分钟
                            setcookie ( 'passtime',time()+600, time ()+600, "/" );
                            $this->success('邮箱发送成功,请于登录您的邮箱进行链接验证','',3);
                        }else{
                            $this->error('邮箱发送失败');
                        }
                    }
                }
            }else{
                $this->error('账号不存在');
            }
        }else{
            $this->error('请输入账号');
        }
    }
}



/**
 *找回密码-手机验证码输入
 */
function check(){
    //获得用户名
    $this->assign('user_name',I('uname'));
    $this->display('findpw_verification');
}

/**
 *找回密码-邮箱验证
 */
function checkout(){
    $token = I('token');
    $user_name = I('user_name');
    $res = M('user')->where(array('user_name'=>$user_name))->find();
    $token_code = md5($res['id'].$res['user_name'].$res['password']);
    //验证token
    if($token == $token_code){
        //验证链接有效时间
        if(isset($_COOKIE['passtime']) && $_COOKIE['passtime'] >=time()){
            session('uname',$user_name);
            $this->redirect('findpw_resetPw');
        }else{
            $this->error('链接失效,请重新发送邮件',U('findpw_account'));
        }
    }else{
        $this->error('验证不对，请重新验证身份信息或者联系管理员',U('findpw_account'));
    }
}

?>