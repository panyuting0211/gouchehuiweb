<?php
/**
 *购车指南
 **/
namespace Home\Controller;
use Think\Controller;

class GuideController extends CommonController {

    /**
     *空控制器
     */
/*    public function _empty()
    {
        $this->redirect('process');
    }*/

    public function process()
    {
        $this->display();
    }


    /**
     *购车指南---在线反馈
     */
    public function feedback()
    {   

        if(!empty($_POST))
        {
            $_POST['id']=md5(microtime());
            $_POST['createuser']=session('user_id');
            M("online_feedback") -> create($_POST);
            if(M("online_feedback")->add())
            {
                $this->success("信息反馈成功！",U("feedback"));
            }else
            {
                $this->error("信息反馈失败！",U("feedback"));
            }    

        }else{
            $this->display();
        }

    }

    /**
     *生成验证码
     */
    public function verifyImg(){
        import('ORG.Util.Image');
        ob_end_clean();
        $config=array(
            'length'    =>  5,               // 验证码位数
            'fontttf'   =>  '4.ttf',        // 验证码字体，不设置随机获取
            'fontSize'  =>  43
            );
        $verify = new \Think\Verify($config);
        $verify ->entry();
    }

}