<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Class IndexController前台首页控制器
 * @package Home\Controller
 */
class CarlifeController extends CommonController {

    /**
     *空控制器
     */
    public function _empty()
    {
        $this->redirect('carknowledge');
    }
    /******************************************************车知识******************************************************************************/
    /**
     *车知识列表页
     */
    public function carknowledge()
    {
        $bbs=D('view_car_bbs');
        $hot_all=$bbs->where(array('isdelete'=>0,'place_hot'=>2,'display'=>2,'pid'=>1))->order('createtime desc')->limit(0,4)->select();
        foreach ($hot_all as $key => $value)
        {
            $hot_all[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $hot_all_list=$bbs->where(array('isdelete'=>0,'display'=>2,'pid'=>1))->order('createtime desc')->limit(0,15)->select();
        $hot_all_click=$bbs->where(array('isdelete'=>0,'display'=>2,'pid'=>1))->order('click_amount desc')->limit(0,15)->select();
        $this->assign('hot_all',$hot_all);
        $this->assign('hot_all_list',$hot_all_list);
        $this->assign('hot_all_click',$hot_all_click);
        //车美容
        $hot_buty=$bbs->where(array('isdelete'=>0,'place_hot'=>2,'display'=>2,'type_id'=>3))->order('createtime desc')->limit(0,4)->select();
        foreach ($hot_buty as $key => $value)
        {
            $hot_buty[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $hot_buty_list=$bbs->where(array('isdelete'=>0,'display'=>2,'type_id'=>3))->order('createtime desc')->limit(0,15)->select();
        $hot_buty_click=$bbs->where(array('isdelete'=>0,'display'=>2,'type_id'=>3))->order('click_amount desc')->limit(0,15)->select();
        $this->assign('hot_buty',$hot_buty);
        $this->assign('hot_buty_list',$hot_buty_list);
        $this->assign('hot_buty_click',$hot_buty_click);
        //车装饰
        $decoration=$bbs->where(array('isdelete'=>0,'place_hot'=>2,'display'=>2,'type_id'=>4))->order('createtime desc')->limit(0,4)->select();
        foreach ($decoration as $key => $value)
        {
            $decoration[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $decoration_list=$bbs->where(array('isdelete'=>0,'display'=>2,'type_id'=>4))->order('createtime desc')->limit(0,15)->select();
        $decoration_click=$bbs->where(array('isdelete'=>0,'display'=>2,'type_id'=>4))->order('click_amount desc')->limit(0,15)->select();
        $this->assign('decoration',$decoration);
        $this->assign('decoration_list',$decoration_list);
        $this->assign('decoration_click',$decoration_click);

        //车维修
        $repair=$bbs->where(array('isdelete'=>0,'place_hot'=>2,'display'=>2,'type_id'=>8))->order('createtime desc')->limit(0,4)->select();
        foreach ($repair as $key => $value)
        {
            $repair[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $repair_list=$bbs->where(array('isdelete'=>0,'display'=>2,'type_id'=>8))->order('createtime desc')->limit(0,15)->select();
        $repair_click=$bbs->where(array('isdelete'=>0,'display'=>2,'type_id'=>8))->order('click_amount desc')->limit(0,15)->select();
        $this->assign('repair',$repair);
        $this->assign('repair_list',$repair_list);
        $this->assign('repair_click',$repair_click);

        $this->display();
    }

    /**
     *帖子详情
     */
    public function carlife_details()
    {
        $bbs=D('view_car_bbs');
        $info=$bbs->where(array('id'=>I('id')))->find();
        //点击量+1
        M('car_life_bbs')->where(array('id'=>I('id')))->setInc('click_amount',1);
        $z=M('car_life_thumbs')->where(array('bbs_id'=>I('id'),'user_id'=>session('user_id'),'isdelete'=>0))->find();
        if($z)
        {
            $info['thumbs_status']=1;
        }else{
            $info['thumbs_status']=2;
        }
        $hot_all_click=$bbs->where(array('isdelete'=>0,'display'=>2,'pid'=>1))->order('click_amount desc')->limit(0,10)->select();
        $this->assign('hot_all_click',$hot_all_click);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     *帖子评论
     */
    public function post_add()
    {
        $comment=M('car_life_comment');
        $_POST['user_id']=session('user_id');
        $comment->create($_POST);
        if($comment->add())
        {
            if ($_POST['rid']==0)
            {
                $bbs= M('car_life_bbs')->where(array('id'=>I('bbs_id'),'check'=>2,'isdelete'=>0))->find();
                if (!empty($bbs))
                {
                    $count=M()->query("select count(*) AS COUNT from gch_coin WHERE to_days(createtime) = to_days(now()) AND isdelete=0 AND info='动态被回复，获得1车币' AND user_id='".$bbs['user_id']."'");
                    if ($count[0]['count']<10)
                    {
                        $datacoin['id']=md5(microtime());
                        $datacoin['user_id']=$bbs['user_id'];
                        $datacoin['role']=1;
                        $datacoin['coin']=1;
                        $datacoin['info']='动态被回复，获得1车币';
                        M('coin')->add($datacoin);
                        M('user_general')->where(array('id'=>$bbs['user_id'],'isdelete'=>0))->setInc('total_coin',1);
                    }

                }

            }

            //返回参数
            $puser_id=$comment->where(array('id'=>I('pid')))->getField('user_id');
            $pnick=M('user_general')->where(array('id'=>$puser_id))->getField('nick');
            if(empty($pnick))
            {
                $pnick='购车惠会员';
            }
            $nick=M('user_general')->where(array('id'=>session('user_id')))->getField('nick');
            if(empty($nick))
            {
                $nick='购车惠会员';
            }
            $data['pnick']=$pnick;
            $data['nick']=$nick;
            $data['status']=1;
            $data['msg']='操作成功！';
        }else
        {
            $data['status']=2;
            $data['msg']='操作失败！';
        }
        $this->ajaxReturn($data);
    }

    /**
     *帖子列表
     */
    public function post_list()
    {
        $comment=M('car_life_comment');
        $pagenum=I('pageNum');
        $count=$comment->where(array('bbs_id'=>I('bbs_id'),'pid'=>0))->count();
        $Page = new \Think\PageAjax($count, $pagenum);
        $info=$comment->where(array('bbs_id'=>I('bbs_id'),'pid'=>0))->order('createtime desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        foreach ($info as $key => $value)
        {
            $info[$key]['createtime']=date('m-d H:i',strtotime($value['createtime']));

            $nick=M('user_general')->where(array('id'=>$value['user_id']))->getField('nick');
            if (!empty($nick))
            {
                $info[$key]['nick']=$nick;
            }else
            {
                $info[$key]['nick']='购车惠会员';
            }
            $head_url=M('user_general')->where(array('id'=>$value['user_id']))->getField('head_url');
            $thumbs_status= M('car_life_thumbs')->where(array('comment_id'=>$value['id'],'user_id'=>session('user_id'),'isdelete'=>0))->find();
            if(!empty($thumbs_status))
            {
                //该评论点赞了
                $info[$key]['thumbs_status']=1;
            }else
            {
                //该评论未点赞
                $info[$key]['thumbs_status']=2;
            }
            if (!empty($head_url))
            {
                $info[$key]['head_url']=OSS.str_replace('type','small',$head_url);
            }else
            {
                $info[$key]['head_url']='/Public/Home/images/headimg.png';
            }
            $reply=$comment->where(array('rid'=>$value['id']))->order('pid asc')->select();
            foreach ($reply as $k=>$v)
            {
                $reply[$k]['createtime']=date('m-d H:i',strtotime($v['createtime']));

                $nickr=M('user_general')->where(array('id'=>$v['user_id']))->getField('nick');
                if (!empty($nickr))
                {
                    $reply[$k]['nick']=$nickr;
                }else{
                    $reply[$k]['nick']='购车惠会员';
                }
                $head_url=M('user_general')->where(array('id'=>$v['user_id']))->getField('head_url');
                if (!empty($head_url))
                {
                    $reply[$k]['head_url']=OSS.str_replace('type','small',$head_url);
                }else
                {
                    $reply[$k]['head_url']='/Public/Home/images/headimg.png';
                }
                $thumbs_reply_status= M('car_life_thumbs')->where(array('comment_id'=>$v['id'],'user_id'=>session('user_id'),'isdelete'=>0))->find();
                if(!empty($thumbs_reply_status))
                {
                    //该回复点赞了
                    $reply[$k]['thumbs_reply_status']=1;
                }else
                {
                    //该回复未点赞
                    $reply[$k]['thumbs_reply_status']=2;
                }
                //上一条帖子信息
                $last_reply=$comment->where(array('id'=>$v['pid']))->find();

                $pnick=M('user_general')->where(array('id'=>$last_reply['user_id']))->getField('nick');
                if (!empty($pnick))
                {
                    $reply[$k]['pnick']=$pnick;
                }else
                {
                    $reply[$k]['pnick']='购车惠会员';
                }
                $phead_url=M('user_general')->where(array('id'=>$last_reply['user_id']))->getField('head_url');
                if (!empty($phead_url))
                {
                    $reply[$k]['phead_url']=OSS.str_replace('type','small',$phead_url);
                }else
                {
                    $reply[$k]['phead_url']='/Public/Home/images/headimg.png';
                }
            }
            $info[$key]['reply']=$reply;
        }
        $data['list'] = $info;
        $data['count']= $count;
        $data['pagelist'] = $Page->show();
        $this->ajaxReturn($data);
    }

    /**
     *点赞和取消赞
     */
    public function thumbs_comment()
    {
        $thumbs=M('car_life_thumbs');
        if(isset($_SESSION['user_id']) and $_SESSION['user_id'] != ''){
            $data1['comment_id']=I('comment_id');
            $data1['user_id']=session('user_id');
            $data1['isdelete']=0;
            $info=$thumbs->where($data1)->find();
            if ($info)
            {
                $_POST['updatetime']=date('Y-m-d H:i:s',time());
                $_POST['updateuser']=session('user_name');
                $_POST['isdelete']=1;
                $thumbs->create($_POST);
                $z=$thumbs->where(array('user_id'=>session('user_id'),'comment_id'=>I('comment_id')))->save();
                if($z)
                {
                    $data['status']=1;
                    $data['str'] = '赞';
                    $data['msg']='取消赞成功';
                }else
                {
                    $data['status']=2;
                    $data['msg']='取消赞失败';
                }
            }else
            {
                $_POST['user_id']=session('user_id');
                $_POST['createuser']=session('user_name');
                $thumbs->create($_POST);
                if($thumbs->add())
                {
                    $data['status']=1;
                    $data['str'] = '已赞';
                    $data['msg']='赞成功';
                }else
                {
                    $data['status']=2;
                    $data['msg']='赞失败';
                }
            }

        }else
        {
            $data['status'] = 2;//未登录
            $data['msg'] = '未登录';//未登录
        }
        $this->ajaxReturn($data);

    }

    /**
     *点赞和取消赞
     */
    public function thumbs_bbs()
    {
        $thumbs=M('car_life_thumbs');
        if(isset($_SESSION['user_id']) and $_SESSION['user_id'] != ''){
            $data1['bbs_id']=I('bbs_id');
            $data1['user_id']=session('user_id');
            $data1['isdelete']=0;
            $info=$thumbs->where($data1)->find();
            if ($info)
            {
                $_POST['updatetime']=date('Y-m-d H:i:s',time());
                $_POST['updateuser']=session('user_name');
                $_POST['isdelete']=1;
                $thumbs->create($_POST);
                $z=$thumbs->where(array('user_id'=>session('user_id'),'bbs_id'=>I('bbs_id')))->save();
                if($z)
                {
                    $data['status']=1;
                    $data['str'] = '赞';
                    $data['msg']='取消赞成功';
                }else
                {
                    $data['status']=2;
                    $data['msg']='取消赞失败';
                }
            }else
            {
                $_POST['user_id']=session('user_id');
                $_POST['createuser']=session('user_name');
                $thumbs->create($_POST);
                if($thumbs->add())
                {
                   $bbs= M('car_life_bbs')->where(array('id'=>I('bbs_id'),'check'=>2,'isdelete'=>0))->find();
                    if (!empty($bbs))
                    {
                        $count=M()->query("select count(*) AS COUNT from gch_coin WHERE to_days(createtime) = to_days(now()) AND isdelete=0 AND info='动态被点赞，获得1车币' AND user_id='".$bbs['user_id']."'");
                        if ($count[0]['count']<10)
                        {
                        $datacoin['id']=md5(microtime());
                        $datacoin['user_id']=$bbs['user_id'];
                        $datacoin['role']=1;
                        $datacoin['coin']=1;
                        $datacoin['info']='动态被点赞，获得1车币';
                        M('coin')->add($datacoin);
                        M('user_general')->where(array('id'=>$bbs['user_id'],'isdelete'=>0))->setInc('total_coin',1);
                        }

                    }
                    $data['status']=1;
                    $data['str'] = '已赞';
                    $data['msg']='赞成功';
                }else
                {
                    $data['status']=2;
                    $data['msg']='赞失败';
                }
            }

        }else
        {
            $data['status'] = 2;//未登录
            $data['msg'] = '未登录';//未登录
        }
        $this->ajaxReturn($data);

    }

    /******************************************************车主秀******************************************************************************/

    /**
     * 车主秀展示页
     */
    public function carshow()
    {
        $bbs=D('view_car_bbs');
        $hot_all1=$bbs->where(array('isdelete'=>0,'check'=>2,'pid'=>2))->order('createtime desc')->limit(0,4)->select();
        foreach ($hot_all1 as $key => $value)
        {
            $hot_all1[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('hot_all1',$hot_all1);
        $hot_all2=$bbs->where(array('isdelete'=>0,'check'=>2,'pid'=>2))->order('createtime desc')->limit(4,4)->select();
        foreach ($hot_all2 as $key => $value)
        {
            $hot_all2[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('hot_all2',$hot_all2);
        $hot_all3=$bbs->where(array('isdelete'=>0,'check'=>2,'pid'=>2))->order('createtime desc')->limit(8,4)->select();
        foreach ($hot_all3 as $key => $value)
        {
            $hot_all3[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('hot_all3',$hot_all3);
        $hot_all4=$bbs->where(array('isdelete'=>0,'check'=>2,'pid'=>2))->order('createtime desc')->limit(12,4)->select();
        foreach ($hot_all4 as $key => $value)
        {
            $hot_all4[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('hot_all4',$hot_all4);
        //新车秀
        $newshow1=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>5))->order('createtime desc')->limit(0,4)->select();
        foreach ($newshow1 as $key => $value)
        {
            $newshow1[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('newshow1',$newshow1);
        $newshow2=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>5))->order('createtime desc')->limit(4,4)->select();
        foreach ($newshow2 as $key => $value)
        {
            $newshow2[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('newshow2',$newshow2);
        $newshow3=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>5))->order('createtime desc')->limit(8,4)->select();
        foreach ($newshow3 as $key => $value)
        {
            $newshow3[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('newshow3',$newshow3);
        $newshow4=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>5))->order('createtime desc')->limit(12,4)->select();
        foreach ($newshow4 as $key => $value)
        {
            $newshow4[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('newshow4',$newshow4);
        //装饰秀
        $decoration1=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>6))->order('createtime desc')->limit(0,4)->select();
        foreach ($decoration1 as $key => $value)
        {
            $decoration1[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('decoration1',$decoration1);
        $decoration2=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>6))->order('createtime desc')->limit(4,4)->select();
        foreach ($decoration2 as $key => $value)
        {
            $decoration2[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('decoration2',$decoration2);
        $decoration3=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>6))->order('createtime desc')->limit(8,4)->select();
        foreach ($decoration3 as $key => $value)
        {
            $decoration3[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('decoration3',$decoration3);
        $decoration4=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>6))->order('createtime desc')->limit(12,4)->select();
        foreach ($decoration4 as $key => $value)
        {
            $decoration4[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('decoration4',$decoration4);
        //改装秀
        $refit1=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>9))->order('createtime desc')->limit(0,4)->select();
        foreach ($refit1 as $key => $value)
        {
            $refit1[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('refit1',$refit1);
        $refit2=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>9))->order('createtime desc')->limit(4,4)->select();
        foreach ($refit2 as $key => $value)
        {
            $refit2[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('refit2',$refit2);
        $refit3=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>9))->order('createtime desc')->limit(8,4)->select();
        foreach ($refit3 as $key => $value)
        {
            $refit3[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('refit3',$refit3);
        $refit4=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>9))->order('createtime desc')->limit(12,4)->select();
        foreach ($refit4 as $key => $value)
        {
            $refit4[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('refit4',$refit4);
        //自驾游
        $travel1=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>7))->order('createtime desc')->limit(0,4)->select();
        foreach ($travel1 as $key => $value)
        {
            $travel1[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('travel1', $travel1);
        $travel2=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>7))->order('createtime desc')->limit(4,4)->select();
        foreach ($travel2 as $key => $value)
        {
            $travel2[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('travel2',$travel2);
        $travel3=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>7))->order('createtime desc')->limit(8,4)->select();
        foreach ($travel3 as $key => $value)
        {
            $travel3[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('travel3',$travel3);
        $travel4=$bbs->where(array('isdelete'=>0,'check'=>2,'type_id'=>7))->order('createtime desc')->limit(12,4)->select();
        foreach ($travel4 as $key => $value)
        {
            $travel4[$key]['image']=OSS.str_replace('type','center',$value['image']);
        }
        $this->assign('travel4',$travel4);
        $this->display();
    }

    /**
     *帖子图片提取生成缩略图
     */
    public function thumbimg()
    {
        $bbs=D('view_car_bbs');
        $image = new \Think\Image();
        $info=$bbs->where(array('isdelete'=>0))->select();
        foreach ($info as $k => $v)
        {
            preg_match_all('#<img.*?src="([^"]*)"[^>]*>#i',$v['contents'],$match);
            foreach ($match[1] as $key =>$value)
            {
                show_bug($value);
                /* $file='./Images/ueditor/thumb/'.date("Y-m-d",time()).'/';
                 if(!file_exists($file)){
                     mkdir($file);
                 }
                 $name=explode('/',$value);
                 $image->open('.'.$value);
                 $image->thumb(150, 150)->save($file.$name[4]);
                 $sfile='/Images/ueditor/thumb/'.date("Y-m-d",time()).'/';
                 $img[]=$sfile.$name[4];*/
            }
        }


    }

    public function go()
    {
        $bbs='f04c3f5cfac11c1c2780bddd472f1daa';
        $info=M()->query("select count(*) AS COUNT from gch_coin WHERE to_days(createtime) = to_days(now()) AND isdelete=0 AND info='连续7天以下登录加1车币' AND user_id='".$bbs."'");
        if ($info[0]['count']==1)
        {
            show_bug($info[0]);
        }

    }


}