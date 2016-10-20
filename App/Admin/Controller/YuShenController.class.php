<?php
namespace Admin\Controller;
class YuShenController extends AuthController {
    protected $onname='医生确诊';
    protected $rule_qz='yishenjz';
    public function quecheck(){
        $this->check_group($this->rule_qz."_add");
        $id=I('get.uuid');

        $map=array(
            'uuid'=>$id
        );

        $model   =   M('YuYue')->where($map)->find();

        $rule=get_wangixao_where(array('is_website'=>'1','type'=>'bingren'),'1','',$model['xf_id']);

        $this->rule=$rule;



        if($model) {
            $this->data =  $model;// 模板变量赋值
        }else{
            return $this->error(lang('数据错误','handle'));
        }

        $this->display();
    }
    public function index(){
        //权限选择
        $this->check_group($this->rule_qz);
        $model=M('XiaoFei');
        $map=array();
        if(IS_GET)
        {
            $map=I('get.');

        }
        $join[] = 'LEFT JOIN __LAN_MU__ l1 ON g1.xf_id = l1.id';

       
       
   
        $page=1;
        if(isset($_GET['p']))
        {
            $page=$_GET['p'];
        }
         $filed = '
            g1.uuid as guuid,
            g1.id as gid,
            l1.name,
            g1.prices,
            g1.clicks,
            g1.shows
           
         ';

        $count = $model->alias('g1')->join($join)->where($map)->count();// 查询满足要求的总记录数

        $pagesize=(C('PAGESIZE'))!=''?C('PAGESIZE'):'20';
        $list =  $model->alias('g1')->field($filed)->join($join)->order('g1.id desc')->page( $page.','.$pagesize)->select();
        
        $this->assign('list',$list);// 赋值数据集

        $this->assign('page',page( $count ,$map,$pagesize));// 赋值分页输出
       
        $this->display();
    }
    public function add(){
        //权限选择
        $this->check_group($this->rule_qz."_add");
        if(IS_POST)
        {

            $model=D('JieZhen');

            if($model->create())
            {
                M()->startTrans();
                $data=$model->create();
                $data['admin_id']=session('admin_id');
                //更新预约表
                $jztime=strtotime($data['jztime']);
                $ydata['status']=3;
                $ydata['ysz_id']=$data['ysz_id'];
                $ydata['jztime']=$data['jztime']= $jztime;//接诊时间
                $ydata['dz_id']=$data['zl_id'];
                $ydata['id']=$data['yy_id'];


                //插入，然后更新
                /*print_r($ydata);
                print_r($data);
                exit();*/
                $result =    $model->add($data);
                $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/kaidan",array('id'=>$result,'yid'=>$data['yy_id']));
                $yresult=M('YuYue')->data($ydata)->save();

                if($result && $yresult) {

                    M()->commit();
                    add_log($this->onname.'：'.$data['name'].'添加成功');
                    $msg=lang('添加成功','handle');

                    add_log($this->onname.'：'.$data['name'].'添加成功');

                    return $this->success('添加成功！',$backurl );
                    // echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');window.location='".$backurl."';</script>";
                }else{
                    M()->rollback();
                    add_log($this->onname.'：'.$data['name'].'添加失败','/Admin/add');
                    $msg=lang('添加失败','handle');
                    return $this->success('添加失败！',$backurl );
                    //$backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/index");
                    //echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');window.location='".$backurl."';</script>";
                }
            }else
            {
                $this->error($model->getError());
            }
        }else
        {

            $this->display();
        }

    }
    public function kaidan($id,$yid){
        $this->onname='开单';
        $this->assign('onname',$this->onname);

        $m=M('JieZhen')->where(array('checked'=>1))->find($id);
        
        $data=array(

            );
        $this->assign($data);
        $this->data=$m;
        $this->display();
    }
    //开单
    public function postKaiDan(){
        //更新预约状is_kd为1，开单时间kdtime
        //权限选择
        $thin->onname='开单';
        $this->check_group("kaidan_add");
        if(IS_POST){
            M()->startTrans();
            $model =D('KaiDan');

            if($data=$model->create()) {
                $data['admin_id']=session('admin_id');
               

                $ydata['is_kd']=1;
                $ydata['id']=$data['yuyue_id'];;

                $ydata['kdtime']=$data['kd_time']=strtotime($data['kd_time']);
                $result =    $model->add($data);
                
                $yresult=M('YuYue')->data($ydata)->save();

                $backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/kaidanList");
               
                if($result && $yresult) {

                    M()->commit();
                    add_log($this->onname.'：'.$data['name'].'添加成功');
                    $msg=lang('添加成功','handle');

                    add_log($this->onname.'：'.$data['name'].'添加成功');

                    return $this->success('添加成功！',$backurl );
                    // echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');window.location='".$backurl."';</script>";
                }else{
                    M()->rollback();
                    add_log($this->onname.'：'.$data['name'].'添加失败','/Admin/add');
                    $msg=lang('添加失败','handle');
                    return $this->success('添加失败！',$backurl );
                    //$backurl=U(MODULE_NAME."/".CONTROLLER_NAME."/index");
                    //echo "<script language='javascript'>var index = parent.layer.getFrameIndex(window.name); parent.layer.msg('".$msg."');window.location='".$backurl."';</script>";
                }

            }
        }
    }
    //消费项目
    public function xiaofei(){
  
         $this->display();
    }
    public function edit(){
        //权限选择

        $this->check_group($this->rule_qz."_edit");
        if(IS_POST)
        {
            $model =D('XiaoFei');

            if($model->create()) {
                $data=$model->create();
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
            
            $rule=get_wangixao_where(array('is_website'=>'1','type'=>'bingren'),'1','',$model['xf_id']);
           
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
    public function kaidanList(){
        $this->check_group('kaidan');
        $map=array();
        $model=M('KaiDan');
        $join[] = 'LEFT JOIN __USER__ u1 ON kd.user_id = u1.id';
        $join[] = 'LEFT JOIN __ADMIN__ a1 ON kd.admin_id = a1.id';

        $page=1;
        if(isset($_GET['p']))
        {
            $page=$_GET['p'];
        }
         $filed = '
            kd.id as id,kd.uuid as uuid,kd.kd_time as kd_time,kd.kd_name as kd_name,kd.is_liaoxiao as is_liaoxiao,kd.price_show,kd.price_total,
            u1.name as user_name
           
         ';

        $count = $model->alias('kd')->join($join)->where($map)->count();// 查询满足要求的总记录数

        $pagesize=(C('PAGESIZE'))!=''?C('PAGESIZE'):'20';
        $list =  $model->alias('kd')->field($filed)->join($join)->order('kd.id desc')->page( $page.','.$pagesize)->select();
         $menu_list= array(
          
            2=>'开单时间',
            3=>'姓名',
            4=>'消费项目',
            5=>'合计总价',
            6=>'是否疗程次数消费',
            7=>'咨询医生',
           
           
            );
        $this->menu_list=$menu_list;

        $this->assign('list',$list);// 赋值数据集
        

        $this->display();
    }
}