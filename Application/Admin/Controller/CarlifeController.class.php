<?php
namespace Admin\Controller;
use Think\Controller;
use Boris\ExportInspector;

/**
 * 车生活控制器
 */
class CarlifeController extends CommonController
{
    /**
     *车生活分类管理
     */
    public function carlife_type()
    {
        $node = M('car_life')->order('sort')->select();
        $count=M('car_life')->count();
        $this->node = A('Ajax')->Tree($node);

        $this->assign('count',$count);
        $this->display();

    }

    /**
     *车生活分类管理--添加
     */
    public function carlife_add()
    {
        $car=M('car_life');
        $node= $car->where(array('isdelete'=>0,'level'=>1))->select();
        $this->assign('node',$node);
        if (!empty($_POST)) {
            $_POST['id']=md5(microtime());
            $_POST['createuser']=session('admin_name');
            if ($_POST['pid']==0) {
                $_POST['level']=1;
            }else
            {
                $_POST['level']=2;

            }
            $car->create($_POST);
            if($car->add())
            {
                $this->success('添加成功！',U('carlife_type'));
            }else{
                $this->error('添加失败！');
            }

        }
        $this->display();
    }

    /**
     * 车知识列表页
     * @return [type] [description]
     */
    public function carknowledge()
    {
        //获得开始和结束时间
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1;
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $node= M('car_life')->where(array('isdelete'=>0,'pid'=>1))->select();
        $this->assign('node',$node);
        $this->display();
    }

    /**
     * 车知识添加页面
     * @return [type] [description]
     */
    public function carknowledge_add()
    {
        if(!empty($_FILES))
        {
            $y = upload_local();
            if($y){
                $_POST['image']= str_replace("big",'type',$y['image']['savepath'].$y['image']['savename']);
            }

        }

        if(!empty($_POST))
        {
            if(empty($_POST['title']))
            {
                $this->error('请上传标题！');
            }elseif(empty($_POST['contents']))
            {
                $this->error('请填写文章内容！');
            }elseif(empty($_POST['image']))
            {
                $this->error('请上传标题图！');
            }else
            {
                $_POST['id']=md5(microtime());
                $_POST['createuser']=session('admin_name');
                $_POST['summary']=substr(strip_tags($_POST['contents']),0,108).'......';
                $bbs=M('car_life_bbs');
                $bbs->create($_POST);
                if ($bbs->add())
                {
                    $this->success('添加成功！',U('carknowledge'));
                }else{
                    $this->error('添加失败！');
                }
            }

        }
        $node= M('car_life')->where(array('isdelete'=>0,'pid'=>1))->select();
        $this->assign('node',$node);
        $this->display();
    }

    /**
     *车知识修改页面
     */
    public function carknowledge_save()
    {
        $info=M('car_life_bbs')->where(array('id'=>I('id')))->find();
        $info['image']=OSS.str_replace('type','small',$info['image']);
        $this->assign('info',$info);
        $this->node= M('car_life')->where(array('isdelete'=>0,'pid'=>1))->select();
        if(!empty($_FILES))
        {
            $y = upload_local();
            if($y){
                $_POST['image']= str_replace("big",'type',$y['image']['savepath'].$y['image']['savename']);
            }
        }
        if(!empty($_POST))
        {
            $_POST['updateuser']=session('admin_name');
            $_POST['updatetime']=date('Y-m-d H:i:s',time());
            $bbs=M('car_life_bbs');
            $bbs->create($_POST);
            if ($bbs->save())
            {
                $this->success('修改成功！',U('carknowledge'));
            }else{
                $this->error('修改失败！');
            }
        }
        $this->display();
    }

    /**
     *车知识删除
     */
    public function carknowledge_delete()
    {
        $data['isdelete']=1;
        $data['updateuser']=session('admin_name');
        $data['updatetime']=date('Y-m-d H:i:s',time());
        $data['id']=I('id');
        if(M('car_life_bbs')->save($data))
        {
            $data['status']=1;
            $data['msg']='删除成功！';
        }else
        {
            $data['status']=2;
            $data['msg']='删除失败！';
        }
        $this->ajaxReturn($data);
    }

    /**
     *车知识不显示
     */
    public function carknowledge_display()
    {
        $data['display']=I('display');
        $data['updateuser']=session('admin_name');
        $data['updatetime']=date('Y-m-d H:i:s',time());
        $data['id']=I('id');
        $data['place_hot']=1;
        if(M('car_life_bbs')->save($data))
        {
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
     *车知识热门推荐
     */
    public function carknowledge_hot()
    {
        $data['place_hot']=I('place_hot');
        $data['updateuser']=session('admin_name');
        $data['updatetime']=date('Y-m-d H:i:s',time());
        $data['id']=I('id');
        if(M('car_life_bbs')->save($data))
        {
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
     * 车主秀列表页
     * @return [type] [description]
     */
    public function carshow()
    {
        //获得开始和结束时间
        $time = date('Y-m-d');
        $endtime = strtotime($time . '23:59:59');
        $starttime = $endtime - 3600 * 24 * 31 + 1;
        $this->assign('endtime', !empty($endtime) ? $endtime : null);
        $this->assign('starttime', !empty($starttime) ? $starttime : null);
        $node= M('car_life')->where(array('isdelete'=>0,'pid'=>2))->select();
        $this->assign('node',$node);
        $this->display();
    }

    /**
     *车主秀详情页
     */
    public function carshow_details()
    {
        $bbs=D('view_car_bbs');
        $info=$bbs->where(array('id'=>I('id')))->find();
        $info['image']=OSS.str_replace('type','small',$info['image']);
        $this->assign('info',$info);
        $this->display();
    }

    /**
     *车主秀帖子首页放置
     */
    public function carshow_index()
    {
        $data['place_index']=I('place_index');
        $data['updateuser']=session('admin_name');
        $data['updatetime']=date('Y-m-d H:i:s',time());
        $data['id']=I('id');
        if(M('car_life_bbs')->save($data))
        {
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
     *车主秀帖子审核
     */
    public function carshow_check()
    {
        $check_fail=I('check_fail');
        if (!empty($check_fail))
        {
            $data['check_fail']=$check_fail;
        }
        $data['check']=I('check');
        $data['place_index']=1;
        $data['updateuser']=session('admin_name');
        $data['updatetime']=date('Y-m-d H:i:s',time());
        $data['id']=I('id');
        if(M('car_life_bbs')->save($data))
        {
            if ($data['check']==2)
            {
                $bbs= M('car_life_bbs')->where(array('id'=>I('id'),'check'=>2,'isdelete'=>0))->find();
                if (!empty($bbs))
                {
                    $count=M()->query("select count(*) AS COUNT from gch_coin WHERE to_days(createtime) = to_days(now()) AND isdelete=0 AND info='发布1条动态且审核通过获得2车币' AND user_id='".$bbs['user_id']."'");
                    if ($count[0]['count']<5)
                    {
                        $datacoin['id']=md5(microtime());
                        $datacoin['user_id']=$bbs['user_id'];
                        $datacoin['role']=1;
                        $datacoin['coin']=2;
                        $datacoin['info']='发布1条动态且审核通过获得2车币';
                        M('coin')->add($datacoin);
                        M('user_general')->where(array('id'=>$bbs['user_id'],'isdelete'=>0))->setInc('total_coin',2);
                    }

                }
            }
            $data['status']=1;
            $data['msg']='操作成功！';
        }else
        {
            $data['status']=2;
            $data['msg']='操作失败！';
        }
        $this->ajaxReturn($data);
    }


}