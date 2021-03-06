<?php
namespace Admin\Controller;
class XiaoFeiController extends AuthController {
    protected $onname='广告费';
    protected $rule_qz='days_price';
    public function index(){
        //权限选择
        $this->check_group($this->rule_qz);
        $model=M('XiaoFei');
        $map=array();
        $map=array();
        $stime=strtotime(I('get.stime'));
        $etime=strtotime(I('get.etime'));
        if($stime!=='' and $etime !='')
        {
            $timestr =  $stime. "," .$etime;
            $map['g1.cdate'] = array('between', $timestr);
        }
      

        $join[] = 'LEFT JOIN __LAN_MU__ l1 ON g1.xf_id = l1.id';
        $join[] = 'LEFT JOIN __ADMIN__ a1 ON g1.admin_id = a1.id';

       
       
   
        $page=1;
        if(isset($_GET['p']))
        {
            $page=$_GET['p'];
        }
         $filed = '
            a1.realname as admin_rname,
            a1.name as admin_name,
            g1.admin_id as admin_id,
            g1.uuid as guuid,
            g1.id as gid,
            l1.name,
            g1.prices,
            g1.clicks,
            g1.shows,
            g1.cdate as cdate
           
         ';

        $count = $model->alias('g1')->join($join)->where($map)->count();// 查询满足要求的总记录数

        $pagesize=(C('PAGESIZE'))!=''?C('PAGESIZE'):'20';
        $pagesize=I('get.pagesize')==''?$pagesize:I('get.pagesize');
        $list =  $model->alias('g1')->field($filed)->join($join)->where($map)->order('g1.cdate desc')->page( $page.','.$pagesize)->select();
        
        $this->assign('list',$list);// 赋值数据集

        $this->assign('page',page( $count ,$map,$pagesize));// 赋值分页输出
       
        $this->display();
    }
    public function add(){
        //权限选择
        $this->check_group('days_price_add');
        if(IS_POST)
        {

            $model=D('XiaoFei');
            $close=I('get.close');

            if($model->create())
            {
                $data=$model->create();
                $data['admin_id']=session('admin_id');
                $data['cdate']=strtotime($data['cdate']);
                $result =    $model->add($data);
                if($result) {
                    add_log($this->onname.'：'.$data['name'].'添加成功');
                    $msg=lang('添加成功','handle');
                    $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/add");
                    if($close!=1)
                    {
                        $this->success($msg,$backurl);
                        //echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); window.location='".$backurl."';</script>";
                    }else
                    {
                        $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/index");
                        echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.window.location='".$backurl."';</script>";
                    }
                   
                }else{
                    add_log($this->onname.'：'.$data['name'].'添加失败','/Admin/add');
                    $msg=lang('添加失败','handle');
                    $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/index");
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); </script>";
                }
            }else
            {
                $this->error($model->getError());
            }
        }else
        {
            $rule=get_wangixao_where(array('is_price'=>1,'type'=>'bingren'));

            $this->rule=$rule;
            $this->display();
        }

    }
    public function edit(){
        //权限选择

        $this->check_group($this->rule_qz."_edit");
        if(IS_POST)
        {
            $model =D('XiaoFei');

            if($model->create()) {
                $data=$model->create();
                $data['cdate']=strtotime($data['cdate']);
                $id=$data['id'];
                if(($data['pwd'])!='')
                {
                    $data['pwd']=sha1($data['pwd']);
                }else
                {
                    unset($data['pwd']);
                }

                $result =   $model->save($data);

                if($result) {
                    $msg=lang('更新成功','handle');
                     add_log($this->onname.'：'.$data['name'].'更新成功');
                    $msg=lang('更新成功','handle');
                    $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/index");
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."'); parent.location.reload();;parent.layer.close(index);</script>";
                    //return  $this->success(lang('更新成功','handle'),'/Admin/edit',$id);
                }else{

                    $msg=lang('数据一样无更新','handle');
                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');parent.layer.close(index);;parent.layer.close(index)</script>";
                }
            }else{
                return $this->error($model->getError());
            }
        }else{
            $id=I('get.uuid');
            $map=array(
            'uuid'=>$id
            );

            $model   =   M('XiaoFei')->where($map)->find();
            
            $rule=get_wangixao_where(array('type'=>'bingren','is_price'=>1),'1','',$model['xf_id']);

           
            $this->rule=$rule;


          
            if($model) {
                $this->data =  $model;// 模板变量赋值
            }else{
                return $this->error(lang('数据错误','handle'));
            }
            $this->display();
        }
    }
    public  function del(){
        //权限选择
        $this->check_group($this->rule_qz."_del");
        $id=I('get.id');
        $map=array(
            'uuid'=>$id
        );
        $model   =   D("XiaoFei");
        $data=$model->where($map)->find();
        $result=$model->where($map)->delete();
        if($result)
        {
            add_log($this->onname.'：'.$data['name'].'删除成功');
            return  $this->success(lang('删除成功','handle'));;
        }
        add_log($this->onname.'：'.$data['name'].'删除失败');
        return $this->error(lang('删除失败','handle'));;
    }
    public function handle($id){
        //权限选择
        $this->check_group($this->rule_qz."_edit");
        $model =M("XiaoFei");
        $type=I('get.type');
        if($type=='true')
        {
            $status=1;
        }else
        {
            $status=0;
        }
        $data['id'] =$id;
        $data['checked'] = $status;


        if($model->save($data)){

            return  $this->success(lang('更新成功','handle'));
            $msg=lang('更新成功','handle');
            $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/index");
            echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');parent.layer.close(index);window.location='".$backurl."';</script>";
        }else
        {
            $msg=lang('更新失败','handle');
            return  $this->success(lang('更新失败','handle'));
            $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/index ");
            echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');parent.layer.close(index);window.location='".$backurl."';</script>";
        }
    }
    public function updateWeb(){
        //权限选择
        $this->check_group('yuyue_edit');
        //网站更新信息
        $this->onname='预约修改来源';

        $base=I('get.webbase');
        if($base=='1')
        {
            $base=U('Admin/YuYue/index',array('webstie'=>'true','show'=>'none'));
        }elseif($base==2)
        {
            $base=U('Admin/YuYue/index',array('djstime'=>date('Y-m-d'),'djetime'=>date('Y-m-d'),'show'=>'none','websiteall'=>'true'));
        }
        //网站更新信息
        $backurl=$base;

     
        if(IS_POST)
        {
            $model =D('YuYue');
            $postdata=I('post.');
            if($model->create()) {
                $data=$model->create();
                /*$udata=D('User')->create();

                foreach ($udata as $key=>$uv)
                {
                    if($uv=='')
                    {
                         unset($udata[$key]);
                    }

                }
                //如果为空，则返回null
                unset($udata['id']);
                $udate['id']=$data['user_id'];
                if(count($udate)>1)
                {
                    M("User")->save($user);
                }*/
                foreach ($data as $k=>$v)
                {
                    if($v=='')
                    {
                        unset($data[$k]);
                    }
                }
              

                $result =   $model->save($data);

                if($result) {
                    add_log($this->onname.'：'.$data['name'].'更新成功');
                    $msg=lang('更新成功','handle');

                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."'); parent.window.location='".$backurl."';</script>";

                    //return  $this->success($msg);
                }else{
                    $msg=lang('数据一样无更新','handle');
                    //return  $this->success(lang('无更新','handle'));

                    echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."'); parent.window.location='".$backurl."';</script>";

                }
            }else{
                return $this->error($model->getError());
            }
        }else{
            $id=I('get.uuid');
            //自己查看自己的

            $map=array(
                'uuid'=>$id
            );

            $model   = D('YuYue')->relation(true)->where($map)->find();

            if($model) {
                $this->data =  $model;// 模板变量赋值
            }else{
                return $this->error(lang('数据错误','handle'));
            }

            return $this->display('YuYue:webedit');


        }
    }
}